<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";

// Set JSON content type
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$userModel = new User();

// Get user ID from request
$userId = $_GET['id'] ?? null;

if (!$userId) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

try {
    // Get user details
    $user = $userModel->getUserById($userId);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    // Return user data
    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} catch (Exception $e) {
    error_log("Error getting user details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving user details'
    ]);
}
