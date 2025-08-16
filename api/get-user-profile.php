<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../services/AuthService.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
$authService = new AuthService();
if (!$authService->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$currentUser = $authService->getCurrentUser();

try {
    // Get fresh user data from database
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->prepare("
        SELECT id, email, first_name, last_name, phone, region, gender, date_of_birth, role, status, expert_authorization
        FROM users 
        WHERE id = ?
    ");

    $stmt->execute([$currentUser['id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        echo json_encode([
            'success' => true,
            'user' => $userData
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Get user profile error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mfumo, jaribu tena.'
    ]);
}
