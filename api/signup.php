<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../services/AuthService.php';

header('Content-Type: application/json');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Unaweza kuingia tu kama hujasajiliwa.'
    ]);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Njia hii hairuhusiwi.'
    ]);
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Token si sahihi. Jaribu tena.'
    ]);
    exit();
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$agreeTerms = isset($_POST['agree_terms']);

// Validate required fields
if (empty($email) || empty($password) || empty($confirmPassword)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Tafadhali jaza sehemu zote muhimu.'
    ]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Barua pepe si sahihi.'
    ]);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Nywila lazima iwe na herufi 6 au zaidi.'
    ]);
    exit();
}

// Validate password confirmation
if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Nywila hazifanani.'
    ]);
    exit();
}

// Validate terms agreement
if (!$agreeTerms) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Lazima ukubaliane na vigezo na masharti.'
    ]);
    exit();
}

try {
    require_once __DIR__ . '/../config/init.php';
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Barua pepe hii tayari imesajiliwa. Jaribu kuingia.'
        ]);
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user - using correct table structure
    $stmt = $db->prepare("INSERT INTO users (email, pass, first_name, last_name, role, status, bio, expert_authorization) VALUES (?, ?, '', '', 'user', 'free', 'none', '0')");

    $result = $stmt->execute([$email, $hashedPassword]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Usajili umekamilika kwa mafanikio! Sasa unaweza kuingia.',
            'user_id' => $email
        ]);
    } else {
        throw new Exception('Failed to insert user');
    }
} catch (Exception $e) {
    error_log("Signup error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mtandao. Jaribu tena.'
    ]);
}
