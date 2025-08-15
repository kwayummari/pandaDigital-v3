<?php
session_start();
if (!isset($_SESSION['userEmail'], $_SESSION['userPhone'], $_SESSION['userFullName'], $_SESSION['isSeller'], $_SESSION['userId'])) {
    header("Location: ../../../../index.php");
    exit();
}
include '../connection/index.php';
$query = "
    SELECT 
        abuse.id, 
        abuse.reason, 
        abuse.description, 
        abuse.productId,
        products.name AS productName,
        products.status As productStatus
    FROM abuse
    JOIN products ON abuse.productId = products.id
    ORDER BY abuse.id DESC
";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../head/head2.php"; ?>

<body>
    <?php include "../header/header2.php"; ?>
    <?php include "../aside/aside2.php"; ?>
    <main id="main" class="main">
        <div class="col-12">
            <div class="card recent-sales overflow-auto">
                <div class="card-body">
                    <h5 class="card-title">All Reports <span>| Today</span></h5>
                    <table class="table table-borderless datatable">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Reasons</th>
                                <th scope="col">Description</th>
                                <th scope="col">Product</th>
                                <th scope="col">Product Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['productName']); ?></td>
                                    <td>
                                        <button
                                            class="btn btn-lg <?php echo $row['productStatus'] == 1 ? 'btn-success' : 'btn-danger'; ?>"
                                            onclick="toggleStatus(<?php echo $row['productId']; ?>, <?php echo $row['productStatus']; ?>)">
                                            <?php echo $row['productStatus'] == 1 ? 'Active' : 'Not Active'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            mysqli_free_result($result);
                            ?>
                        </tbody>
                    </table>
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
        function toggleStatus(productId, currentStatus) {
            if (confirm('Are you sure you want to change the status?')) {
                const newStatus = currentStatus === 1 ? 0 : 1;
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_status.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload(); // Reload the page to reflect the updated status
                    }
                };
                xhr.send('productId=' + productId + '&newStatus=' + newStatus);
            }
        }
    </script>
</body>

</html>
<?php
mysqli_close($connect);
?>