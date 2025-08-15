<?php
include '../admin/includes/db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = $_POST['reason'];
    $description = $_POST['description'];
    $productId = $_POST['productId'];

    $stmt = $conn->prepare("INSERT INTO abuse (reason, description, productId) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $reason, $description, $productId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
}
