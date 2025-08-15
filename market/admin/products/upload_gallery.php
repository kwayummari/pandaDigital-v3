<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include '../connection/index.php';

if (isset($_POST['upload'])) {
    $productId = intval($_POST['productId']);
    $folder = "../../assets/images/";
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    // Check the current number of images for the product
    $query = "SELECT COUNT(*) as image_count FROM gallery WHERE productId = '$productId'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);
    $currentImageCount = $row['image_count'];

    if ($currentImageCount >= 4) {
        echo "Maximum number of images (4) reached for this product.";
        exit();
    } else {
        $files_to_upload = count($_FILES['gallery_images']['name']);
        if ($currentImageCount + $files_to_upload > 4) {
            echo "You can only upload " . (4 - $currentImageCount) . " more images.";
            exit();
        }

        $upload_success = false;
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            if ($currentImageCount >= 4) {
                break;
            }

            $file_name = $_FILES['gallery_images']['name'][$key];
            $file_tmp = $_FILES['gallery_images']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_types)) {
                $final_name = str_replace(" ", "-", $file_name);
                $upload_path = $folder . $final_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $query = "INSERT INTO gallery (productId, image) VALUES ('$productId', '$final_name')";
                    $insert = mysqli_query($connect, $query);

                    if ($insert) {
                        $upload_success = true;
                        $currentImageCount++;
                    } else {
                        echo "Failed to add $file_name to the gallery.";
                    }
                } else {
                    echo "Failed to upload $file_name.";
                }
            } else {
                echo "File type not allowed: $file_name.";
            }
        }

        if ($upload_success) {
            header("Location: update_gallery.php?productId=$productId");
            exit();
        } else {
            echo "Failed to upload one or more images.";
        }
    }
}