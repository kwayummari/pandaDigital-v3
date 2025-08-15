<?php
include '../connection/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $productId = $_POST['product_id'];

  $query = "UPDATE products SET isOffered = 0, offer = NULL WHERE id = ?";
  $stmt = $connect->prepare($query);
  $stmt->bind_param("i", $productId);

  if ($stmt->execute()) {
    header("Location: index.php");
  } else {
    echo "Error updating record: " . $connect->error;
  }

  $stmt->close();
  $connect->close();
}

