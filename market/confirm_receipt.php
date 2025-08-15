<?php
session_start();
require_once "../admin/includes/db/connect.php";
if (isset($_GET['saleId'])) {
    $saleId = intval($_GET['saleId']);
    $updateQuery = "
        UPDATE sales
        SET status = 1
        WHERE id = ?
    ";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $saleId);

    if ($updateStmt->execute()) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error updating record: " . $updateStmt->error;
    }
    $updateStmt->close();
}
mysqli_close($conn);
