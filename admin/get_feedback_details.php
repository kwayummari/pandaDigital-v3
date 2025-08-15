<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Feedback.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get feedback ID from request
$feedbackId = $_GET['id'] ?? null;

if (!$feedbackId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Feedback ID haijatolewa'
    ]);
    exit;
}

try {
    // Initialize Feedback model
    $feedbackModel = new Feedback();

    // Get feedback details
    $feedback = $feedbackModel->getFeedbackById($feedbackId);

    // Debug logging
    error_log("Feedback ID requested: " . $feedbackId);
    error_log("Feedback data returned: " . json_encode($feedback));

    if (!$feedback) {
        echo json_encode([
            'success' => false,
            'message' => 'Feedback haijapatikana'
        ]);
        exit;
    }

    // Return feedback data
    echo json_encode([
        'success' => true,
        'feedback' => $feedback
    ]);
} catch (Exception $e) {
    error_log("Error getting feedback details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kupata maelezo ya feedback'
    ]);
}
