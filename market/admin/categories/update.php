<?php
session_start();
include '../connection/index.php';

// Get category ID from the URL
$id = $_GET['id'];

// Fetch the category data based on the ID
$query = "SELECT * FROM categories WHERE id = '$id'";
$result = mysqli_query($connect, $query);
$category = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    // Get values from the form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $file = $_FILES["image"]["name"]; // Category image file input
    $path = $_FILES['image']['tmp_name'];
    $folder = "../../assets/images/";

    // If an image is uploaded, move it to the assets folder and prepare the image update query
    if ($file) {
        $final_name = str_replace(" ", "-", $file); // Sanitize file name
        move_uploaded_file($path, $folder . $final_name);
        $imageQuery = ", image='$final_name'";
    } else {
        $imageQuery = ""; // If no image is uploaded, do not change the image
    }

    // Update query for the category
    $updateQuery = "UPDATE categories SET 
        name='$name', 
        description='$description' 
        $imageQuery 
        WHERE id='$id'";

    // Execute the update query
    $update = mysqli_query($connect, $updateQuery);

    // Check if the update was successful
    if ($update) {
        echo "Category Updated Successfully!";
        header("Location: index.php"); // Redirect back to category list
    } else {
        echo "Failed to Update Category.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "../head/head2.php"; ?>
<script>
  function upload_check() {
    var upl = document.getElementById("file_id");
    var max = 1500000;

    if (upl.files[0].size > max) {
      alert("Image is too Large!");
      upl.value = "";
    }
  };
</script>
<body>
  <?php include "../header/header2.php"; ?>
  <?php include "../aside/aside2.php"; ?>

  <main id="main" class="main">
    <section class="section">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Update Category Details</h5>

            <!-- Form for updating category -->
            <form method="POST" action="" autocomplete="on" enctype="multipart/form-data">
              
              <!-- Category Name -->
              <div class="row mb-3">
                <label for="inputName" class="col-sm-2 col-form-label">Category Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>
              </div>

              <!-- Category Description -->
              <div class="row mb-3">
                <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                  <textarea rows="10" id="editor" class="form-control" name="description" required><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
              </div>

              <!-- Category Image -->
              <div class="row mb-3">
                <label for="inputImage" class="col-sm-2 col-form-label">Category Image</label>
                <div class="col-sm-10">
                  <input class="form-control" type="file" name="image" id="file_id" onchange="upload_check();">
                  <?php if ($category['image']) { ?>
                    <img src="../../assets/images/<?php echo htmlspecialchars($category['image']); ?>" alt="Current Image" style="margin-top: 10px; max-width: 200px;">
                  <?php } ?>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                  <button type="submit" name="submit" class="btn btn-primary">Update Category</button>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include "../footer/footer.php"; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.min.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>

  <!-- CKEditor script -->
  <script src="ckeditor/ckeditor.js"></script>
  <script>
    CKEDITOR.replace('editor', {
      filebrowserUploadUrl: 'upload.php',
      filebrowserUploadMethod: 'form',
    });
  </script>
</body>
</html>
