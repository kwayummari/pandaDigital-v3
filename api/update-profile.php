<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../services/AuthService.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Check if user is logged in
$authService = new AuthService();
if (!$authService->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$currentUser = $authService->getCurrentUser();

try {
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'phone', 'region'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Tafadhali jaza sehemu zote zinazohitajika: ' . implode(', ', $missingFields)
        ]);
        exit();
    }
    
    // Sanitize input data
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $region = trim($_POST['region']);
    
    // Validate phone number format (Tanzania format)
    if (!preg_match('/^(\+255|0)[1-9][0-9]{8}$/', $phone)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Namba ya simu si sahihi. Tumia muundo wa Tanzania (mfano: 0712345678)'
        ]);
        exit();
    }
    
    // Update user profile in database
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD
    );
    
    $stmt = $db->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, phone = ?, region = ?, date_updated = NOW() 
        WHERE id = ?
    ");
    
    $result = $stmt->execute([$firstName, $lastName, $phone, $region, $currentUser['id']]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Maelezo yako yamehifadhiwa kwa mafanikio!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Haikuweza kuhifadhi maelezo, jaribu tena.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mfumo, jaribu tena.'
    ]);
}
?>
