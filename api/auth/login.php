<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Basic error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . "/../../services/AuthService.php";
    error_log("AuthService.php loaded successfully");
} catch (Exception $e) {
    error_log("Failed to load AuthService: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Service not available',
        'error' => $e->getMessage()
    ]);
    exit();
}

// Basic API test
error_log("Login API endpoint accessed");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
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

    // Debug logging
    error_log("Login attempt for email: " . ($input['email'] ?? 'not provided'));

    // Validate required fields
    if (empty($input['email']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required.'
        ]);
        exit();
    }

    // Sanitize input
    $email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($input['password']);

    // Initialize auth service
    error_log("Initializing AuthService...");
    $authService = new AuthService();
    error_log("AuthService initialized successfully");

    // Attempt login
    error_log("Attempting login for user: " . $email);
    $result = $authService->loginUser($email, $password);
    error_log("Login result: " . json_encode($result));

    if ($result['valid']) {
        // Login successful
        // Use the AuthService's role-based redirect
        $redirectUrl = $authService->getRoleBasedRedirect($result['user']['role']);

        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'user' => $result['user'],
            'redirect_url' => $redirectUrl
        ]);
    } else {
        // Login failed
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $result['message'],
            'field' => $result['field'] ?? 'general'
        ]);
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    error_log("Login error trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during login. Please try again.',
        'debug' => Environment::isDebug() ? $e->getMessage() : null
    ]);
}
