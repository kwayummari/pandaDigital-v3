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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST; // Fallback to POST data
    }
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'password', 'confirm_password'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
            ]);
            exit();
        }
    }
    
    // Sanitize input
    $data = [
        'first_name' => filter_var(trim($input['first_name']), FILTER_SANITIZE_STRING),
        'last_name' => filter_var(trim($input['last_name']), FILTER_SANITIZE_STRING),
        'email' => filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL),
        'phone' => filter_var(trim($input['phone']), FILTER_SANITIZE_STRING),
        'password' => trim($input['password']),
        'confirm_password' => trim($input['confirm_password']),
        'role' => $input['role'] ?? 'user',
        'region' => $input['region'] ?? null,
        'business' => $input['business'] ?? null
    ];
    
    // Initialize auth service
    $authService = new AuthService();
    
    // Attempt registration
    $result = $authService->registerUser($data);
    
    if ($result['valid']) {
        // Registration successful
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'user_id' => $result['user_id'],
            'redirect_url' => '/dashboard.php' // or wherever you want to redirect after registration
        ]);
    } else {
        // Registration failed
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'],
            'field' => $result['field'] ?? 'general'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during registration. Please try again.'
    ]);
}
