<?php
session_start();
if (isset($_SESSION['message'])) {
  echo "<div class='alert alert-success'>" . $_SESSION['message'] . "</div>";
  unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
  echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
  unset($_SESSION['error']);
}
include '../connection/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate and sanitize input
  $categoryName = htmlspecialchars($_POST['categoryName']);
  $categoryDescription = htmlspecialchars($_POST['categoryDescription']);
  $image = $_FILES['categoryImage']['name'];

  // Handle image upload
  $target_dir = "../../assets/images/";
  $target_file = $target_dir . basename($_FILES["categoryImage"]["name"]);

  if (move_uploaded_file($_FILES["categoryImage"]["tmp_name"], $target_file)) {
    // Prepare SQL query to insert category data
    $query = "INSERT INTO categories (name, image, description) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("sss", $categoryName, $image, $categoryDescription);
    
    if ($stmt->execute()) {
      $_SESSION['message'] = "Category added successfully!";
    } else {
      $_SESSION['error'] = "Error adding category!";
    }

    $stmt->close();
    header('Location: index.php'); // Redirect to the page where categories are listed
  } else {
    $_SESSION['error'] = "Error uploading image!";
  }
}
