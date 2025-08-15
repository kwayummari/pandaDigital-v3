<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Question.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Set content type to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get question ID from POST data
$questionId = $_POST['id'] ?? null;

if (!$questionId || !is_numeric($questionId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID ya swali si sahihi'
    ]);
    exit;
}

try {
    // Initialize model
    $questionModel = new Question();

    // Get question details
    $question = $questionModel->getQuestionById($questionId);

    if (!$question) {
        echo json_encode([
            'success' => false,
            'message' => 'Swali halijulikani'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'question' => $question
    ]);
} catch (Exception $e) {
    error_log("Error getting question details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mtandao. Jaribu tena.'
    ]);
}
