<?php
require_once __DIR__ . "/../config/init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $trackQuery = "INSERT INTO user_page_tracking (user_id, page_type, page_url, course_id, video_id, action_type, session_id, ip_address, user_agent) 
                    VALUES (?, 'learn_video', ?, ?, ?, 'video_start', ?, ?, ?)";
    $trackStmt = $pdo->prepare($trackQuery);
    $trackStmt->execute([
        $_SESSION['user_id'],
        $_SERVER['HTTP_REFERER'] ?? '',
        $data['course_id'] ?? null,
        $data['video_id'] ?? null,
        session_id(),
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    echo json_encode(['success' => true]);
}
?>
