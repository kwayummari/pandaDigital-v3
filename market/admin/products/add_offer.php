<?php
include '../connection/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $productId = $_POST['product_id'];
  $offerPercentage = $_POST['offer_percentage'];

  $query = "UPDATE products SET isOffered = 1, offer = ? WHERE id = ?";
  $stmt = $connect->prepare($query);
  $stmt->bind_param("ii", $offerPercentage, $productId);

  if ($stmt->execute()) {
    header("Location: index.php");
  } else {
    echo "Error updating record: " . $connect->error;
  }

  $stmt->close();
  $connect->close();
}
