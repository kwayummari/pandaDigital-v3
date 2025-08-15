<?php
session_start();
$user_id = $_SESSION['userId'];
$role = $_SESSION['role'];
$userFullName = $_SESSION['userFullName'];
$userEmail = $_SESSION['userEmail'];
$userPhone = $_SESSION['userPhone'];
$isSeller = $_SESSION['isSeller'];
include 'connection/index.php';


$query = "SELECT * FROM products WHERE sellerId = '$user_id'";
$result = mysqli_query($connect, $query);
$total_products = mysqli_num_rows($result);

$query_sales = "
SELECT 
    COUNT(*) AS total_sales, 
    SUM(sales.amount) AS total_sales_amount,
    COALESCE((
        SELECT SUM(amount)
        FROM withdrawals
        WHERE sellerId = '$user_id'
    ), 0) as total_withdrawn
FROM 
    sales
JOIN 
    products ON sales.productId = products.id
JOIN 
    transactions ON sales.reference_no = transactions.reference
WHERE 
    sales.status = 1 
    AND products.sellerId = '$user_id' 
    AND transactions.message = 'Success';
";
$sales_result = mysqli_query($connect, $query_sales);
$sales_data = mysqli_fetch_assoc($sales_result);
$total_sales = $sales_data['total_sales'];
$total_sales_amount = $sales_data['total_sales_amount'];
$total_withdrawn = $sales_data['total_withdrawn'];
$payable_amount = $total_sales_amount - ($total_sales_amount * 0.03) - $total_withdrawn - $pending_withdrawals;


// Fetch most sold product
$query_most_sold = "SELECT 
    products.name, 
    SUM(sales.quantity) AS total_sold
FROM sales
JOIN products ON sales.productId = products.id
JOIN transactions ON sales.reference_no = transactions.reference
WHERE sales.status = 1 
  AND products.sellerId = '$user_id'
  AND transactions.message = 'Success'
GROUP BY sales.productId
ORDER BY total_sold DESC
LIMIT 1";
$most_sold_result = mysqli_query($connect, $query_most_sold);
$most_sold_data = mysqli_fetch_assoc($most_sold_result);
$most_sold_product = $most_sold_data['name'];
$most_sold_count = $most_sold_data['total_sold'];

// Fetch most rated product based on average rating
$query_most_rated = "
  SELECT products.name, AVG(ratings.rating) as avg_rating
  FROM ratings
  JOIN products ON ratings.productId = products.id
  WHERE products.sellerId = '$user_id'
  GROUP BY ratings.productId
  ORDER BY avg_rating DESC
  LIMIT 1";
$most_rated_result = mysqli_query($connect, $query_most_rated);
$most_rated_data = mysqli_fetch_assoc($most_rated_result);
$most_rated_product = $most_rated_data['name'];
$most_rated_avg = $most_rated_data['avg_rating'];

// Calculate pending withdrawals
$query_pending = "SELECT SUM(amount) as pending_amount FROM withdrawals 
                 WHERE sellerId = '$user_id' AND status = 0";
$pending_result = mysqli_query($connect, $query_pending);
$pending_data = mysqli_fetch_assoc($pending_result);
$pending_withdrawals = $pending_data['pending_amount'] ?? 0;

// Calculate disbursed withdrawals
$query_disbursed = "SELECT SUM(amount) as disbursed_amount FROM withdrawals 
                   WHERE sellerId = '$user_id' AND status = 1";
