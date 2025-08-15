<?php
session_start();
if (!isset($_SESSION['userEmail'], $_SESSION['userPhone'], $_SESSION['userFullName'], $_SESSION['isSeller'], $_SESSION['userId'])) {
    header("Location: ../../../../index.php");
    exit();
}
include '../connection/index.php';
$userId = $_SESSION['userId'];
$profile = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($connect, $_POST['profileName']);
    $description = mysqli_real_escape_string($connect, $_POST['profileDescription']);
    $email = mysqli_real_escape_string($connect, $_POST['profileEmail']);
    $phone = mysqli_real_escape_string($connect, $_POST['profilePhone']);
    $image = $_FILES['profileImage']['name'];
    $image_tmp = $_FILES['profileImage']['tmp_name'];
    $target_dir = "../../assets/images/";
    $target_file = $target_dir . basename($image);

    if (empty($image)) {
        $query = "UPDATE profile SET name = ?, description = ?, email = ?, phone = ? WHERE userId = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("ssssi", $name, $description, $email, $phone, $userId);
    } else {
        if (move_uploaded_file($image_tmp, $target_file)) {
            $query = "INSERT INTO profile (name, description, image, email, phone, userId) VALUES (?, ?, ?, ?, ?, ?) 
                      ON DUPLICATE KEY UPDATE name = ?, description = ?, image = ?, email = ?, phone = ?";
            $stmt = $connect->prepare($query);
            $stmt->bind_param("ssssssisii", $name, $description, $image, $email, $phone, $userId, $name, $description, $image, $email, $phone);
        } else {
            echo "Failed to upload image.";
            exit();
        }
    }

    if ($stmt->execute()) {
        echo "Profile uploaded/updated successfully.";
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $query = "SELECT * FROM profile WHERE userId = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    }
    $stmt->close();
}
mysqli_close($connect);
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
            <h5 class="card-title"><?php echo $profile ? 'Edit' : 'Upload'; ?> Business Profile</h5>
            <form method="POST" action="" enctype="multipart/form-data">
              <div class="row mb-3">
                <label for="profileName" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="profileName" name="profileName" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="profileDescription" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="profileDescription" name="profileDescription" rows="3" required><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                </div>
              </div>
              <div class="row mb-3">
                <label for="profileEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" id="profileEmail" name="profileEmail" value="<?php echo htmlspecialchars($profile['email'] ?? $_SESSION['userEmail']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="profilePhone" class="col-sm-2 col-form-label">Phone</label>
                <div class="col-sm-10">
                  <input type="tel" class="form-control" id="profilePhone" name="profilePhone" value="<?php echo htmlspecialchars($profile['phone'] ?? $_SESSION['userPhone']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="profileImage" class="col-sm-2 col-form-label">Image</label>
                <div class="col-sm-10">
                  <input type="file" class="form-control" id="profileImage" name="profileImage" accept="image/*">
                  <?php if ($profile && !empty($profile['image'])): ?>
                    <img src="../../assets/images/<?php echo htmlspecialchars($profile['image']); ?>" style="width: 100px; height: 100px; margin: 10px;">
                  <?php endif; ?>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                  <button type="submit" name="upload" class="btn btn-primary"><?php echo $profile ? 'Update' : 'Upload'; ?> Profile</button>
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
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>
</html>
