<?php
session_start();
include '../connection/index.php';
$id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = '$id'";
$result4 = mysqli_query($connect, $query);
$product = mysqli_fetch_assoc($result4);
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $caption = $_POST['caption'];
    $description = $_POST['description'];
    $categoryId = $_POST['categoryId'];
    $file = $_FILES["cv"]["name"];
    $path = $_FILES['cv']['tmp_name'];
    $folder = "../../assets/images/";
    if ($file) {
        $final_name = str_replace(" ", "-", $file);
        move_uploaded_file($path, $folder . $final_name);
        $imageQuery = ", image='$final_name'";
    } else {
        $imageQuery = "";
    }
    $updateQuery = "UPDATE products SET 
        name='$name', 
        amount='$amount', 
        caption='$caption', 
        description='$description', 
        categoryId='$categoryId' 
        $imageQuery 
        WHERE id='$id'";
    $update = mysqli_query($connect, $updateQuery);
    if ($update) {
        echo "Product Updated Successfully!";
        header("Location: index.php");
    } else {
        echo "Failed to Update Product.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../head/head2.php" ?>
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
  <?php include "../header/header2.php" ?>
  <?php include "../aside/aside2.php" ?>
  <main id="main" class="main">
    <section class="section">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Update Product Details</h5>
            <form method="POST" action="" autocomplete="on" enctype="multipart/form-data">
              <div class="row mb-3">
                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputAmount" class="col-sm-2 col-form-label">Amount</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="amount" value="<?php echo htmlspecialchars($product['amount']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputCaption" class="col-sm-2 col-form-label">Caption</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="caption" value="<?php echo htmlspecialchars($product['caption']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                  <textarea rows="20" id="editor" class="form-control" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputCategory" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                  <select class="form-select" name="categoryId">
                    <?php
                    $categoryQuery = "SELECT * FROM categories";
                    $categoryResult = mysqli_query($connect, $categoryQuery);
                    while ($category = mysqli_fetch_assoc($categoryResult)) {
                        $selected = ($category['id'] == $product['categoryId']) ? 'selected' : '';
                        echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputImage" class="col-sm-2 col-form-label">Main Image</label>
                <div class="col-sm-10">
                  <input class="form-control" type="file" name="cv" id="file_id" onchange="upload_check();">
                  <?php if ($product['image']) { ?>
                    <img src="../../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" style="margin-top: 10px; max-width: 200px;">
                  <?php } ?>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                  <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include "../footer/footer.php" ?>
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
  <script src="ckeditor/ckeditor.js"></script>
  <script>
    CKEDITOR.replace('editor', {
      filebrowserUploadUrl: 'upload.php',
      filebrowserUploadMethod: 'form',
    });
  </script>
</body>
</html>