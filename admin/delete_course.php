<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

header('Content-Type: application/json');

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$courseId = $input['id'] ?? null;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit;
}

try {
    $courseModel = new Course();
    $result = $courseModel->deleteCourse($courseId);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Kozi imefutwa kwa mafanikio!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Haikuweza kufuta kozi']);
    }
} catch (Exception $e) {
    error_log("Error deleting course: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Kuna tatizo la kufuta kozi']);
}
