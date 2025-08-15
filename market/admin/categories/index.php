<?php
session_start();
if (!isset($_SESSION['userEmail'], $_SESSION['userPhone'], $_SESSION['userFullName'], $_SESSION['isSeller'], $_SESSION['userId'])) {
  header("Location: ../../../../index.php");
  exit();
}

$email = $_SESSION['userEmail'];
$phone = $_SESSION['userPhone'];
$full_name = $_SESSION['userFullName'];
$isSeller = $_SESSION['isSeller'];
$userId = $_SESSION['userId'];

include '../connection/index.php';

// Query to select data from the categories table
$query = "SELECT id, name, image, description FROM categories";
$stmt = $connect->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../head/head2.php" ?>

<body>
  <?php include "../header/header2.php" ?>
  <?php include "../aside/aside2.php" ?>
  <main id="main" class="main">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <h5 class="card-title">Categories List <span>| Today</span>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">+ Add Category</button>
          </h5>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Image</th>
                <th scope="col">Description</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Loop through each category and display its data
              while ($row = $result->fetch_assoc()) {
              ?>
                <tr>
                  <td><?php echo $row['name']; ?></td>
                  <td><img src="../../assets/images/<?php echo $row['image']; ?>" alt="Category Image" class="rounded-circle" style="height: 50px; width: 50px"></td>
                  <td><?php echo $row['description']; ?></td>
                  <td>
                    <a href="update.php?id=<?php echo urlencode($row['id']); ?>"><i class="bi bi-pen" style="color: green; padding-right: 15px;"></i></a>
                  </td>
                </tr>
              <?php
              }
              $stmt->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Add Category Modal -->
      <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm" method="POST" action="add_category.php" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="categoryName" class="form-label">Category Name</label>
                  <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                </div>
                <div class="mb-3">
                  <label for="categoryImage" class="form-label">Category Image</label>
                  <input type="file" class="form-control" id="categoryImage" name="categoryImage" required>
                </div>
                <div class="mb-3">
                  <label for="categoryDescription" class="form-label">Category Description</label>
                  <textarea class="form-control" id="categoryDescription" name="categoryDescription" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Category</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php include "../footer/footer.php" ?>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>

</html>