<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

header('Content-Type: application/json');

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

$courseModel = new Course();
$courseId = $_GET['id'] ?? null;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit;
}

try {
    $course = $courseModel->getCourseById($courseId);
    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    // Get additional course statistics
    $courseStats = $courseModel->getCourseStats($courseId);

    // Prepare course data for the modal
    $courseData = [
        'id' => $course['id'],
        'title' => $course['title'] ?? 'N/A',
        'description' => $course['description'] ?? 'N/A',
        'price' => $course['price'] ?? 0,
        'status' => $course['status'] ?? 'pending',
        'created_at' => $course['created_at'] ?? 'N/A',
        'image_url' => $course['image_url'] ?? null,
        'instructor_name' => $course['instructor_name'] ?? 'N/A',
        'enrollment_count' => $courseStats['total_enrollments'] ?? 0,
        'video_count' => $courseStats['total_videos'] ?? 0
    ];

    echo json_encode(['success' => true, 'course' => $courseData]);
} catch (Exception $e) {
    error_log("Error getting course details: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error retrieving course details']);
}
