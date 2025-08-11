<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . "/../../services/AuthService.php";

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

try {
    // Initialize auth service
    $authService = new AuthService();
    
    // Perform logout
    $result = $authService->logoutUser();
    
    if ($result['valid']) {
        // Logout successful
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'redirect_url' => '/login.php'
        ]);
    } else {
        // Logout failed
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Logout failed. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during logout. Please try again.'
    ]);
}
