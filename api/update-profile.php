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
    // Get all submitted fields
    $fields = ['first_name', 'last_name', 'phone', 'region', 'gender', 'date_of_birth'];
    $data = [];
    $missingFields = [];

    foreach ($fields as $field) {
        if (isset($_POST[$field]) && !empty(trim($_POST[$field]))) {
            $data[$field] = trim($_POST[$field]);
        } else {
            $missingFields[] = $field;
        }
    }

    // Check if we have at least some data
    if (empty($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Tafadhali jaza angalau sehemu moja ya maelezo'
        ]);
        exit();
    }

    // Validate phone number format if provided (Tanzania format)
    if (isset($data['phone']) && !preg_match('/^(\+255|0)[1-9][0-9]{8}$/', $data['phone'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Namba ya simu si sahihi. Tumia muundo wa Tanzania (mfano: 0712345678)'
        ]);
        exit();
    }

    // Update user profile in database
    require_once __DIR__ . '/../config/init.php';
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Build dynamic UPDATE query
    $setClause = [];
    $values = [];

    foreach ($data as $field => $value) {
        $setClause[] = "$field = ?";
        $values[] = $value;
    }

    // Add updated_at timestamp
    $setClause[] = "updated_at = NOW()";

    // Add user ID for WHERE clause
    $values[] = $currentUser['id'];

    $sql = "UPDATE users SET " . implode(', ', $setClause) . " WHERE id = ?";
    $stmt = $db->prepare($sql);

    $result = $stmt->execute($values);

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
