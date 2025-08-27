<?php
require_once '../../config/init.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Get user info before logout for logging
    $userEmail = $_SESSION['user_email'] ?? 'Unknown';

    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    $response['success'] = true;
    $response['message'] = 'Logged out successfully';

    // Log successful logout
    error_log("User {$userEmail} logged out successfully");
} catch (Exception $e) {
    $response['message'] = 'Error during logout';
    error_log("Logout error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error during logout: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
