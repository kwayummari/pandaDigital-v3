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
        'title' => $course['name'] ?? $course['title'] ?? 'N/A',
        'description' => $course['description'] ?? '',
        'price' => $course['price'] ?? 0,
        'status' => $course['status'] ?? 'pending',
        'created_at' => $course['created_at'] ?? $course['dateCreated'] ?? date('Y-m-d H:i:s'),
        'image_url' => !empty($course['photo']) ? '../uploads/courses/' . $course['photo'] : '../assets/images/default-course.jpg',
        'instructor_name' => 'Admin', // TODO: Add instructor info to course table
        'enrollment_count' => $courseStats['total_students'] ?? 0,
        'video_count' => $courseStats['total_lessons'] ?? 0
    ];

    echo json_encode(['success' => true, 'course' => $courseData]);
} catch (Exception $e) {
    error_log("Error getting course details: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error retrieving course details']);
}
