<?php
// Include your database connection file
include '../admin/includes/db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'];
    $status = $_POST['status'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO marked (status, productId) VALUES (?, ?)");
    $stmt->bind_param("ii", $status, $productId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
}