$disbursed_result = mysqli_query($connect, $query_disbursed);
$disbursed_data = mysqli_fetch_assoc($disbursed_result);
$disbursed_withdrawals = $disbursed_data['disbursed_amount'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<?php include "head/head.php" ?>

<body>
  <!-- ======= Header ======= -->
  <?php include "header/header.php" ?>
  <!-- ======= Sidebar ======= -->
  <?php include "aside/aside.php" ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashibodi</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Nyumbani</a></li>
          <li class="breadcrumb-item active">Dashibodi</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <div class="row">
          <!-- Total Registered Products Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #f0f6ff;">
                <h5 class="card-title">Jumla <span>| Bidhaa zilizosajiliwa</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #e0edff;">
                    <i class="bi bi-box-seam" style="color: #4154f1;"></i>
                  </div>
                  <div class="ps-3">
                    <h5><?php echo $total_products ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sales Total Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #e8f7e6;">
                <h5 class="card-title">Jumla <span>| Mauzo</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #d1f0cc;">
                    <i class="bi bi-cart-check" style="color: #2eca6a;"></i>
                  </div>
                  <div class="ps-3">
                    <h5><?php echo $total_sales ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Sales Amount Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #fff4e5;">
                <h5 class="card-title">Jumla <span>| Kiasi cha mauzo</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #ffe5c0;">
                    <i class="bi bi-cash-stack" style="color: #ff771d;"></i>
                  </div>
                  <div class="ps-3">
                    <h5>TSh. <?php echo number_format($total_sales_amount); ?>/=</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payable Amount Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #e0f8ff;">
                <h5 class="card-title">Kiasi <span>| kinachoweza kutolewa</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #c5f1ff;">
                    <i class="bi bi-wallet2" style="color: #0dcaf0;"></i>
                  </div>
                  <div class="ps-3">
                    <h5>TSh. <?php echo number_format($payable_amount, 2); ?>/=</h5>
                    <?php if ($payable_amount >= 50000) : ?>
                      <span class="badge bg-success text-white" onclick="openWithdrawPopup(<?php echo $payable_amount; ?>)"
                        style="cursor: pointer;">Bofya Hapa Ili Kutoa</span>
                    <?php else : ?>
                      <span class="badge bg-secondary">Kima cha chini cha TSh. 50,000 inahitajika</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Withdrawals Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #fff3cd;">
                <h5 class="card-title">Kiasi cha pesa <span>| Kinachosubiri kutolewa</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #ffe5bc;">
                    <i class="bi bi-hourglass-split" style="color: #ffa500;"></i>
                  </div>
                  <div class="ps-3">
                    <h5>TSh. <?php echo number_format($pending_withdrawals, 2); ?>/=</h5>
                    <?php if ($pending_withdrawals > 0) : ?>
                      <span class="badge bg-warning">Inasubiri Kuidhinishwa</span>
                    <?php else : ?>
                      <span class="badge bg-secondary">Hakuna Pesa Zinazosubiri</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Disbursed Withdrawals Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #d1e7dd;">
                <h5 class="card-title">Kiasi<span>| kilichotolewa</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #bcdcce;">
                    <i class="bi bi-check-circle" style="color: #198754;"></i>
                  </div>
                  <div class="ps-3">
                    <h5>TSh. <?php echo number_format($disbursed_withdrawals, 2); ?>/=</h5>
                    <?php if ($disbursed_withdrawals > 0) : ?>
                      <span class="badge bg-success">Kiasi kilichotolewa kikamilifu</span>
                    <?php else : ?>
                      <span class="badge bg-secondary">Hakuna Kiasi cha Pesa Zilizolipwa</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Most Sold Product Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #fff4e5;">
                <h5 class="card-title">Wengi <span>| Bidhaa Iliyouzwa</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #ffe5c0;">
                    <i class="bi bi-trophy" style="color: #ffa200;"></i>
                  </div>
                  <div class="ps-3">
                    <h5><?php echo $most_sold_product; ?> (<?php echo $most_sold_count; ?> sold)</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Most Rated Product Card -->
          <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
              <div class="card-body" style="background-color: #ffe5e5;">
                <h5 class="card-title">Bidhaa  <span>| Iliyokadiriwa Zaidi</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                    style="background-color: #ffd0d0;">
                    <i class="bi bi-star-fill" style="color: #ff0000;"></i>
                  </div>
                  <div class="ps-3">
                    <h5><?php echo $most_rated_product; ?> (Avg Rating: <?php echo number_format($most_rated_avg, 2); ?>)</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include "footer/footer.php" ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
  <!-- Add this script section -->
  <script>
    // In your openWithdrawPopup function, add this information:
    const popupContent = document.createElement('div');
    popupContent.innerHTML = `
    <h2 class="mb-4">Withdraw Funds</h2>
    <div class="alert alert-info" role="alert">
        <strong>Note:</strong> You have TSh. ${<?php echo $pending_withdrawals; ?>?.toLocaleString()} in pending withdrawals
    </div>
    <form id="withdrawForm">
    // ... rest of your existing form ...
`;

    function openWithdrawPopup(availableAmount) {
      // Create popup container
      const popupOverlay = document.createElement('div');
      popupOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 20px;
        box-sizing: border-box;
    `;

      // Create popup content
      const popupContent = document.createElement('div');
      popupContent.style.cssText = `
        background-color: white;
        max-width: 500px;
        width: 90%;
        border-radius: 10px;
        padding: 30px;
        position: relative;
    `;

      // Popup HTML content
      popupContent.innerHTML = `
        <h2 class="mb-4">Withdraw Funds</h2>
        <form id="withdrawForm">
            <div class="mb-3">
                <label for="availableAmount" class="form-label">Available Amount</label>
                <input type="text" class="form-control" id="availableAmount" value="TSh. ${availableAmount.toLocaleString()}/=" readonly>
            </div>
            <div class="mb-3">
                <label for="withdrawAmount" class="form-label">Withdrawal Amount</label>
                <input type="number" class="form-control" id="withdrawAmount" 
                       max="${availableAmount}" 
                       placeholder="Enter amount to withdraw">
                <small class="text-muted">Minimum withdrawal amount is TSh. 50,000</small>
            </div>
            <div class="mb-3">
                <label for="bankName" class="form-label">Bank Name</label>
                <input type="text" class="form-control" id="bankName" placeholder="Enter bank name">
            </div>
            <div class="mb-3">
                <label for="accountNumber" class="form-label">Account Number</label>
                <input type="text" class="form-control" id="accountNumber" placeholder="Enter account number">
            </div>
            <div class="mb-3">
                <label for="accountName" class="form-label">Account Name</label>
                <input type="text" class="form-control" id="accountName" placeholder="Enter account name">
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="closeWithdrawPopup()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitWithdrawal()">Withdraw</button>
            </div>
        </form>
    `;

      // Close button
      const closeButton = document.createElement('button');
      closeButton.innerHTML = '&times;';
      closeButton.style.cssText = `
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 30px;
        cursor: pointer;
        color: #333;
    `;
      closeButton.onclick = closeWithdrawPopup;

      // Add close button to popup
      popupContent.appendChild(closeButton);
      popupOverlay.appendChild(popupContent);

      // Add click outside to close
      popupOverlay.onclick = (e) => {
        if (e.target === popupOverlay) {
          closeWithdrawPopup();
        }
      };

      document.body.appendChild(popupOverlay);
    }

    function closeWithdrawPopup() {
      const popupOverlay = document.querySelector('div[style*="position: fixed"]');
      if (popupOverlay) {
        document.body.removeChild(popupOverlay);
      }
    }

    function submitWithdrawal() {
      const availableAmount = <?php echo $payable_amount; ?>;
      const withdrawAmount = document.getElementById('withdrawAmount').value;
      const bankName = document.getElementById('bankName').value;
      const accountNumber = document.getElementById('accountNumber').value;
      const accountName = document.getElementById('accountName').value;

      // Basic validation
      if (!withdrawAmount || !bankName || !accountNumber || !accountName) {
        alert('Please fill in all fields');
        return;
      }

      const numericWithdrawAmount = parseFloat(withdrawAmount);

      if (numericWithdrawAmount < 50000) {
        alert('Minimum withdrawal amount is TSh. 50,000');
        return;
      }

      if (numericWithdrawAmount > availableAmount) {
        alert('Withdrawal amount cannot exceed available balance');
        return;
      }

      const data = {
        amount: withdrawAmount,
        bankName: bankName,
        accountNumber: accountNumber,
        accountName: accountName,
        sellerId: <?php echo $user_id; ?>
      };

      console.log('Sending data:', data);

      fetch('withdraw.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data)
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Response:', data);
          if (data.status === 'success') {
            alert('Withdrawal request submitted successfully!');
            closeWithdrawPopup();
            location.reload();
          } else {
            alert('Withdrawal request failed: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred during withdrawal request: ' + error.message);
        });
    }
  </script>

</body>

</html>