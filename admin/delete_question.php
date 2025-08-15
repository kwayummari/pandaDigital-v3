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

    // Get question details first to check if it exists
    $question = $questionModel->getQuestionById($questionId);

    if (!$question) {
        echo json_encode([
            'success' => false,
            'message' => 'Swali halijulikani'
        ]);
        exit;
    }

    // Delete the question
    if ($questionModel->deleteQuestion($questionId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Swali limefutwa kikamilifu!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Imefeli kufuta swali. Tafadhali jaribu tena.'
        ]);
    }
} catch (Exception $e) {
    error_log("Error deleting question: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mtandao. Jaribu tena.'
    ]);
}
