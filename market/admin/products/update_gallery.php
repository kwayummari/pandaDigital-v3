<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
include '../connection/index.php';

// Handle Upload
if (isset($_POST['upload']) && isset($_FILES['gallery_images'])) {
    $productId = intval($_POST['productId']);
    $folder = "../../assets/images/";
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    // Check current image count
    $query = "SELECT COUNT(*) as count FROM gallery WHERE productId = '$productId'";
    $result = mysqli_query($connect, $query);
    $data = mysqli_fetch_assoc($result);
    $currentImageCount = $data['count'];

    if ($currentImageCount < 4) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['gallery_images']['name'][$key];
            $file_tmp = $_FILES['gallery_images']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_types)) {
                $final_name = uniqid() . '-' . str_replace(" ", "-", $file_name);
                $upload_path = $folder . $final_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $query = "INSERT INTO gallery (productId, image) VALUES ('$productId', '$final_name')";
                    mysqli_query($connect, $query);
                }
            }
        }
    }
    header("Location: update_gallery.php?productId=" . $productId);
    exit();
}

// Handle Delete
if (isset($_GET['delete_image'])) {
    $imageId = intval($_GET['delete_image']);
    $productId = intval($_GET['productId']);
    
    $query = "SELECT image FROM gallery WHERE id = '$imageId'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        $imagePath = "../../assets/images/" . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $query = "DELETE FROM gallery WHERE id = '$imageId'";
        mysqli_query($connect, $query);
    }
    
    header("Location: update_gallery.php?productId=" . $productId);
    exit();
}

$productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;
$query = "SELECT * FROM gallery WHERE productId = '$productId'";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<?php include "../head/head2.php"; ?>
<body>
    <?php include "../header/header2.php"; ?>
    <?php include "../aside/aside2.php"; ?>
    <main id="main" class="main">
        <section class="section">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Update Product Gallery</h5>
                        
                        <!-- Upload Form -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <label for="inputFiles" class="col-sm-2 col-form-label">Select Images</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="gallery_images[]" id="gallery_images" multiple accept="image/*" onchange="previewImages();">
                                    <div id="image_preview" style="margin-top: 10px;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <input type="hidden" name="productId" value="<?php echo $productId; ?>">
                                    <button type="submit" name="upload" class="btn btn-primary">Upload Images</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <!-- Display Existing Images -->
                        <h5 class="card-title">Existing Images</h5>
                        <div class="row">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <img src="../../assets/images/<?php echo htmlspecialchars($row['image']); ?>" 
                                         class="card-img-top" 
                                         alt="Gallery Image"
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <a href="?delete_image=<?php echo $row['id']; ?>&productId=<?php echo $productId; ?>" 
                                           class="btn btn-danger btn-sm w-100"
                                           onclick="return confirm('Are you sure you want to delete this image?');">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include "../footer/footer.php" ?>
  <script src="../assets/js/main.js"></script>

    <script>
        function previewImages() {
            var preview = document.getElementById('image_preview');
            preview.innerHTML = '';
            var files = document.getElementById('gallery_images').files;
            
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var img = document.createElement("img");
                img.classList.add("img-preview");
                img.src = URL.createObjectURL(file);
                img.style.width = "100px";
                img.style.height = "100px";
                img.style.margin = "10px";
                preview.appendChild(img);
            }
        }
    </script>
    <style>
        .img-preview {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
</body>
</html>