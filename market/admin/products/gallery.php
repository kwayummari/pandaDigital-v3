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
            <h5 class="card-title">Upload Images to Gallery</h5>
            <form method="POST" action="upload_gallery.php" target="_top" autocomplete="on" enctype="multipart/form-data">
              <div class="row mb-3">
                <label for="inputFiles" class="col-sm-2 col-form-label">Select Images</label>
                <div class="col-sm-10">
                  <input type="file" class="form-control" name="gallery_images[]" id="gallery_images" multiple accept="image/*" onchange="previewImages(); checkFileLimit();">
                  <div id="image_preview" style="margin-top: 10px;"></div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                  <button type="submit" name="upload" class="btn btn-primary">Upload Images</button>
                </div>
              </div>
              <input type="hidden" name="productId" value="<?php echo $_GET['productId']; ?>">
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include "../footer/footer.php"; ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
    function checkFileLimit() {
      var input = document.getElementById('gallery_images');
      var files = input.files;
      if (files.length > 4) {
        alert("You can only upload a maximum of 4 images.");
        input.value = "";
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