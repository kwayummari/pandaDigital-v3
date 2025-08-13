<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get form data
    $studentId = $currentUser['id'];
    $category = $_POST['category'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $courseId = !empty($_POST['courseId']) ? intval($_POST['courseId']) : null;
    $priority = $_POST['priority'] ?? 'medium';

    // Validate required fields
    if (empty($category)) {
        throw new Exception('Chagua aina ya maoni');
    }

    if (empty($subject)) {
        throw new Exception('Ingiza kichwa cha maoni');
    }

    if (empty($message)) {
        throw new Exception('Ingiza maelezo ya maoni');
    }

    if (strlen($subject) > 255) {
        throw new Exception('Kichwa cha maoni ni kirefu sana');
    }

    if (strlen($message) > 2000) {
        throw new Exception('Maelezo ya maoni ni marefu sana');
    }

    // Validate category
    $validCategories = ['technical', 'content', 'payment', 'interface', 'feature', 'account', 'general'];
    if (!in_array($category, $validCategories)) {
        throw new Exception('Aina ya maoni si sahihi');
    }

    // Validate priority
    $validPriorities = ['low', 'medium', 'high', 'urgent'];
    if (!in_array($priority, $validPriorities)) {
        throw new Exception('Kipaumbele si sahihi');
    }

    // Handle file upload if provided
    $attachmentUrl = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $attachmentUrl = handleFileUpload($_FILES['attachment']);
    }

    // Get client information
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Database connection
    require_once __DIR__ . "/../config/database.php";
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Start database transaction
    $conn->beginTransaction();

    // Insert feedback into database
    $stmt = $conn->prepare("
        INSERT INTO student_feedback 
        (student_id, feedback_type, subject, message, course_id, priority, attachment_url, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $studentId,
        $category,
        $subject,
        $message,
        $courseId,
        $priority,
        $attachmentUrl,
        $ipAddress,
        $userAgent
    ]);

    $feedbackId = $conn->lastInsertId();

    // If there's an attachment, save it to attachments table
    if ($attachmentUrl && isset($_FILES['attachment'])) {
        saveAttachmentRecord($conn, $feedbackId, $_FILES['attachment'], $attachmentUrl);
    }

    $conn->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Maoni yamepokewa kwa ufanisi',
        'feedback_id' => $feedbackId
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

    error_log("Feedback submission error: " . $e->getMessage());
}

function handleFileUpload($file)
{
    // Validate file
    $allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Aina ya faili haikubaliki. Tumia PNG, JPG, PDF au DOC');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('Faili ni kubwa sana. Tumia faili la chini ya 5MB');
    }

    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/feedback/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'feedback_' . uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Hitilafu ya kupakia faili');
    }

    return 'uploads/feedback/' . $filename;
}

function saveAttachmentRecord($conn, $feedbackId, $file, $filePath)
{
    $stmt = $conn->prepare("
        INSERT INTO feedback_attachments 
        (feedback_id, filename, original_filename, file_size, mime_type, file_path) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $filename = basename($filePath);
    $originalFilename = $file['name'];
    $fileSize = $file['size'];
    $mimeType = $file['type'];

    $stmt->execute([
        $feedbackId,
        $filename,
        $originalFilename,
        $fileSize,
        $mimeType,
        $filePath
    ]);
}
