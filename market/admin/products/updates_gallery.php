<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../connection/index.php';

// Add debugging at the start
error_log("Request received in updates_gallery.php");
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

if (isset($_POST['upload'])) {
    error_log("Upload button detected");
    
    $productId = intval($_POST['productId']);
    error_log("Product ID: " . $productId);
    
    $folder = "../../assets/images/";
    if (!file_exists($folder)) {
        error_log("Creating upload directory");
        mkdir($folder, 0777, true);
    }
    
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    
    // Debug current image count
    $query = "SELECT COUNT(*) as count FROM gallery WHERE productId = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $currentImageCount = $data['count'];
    error_log("Current image count: " . $currentImageCount);
    
    if ($currentImageCount < 4) {
        error_log("Processing file upload");
        
        if (!isset($_FILES['gallery_images'])) {
            error_log("No files uploaded");
            die("No files uploaded");
        }
        
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            error_log("Processing file: " . $_FILES['gallery_images']['name'][$key]);
            
            if (empty($tmp_name)) {
                error_log("Empty tmp_name for key: " . $key);
                continue;
            }
            
            $file_name = $_FILES['gallery_images']['name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                error_log("Invalid file type: " . $file_ext);
                continue;
            }
            
            $final_name = uniqid() . '-' . str_replace(" ", "-", $file_name);
            $upload_path = $folder . $final_name;
            
            error_log("Attempting to move file to: " . $upload_path);
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                error_log("File moved successfully");
                
                $query = "INSERT INTO gallery (productId, image) VALUES (?, ?)";
                $stmt = $connect->prepare($query);
                $stmt->bind_param("is", $productId, $final_name);
                
                if ($stmt->execute()) {
                    error_log("Database insert successful");
                } else {
                    error_log("Database insert failed: " . $stmt->error);
                }
                $stmt->close();
            } else {
                error_log("Failed to move uploaded file. Upload error code: " . $_FILES['gallery_images']['error'][$key]);
                error_log("PHP Last Error: " . error_get_last()['message']);
            }
        }
    } else {
        error_log("Maximum images reached");
        die("Maximum number of images (4) reached");
    }
    
    // Redirect back
    header("Location: update_gallery.php?productId=" . $productId);
    exit();
}

// If we get here without processing anything, show debug info
echo "<pre>";
echo "Debug Information:\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST Data: \n";
print_r($_POST);
echo "FILES Data: \n";
print_r($_FILES);
echo "</pre>";
?>