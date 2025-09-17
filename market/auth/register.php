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
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $business = trim($_POST['business'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $isSeller = (int)($_POST['is_seller'] ?? 0);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password)) {
        throw new Exception('All required fields must be filled');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate password
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    // Validate phone number (remove any non-numeric characters)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 9) {
        throw new Exception('Invalid phone number');
    }

    // Initialize database
    $database = Database::getInstance();
    $conn = $database->getConnection();

    // Check if email already exists
    $emailCheckQuery = "SELECT id FROM users WHERE email = ?";
    $emailStmt = $conn->prepare($emailCheckQuery);
    $emailStmt->execute([$email]);

    if ($emailStmt->fetch()) {
        throw new Exception('Email already exists');
    }

    // Check if phone already exists
    $phoneCheckQuery = "SELECT id FROM users WHERE phone = ?";
    $phoneStmt = $conn->prepare($phoneCheckQuery);
    $phoneStmt->execute([$phone]);

    if ($phoneStmt->fetch()) {
        throw new Exception('Phone number already exists');
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate username from email
    $username = explode('@', $email)[0];

    // Insert new user
    $insertQuery = "INSERT INTO users (first_name, last_name, email, phone, region, business, gender, date_of_birth, 
                                      pass, username, isSeller, role, status, is_active, email_verified, date_created) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'user', 'free', 1, 0, NOW())";

    $insertStmt = $conn->prepare($insertQuery);
    $result = $insertStmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone,
        $region,
        $business,
        $gender,
        $date_of_birth,
        $hashedPassword,
        $username,
        $isSeller
    ]);

    if (!$result) {
        throw new Exception('Failed to create account');
    }

    $userId = $conn->lastInsertId();

    // Set session variables for automatic login
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
    $_SESSION['user_phone'] = $phone;
    $_SESSION['user_region'] = $region;
    $_SESSION['user_business'] = $business;
    $_SESSION['is_seller'] = $isSeller;
    $_SESSION['user_status'] = 'free';
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Prepare user data for response
    $userData = [
        'id' => $userId,
        'email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'phone' => $phone,
        'region' => $region,
        'business' => $business,
        'is_seller' => $isSeller,
        'status' => 'free'
    ];

    $response['success'] = true;
    $response['message'] = 'Account created successfully';
    $response['user'] = $userData;

    // Log successful registration
    error_log("New user registered: {$email}");
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Registration error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error during registration: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
