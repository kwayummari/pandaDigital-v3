<?php
include '../connection/index.php';

if (isset($_POST['productId']) && isset($_POST['newStatus'])) {
    $productId = intval($_POST['productId']);
    $newStatus = intval($_POST['newStatus']);

    $query = "UPDATE products SET status = ? WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('ii', $newStatus, $productId);

    if ($stmt->execute()) {
        echo "Product status updated successfully.";
    } else {
        echo "Failed to update product status.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

mysqli_close($connect);
