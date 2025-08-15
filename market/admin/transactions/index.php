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

// Query to select transaction data for the specific seller
$query = "SELECT 
    t.id as transaction_id,
    t.reference,
    t.message as status,
    t.created_at as transaction_date,
    s.amount,
    s.quantity,
    s.productId,
    s.status as sale_status,
    p.name as product_name
FROM 
    transactions t
JOIN 
    sales s ON t.reference = s.reference_no
JOIN 
    products p ON s.productId = p.id
WHERE 
    p.sellerId = ?
ORDER BY 
    t.created_at DESC";

$stmt = $connect->prepare($query);
$stmt->bind_param("i", $userId);
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
          <h5 class="card-title">My Transactions <span>| All Time</span></h5>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th scope="col">Reference</th>
                <th scope="col">Product</th>
                <th scope="col">Amount (TSh)</th>
                <th scope="col">Quantity</th>
                <th scope="col">Status</th>
                <th scope="col">Transaction Date</th>
                <th scope="col">Sale Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  // Format date
                  $date = date('M d, Y H:i', strtotime($row['transaction_date']));
                  
                  // Determine status badge color
                  $statusBadge = $row['status'] === 'Success' ? 'success' : 'danger';
                  
                  // Determine sale status badge color
                  $saleStatusBadge = $row['sale_status'] == 1 ? 'success' : 'warning';
                  $saleStatusText = $row['sale_status'] == 1 ? 'Completed' : 'Pending';
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['reference']); ?></td>
                  <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                  <td><?php echo number_format($row['amount'], 2); ?></td>
                  <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                  <td><span class="badge bg-<?php echo $statusBadge; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                  <td><?php echo $date; ?></td>
                  <td><span class="badge bg-<?php echo $saleStatusBadge; ?>"><?php echo $saleStatusText; ?></span></td>
                </tr>
              <?php
                }
              } else {
              ?>
                <tr>
                  <td colspan="7" class="text-center">No transactions found</td>
                </tr>
              <?php
              }
              $stmt->close();
              ?>
            </tbody>
          </table>

          <!-- Add summary section -->
          <?php
          // Reset the result set pointer
          $stmt = $connect->prepare($query);
          $stmt->bind_param("i", $userId);
          $stmt->execute();
          $result = $stmt->get_result();

          $total_transactions = 0;
          $total_amount = 0;
          $total_quantity = 0;
          $successful_transactions = 0;

          while ($row = $result->fetch_assoc()) {
              $total_transactions++;
              $total_amount += $row['amount'];
              $total_quantity += $row['quantity'];
              if ($row['status'] === 'Success') {
                  $successful_transactions++;
              }
          }
          ?>
          <div class="mt-4">
            <h6 class="card-title">Transaction Summary</h6>
            <div class="row">
              <div class="col-md-3">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Transactions</h6>
                    <p class="card-text"><?php echo $total_transactions; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Amount</h6>
                    <p class="card-text">TSh <?php echo number_format($total_amount, 2); ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Items Sold</h6>
                    <p class="card-text"><?php echo $total_quantity; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Successful Transactions</h6>
                    <p class="card-text"><?php echo $successful_transactions; ?></p>
                  </div>
                </div>
              </div>
            </div>
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