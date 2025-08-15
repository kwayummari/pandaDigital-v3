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

try {
    // Validate required fields
    $title = trim($_POST['title'] ?? '');
    $instructorId = $_POST['instructor_id'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'draft';

    if (empty($title) || empty($instructorId)) {
        echo json_encode(['success' => false, 'message' => 'Jina la kozi na mwalimu ni lazima']);
        exit;
    }

    // Handle file upload if image is provided
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/courses/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Uwezo wa picha tu: JPG, PNG, GIF']);
            exit;
        }

        $fileName = 'course_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $imageUrl = 'uploads/courses/' . $fileName;
        }
    }

    // Create course data array
    $courseData = [
        'title' => $title,
        'description' => $description,
        'instructor_id' => $instructorId,
        'price' => $price > 0 ? $price : 0,
        'status' => $status,
        'image_url' => $imageUrl,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Use the Course model to add the course
    $courseModel = new Course();
    $courseId = $courseModel->addCourse($courseData);

    if ($courseId) {
        echo json_encode([
            'success' => true,
            'message' => 'Kozi imehifadhiwa kwa mafanikio!',
            'course_id' => $courseId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Haikuweza kuhifadhi kozi'
        ]);
    }
} catch (Exception $e) {
    error_log("Error adding course: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Kuna tatizo la kupata maelezo ya kozi']);
}
