<?php
ob_start(); // Start output buffering
include '../connection/index.php';

if (isset($_POST['submit'])) {
    $sellerId = $_SESSION['userId'];
    $name = mysqli_real_escape_string($connect, trim($_POST['name']));
    $amount = mysqli_real_escape_string($connect, trim($_POST['amount']));
    $caption = mysqli_real_escape_string($connect, trim($_POST['caption']));
    $description = mysqli_real_escape_string($connect, trim($_POST['description']));
    $categoryId = mysqli_real_escape_string($connect, trim($_POST['category']));
    if (isset($_FILES["cv"]) && $_FILES["cv"]["error"] == 0) {
        $file = $_FILES["cv"]["name"];
        $path = $_FILES['cv']['tmp_name'];
        $folder = "../../assets/images/";
        $final_name = str_replace(" ", "-", $file);
        $query = $connect->prepare("INSERT INTO products (name, amount, caption, description, categoryId, sellerId, status, image) VALUES (?, ?, ?, ?, ?, ?, '0', ?)");
        $query->bind_param("ssssiss", $name, $amount, $caption, $description, $categoryId, $sellerId, $final_name);
        if ($query->execute()) {
            $productId = $query->insert_id;
            if (move_uploaded_file($path, $folder . $final_name)) {
                echo "<meta http-equiv='refresh' content='0;url=gallery.php?productId=$productId'>";
                exit;
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Failed to add product: " . $query->error;
        }
        $query->close();
    } else {
        echo "Invalid file upload.";
    }
}
$connect->close();
ob_end_flush();
