<?php
session_start();
require_once "../admin/includes/db/connect.php";

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    
    // Prepare the update query
    $updateQuery = "UPDATE users SET isSeller = 1 WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        // Update the session variable
        $_SESSION['isSeller'] = 1;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
}

$conn->close();
?>
