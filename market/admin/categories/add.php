<?php
session_start();
include '../connection/index.php';
$query = "SELECT id, name FROM categories";
$result = mysqli_query($connect, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($connect));
}
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
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
            <h5 class="card-title">Product Input<span style="color: green; margin-left: 6cm;"><b><?php include('adds.php'); ?></b></span> </h5>
            <form method="POST" target="_top" autocomplete="on" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="name">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Amount</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="amount">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Caption</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="caption">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-10">
                    <textarea type="text" rows="20" id="editor" class="form-control" name="description"></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Main Image</label>
                  <div class="col-sm-10">
                    <input class="form-control" type="file" name="cv" value="file" id="formFile">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="category" class="col-sm-2 col-form-label">Category</label>
                  <div class="col-sm-10">
                    <select class="form-control" name="category" id="category">
                      <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Submit Button</label>
                  <div class="col-sm-10">
                    <button type="submit" name="submit" class="btn btn-primary">Submit Product</button>
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