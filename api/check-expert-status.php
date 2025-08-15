<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

$auth = new AuthMiddleware();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$currentUser = $auth->getCurrentUser();

// Check if user is an expert
if ($currentUser['role'] !== 'expert') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
    
    // Get current expert status
    $stmt = $db->prepare("SELECT expert_authorization FROM users WHERE id = ?");
    $stmt->execute([$currentUser['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $authorized = ($result['expert_authorization'] == 1);
        
        echo json_encode([
            'success' => true,
            'authorized' => $authorized,
            'status' => $result['expert_authorization']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'User not found'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error checking expert status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}
?>
