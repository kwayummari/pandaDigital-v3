<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'user' => null
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get POST data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Initialize database
    $database = Database::getInstance();
    $conn = $database->getConnection();

    // Check if user exists
    $query = "SELECT id, email, pass, first_name, last_name, phone, region, business, isSeller, status, is_active 
              FROM users WHERE email = ? AND is_active = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('Invalid email or password');
    }

    // Verify password
    if (!password_verify($password, $user['pass'])) {
        throw new Exception('Invalid email or password');
    }

    // Check if user is active
    if ($user['is_active'] != 1) {
        throw new Exception('Account is deactivated. Please contact support.');
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_region'] = $user['region'];
    $_SESSION['user_business'] = $user['business'];
    $_SESSION['is_seller'] = $user['isSeller'];
    $_SESSION['user_status'] = $user['status'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Update last login
    $updateQuery = "UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([$user['id']]);

    // Prepare user data for response (exclude sensitive information)
    $userData = [
        'id' => $user['id'],
        'email' => $user['email'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'phone' => $user['phone'],
        'region' => $user['region'],
        'business' => $user['business'],
        'is_seller' => $user['isSeller'],
        'status' => $user['status']
    ];

    $response['success'] = true;
    $response['message'] = 'Login successful';
    $response['user'] = $userData;

    // Log successful login
    error_log("User {$user['email']} logged in successfully");
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Login error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error during login: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
