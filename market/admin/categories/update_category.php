<?php
include '../connection/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $categoryId = $_POST['categoryId'];
  $categoryName = $_POST['categoryName'];
  $categoryDescription = $_POST['categoryDescription'];

  // Check if a new image was uploaded
  if (!empty($_FILES['categoryImage']['name'])) {
    $target_dir = "../../assets/images/";
    $image = basename($_FILES["categoryImage"]["name"]);
    $target_file = $target_dir . $image;
    move_uploaded_file($_FILES["categoryImage"]["tmp_name"], $target_file);

    // Update query with image
    $query = "UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('sssi', $categoryName, $categoryDescription, $image, $categoryId);
  } else {
    // Update query without image
    $query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('ssi', $categoryName, $categoryDescription, $categoryId);
  }

  if ($stmt->execute()) {
    // Success
    header("Location: categories_list.php?success=Category updated successfully");
  } else {
    // Error
    header("Location: categories_list.php?error=Error updating category");
  }

  $stmt->close();
  $connect->close();
}
