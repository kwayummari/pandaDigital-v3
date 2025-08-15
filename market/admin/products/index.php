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
$query = "SELECT p.*, c.name as categoryName FROM products p JOIN categories c ON p.categoryId = c.id WHERE p.sellerId = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result4 = $stmt->get_result();
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
          <h5 class="card-title">Data Zote<span>| Leo</span> <a href="add.php"><span class="badge bg-success text-white">+ Ongeza Bidhaa</span></a></h5>
          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th scope="col">Jina</th>
                <th scope="col">Kiasi</th>
                <th scope="col">Manukuu</th>
                <th scope="col">Kategoria</th>
                <th scope="col">Picha</th>
                <th scope="col">Hali</th>
                <th scope="col">Ofa</th>
                <th scope="col">Vitendo</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($rows4 = $result4->fetch_assoc()) {
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($rows4['name']); ?></td>
                  <td><?php echo htmlspecialchars($rows4['amount']); ?></td>
                  <td>
                    <?php
                    $caption = htmlspecialchars($rows4['caption']);
                    if (strlen($caption) > 50) { // Adjust the character limit as needed
                      $truncatedCaption = substr($caption, 0, 50) . '...';
                      echo '<span class="short-caption">' . $truncatedCaption . '</span>';
                      echo '<span class="full-caption" style="display:none;">' . $caption . '</span>';
                      echo '<a href="#" class="read-more" onclick="toggleCaption(this); return false;">Read More</a>';
                    } else {
                      echo $caption;
                    }
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($rows4['categoryName']); ?></td>
                  <td><img src="../../assets/images/<?php echo htmlspecialchars($rows4['image']); ?>" alt="Profile" class="rounded-circle" style="height: 50px; width: 50px"></td>
                  <td>
                    <?php echo $rows4['status'] == 1 ? 'Active' : 'Inactive'; ?>
                  </td>
                  <td>
                    <?php if ($rows4['isOffered'] == 0) { ?>
                      <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#offerModal" data-product-id="<?php echo $rows4['id']; ?>">
                        Add Offer
                      </button>
                    <?php } else { ?>
                      <span><?php echo htmlspecialchars($rows4['offer']); ?>%</span>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeOfferModal" data-product-id="<?php echo $rows4['id']; ?>">
                        Remove Offer
                      </button>
                    <?php } ?>
                  </td>
                  <td>
                    <a href="update.php?id=<?php echo urlencode($rows4['id']); ?>"><i class="bi bi-pen" style="color: green; padding-right: 15px;"></i></a>
                    <a href="update_gallery.php?productId=<?php echo urlencode($rows4['id']); ?>"><i class="bi bi-images" style="color: green; padding-right: 15px;"></i></a>
                    <a href="toggle_status.php?id=<?php echo urlencode($rows4['id']); ?>"><i class="bi bi-toggle2-on" style="color: blue; padding-right: 15px;"></i></a>
                    <a href="delete.php?id=<?php echo urlencode($rows4['id']); ?>"><i class="bi bi-archive-fill" style="color: red;"></i></a>
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
    </div>
    <div class="col-lg-4">
    </div>
    <!-- Offer Modal -->
    <div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="offerModalLabel">Weka Ofa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="offerForm" method="POST" action="add_offer.php">
            <div class="modal-body">
              <input type="hidden" name="product_id" id="product_id">
              <div class="mb-3">
                <label for="offer_percentage" class="form-label">Asilimia ya Ofa</label>
                <select class="form-select" id="offer_percentage" name="offer_percentage">
                  <option value="10">10%</option>
                  <option value="20">20%</option>
                  <option value="30">30%</option>
                  <option value="40">40%</option>
                  <option value="50">50%</option>
                  <option value="60">60%</option>
                  <option value="70">70%</option>
                  <option value="80">80%</option>
                  <option value="90">90%</option>
                </select>
                <input type="number" class="form-control mt-2" id="custom_percentage" name="custom_percentage" placeholder="Or enter custom percentage" min="1" max="100">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
              <button type="submit" class="btn btn-primary">Hifadhi mabadiliko</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Remove Offer Modal -->
    <div class="modal fade" id="removeOfferModal" tabindex="-1" aria-labelledby="removeOfferModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="removeOfferModalLabel">Ondoa Ofa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="removeOfferForm" method="POST" action="remove_offer.php">
            <div class="modal-body">
              <input type="hidden" name="product_id" id="remove_product_id">
              <p>Je, una uhakika unataka kuondoa ofa kutoka kwa bidhaa hii?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
              <button type="submit" class="btn btn-danger">Ondoa Ofa</button>
            </div>
          </form>
        </div>
      </div>
    </div>


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
  <script>
    function toggleCaption(element) {
      var shortCaption = element.previousElementSibling.previousElementSibling;
      var fullCaption = element.previousElementSibling;
      if (fullCaption.style.display === "none") {
        fullCaption.style.display = "inline";
        shortCaption.style.display = "none";
        element.innerText = "Read Less";
      } else {
        fullCaption.style.display = "none";
        shortCaption.style.display = "inline";
        element.innerText = "Read More";
      }
    }
  </script>
  <script>
    var offerModal = document.getElementById('offerModal');
    offerModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var productId = button.getAttribute('data-product-id');
      var modalProductId = offerModal.querySelector('#product_id');
      modalProductId.value = productId;
    });

    document.getElementById('offerForm').addEventListener('submit', function(event) {
      var customPercentage = document.getElementById('custom_percentage').value;
      if (customPercentage) {
        document.getElementById('offer_percentage').value = customPercentage;
      }
    });
  </script>
  <script>
    var removeOfferModal = document.getElementById('removeOfferModal');
    removeOfferModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var productId = button.getAttribute('data-product-id');
      var modalProductId = removeOfferModal.querySelector('#remove_product_id');
      modalProductId.value = productId;
    });
  </script>

</body>

</html>