<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Video.php";

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

// Get video ID from POST data
$videoId = $_POST['id'] ?? null;

if (!$videoId || !is_numeric($videoId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID ya video si sahihi'
    ]);
    exit;
}

try {
    // Initialize model
    $videoModel = new Video();

    // Get video details
    $video = $videoModel->getVideoById($videoId);

    if (!$video) {
        echo json_encode([
            'success' => false,
            'message' => 'Video haijulikani'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'video' => $video
    ]);
} catch (Exception $e) {
    error_log("Error getting video details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la mtandao. Jaribu tena.'
    ]);
}
