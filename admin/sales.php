<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Sales.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$salesModel = new Sales();

// Handle sales actions
if ($_POST && isset($_POST['action'])) {
    $transactionId = $_POST['transaction_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $salesModel->deleteTransaction($transactionId);
        if ($result) {
            $success = "Muamala umefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta muamala. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'update_status') {
        $status = $_POST['status'];
        $result = $salesModel->updateTransactionStatus($transactionId, $status);
        if ($result) {
            $success = "Hali ya muamala imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha hali ya muamala. Tafadhali jaribu tena.";
        }
    }
}

// Get date range for filtering
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get all transactions with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$transactions = $salesModel->getAllTransactionsForAdmin($startDate, $endDate, $page, $perPage);
$totalTransactions = $salesModel->getTotalTransactions($startDate, $endDate);
$totalPages = ceil($totalTransactions / $perPage);

// Get sales statistics
$salesStats = $salesModel->getOverallSalesStats($startDate, $endDate);
$monthlyStats = $salesModel->getMonthlySalesStats();
$topProducts = $salesModel->getTopSellingProducts($startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Mauzo - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #662e91;
            --secondary-color: #FFC10B;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .sales-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .sales-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .sales-table td {
            vertical-align: middle;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .transaction-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .amount-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .payment-method-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .top-products {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-rank {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .product-rank.top-1 {
            background: #ffd700;
        }

        .product-rank.top-2 {
            background: #c0c0c0;
        }

        .product-rank.top-3 {
            background: #cd7f32;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Panda Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
                </a>
                <a class="nav-link" href="/admin/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
                </a>
                <a class="nav-link" href="/admin/blogs.php">
                    <i class="fas fa-blog me-1"></i> Blog
                </a>
                <a class="nav-link" href="/admin/feedback.php">
                    <i class="fas fa-comments me-1"></i> Maoni
                </a>
                <a class="nav-link" href="/admin/opportunities.php">
                    <i class="fas fa-briefcase me-1"></i> Fursa
                </a>
                <a class="nav-link" href="/admin/beneficiaries.php">
                    <i class="fas fa-users me-1"></i> Wenyenyeji
                </a>
                <a class="nav-link active" href="/admin/sales.php">
                    <i class="fas fa-chart-line me-1"></i> Mauzo
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Usimamizi wa Mauzo na Fedha
                    </h1>
                    <p class="text-muted">Udhibiti mauzo, muamala, na ripoti za fedha</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <h3 class="mb-1">TZS <?php echo number_format($salesStats['total_revenue'] ?? 0); ?></h3>
                            <p class="mb-0">Jumla ya Mapato</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $salesStats['total_transactions'] ?? 0; ?></h3>
                            <p class="mb-0">Muamala Yote</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $salesStats['unique_customers'] ?? 0; ?></h3>
                            <p class="mb-0">Wateja Waliojitolea</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <h3 class="mb-1">TZS <?php echo number_format($salesStats['average_order'] ?? 0); ?></h3>
                            <p class="mb-0">Wastani wa Muamala</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Date Range Filter -->
            <div class="search-box">
                <form method="GET" action="" class="row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tarehe ya Mwanzo</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="<?php echo htmlspecialchars($startDate); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tarehe ya Mwisho</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="<?php echo htmlspecialchars($endDate); ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-custom me-2">
                            <i class="fas fa-filter me-2"></i>
                            Chagua Tarehe
                        </button>
                        <a href="/admin/sales.php" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i>
                            Safisha
                        </a>
                    </div>
                </form>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Mapato ya Mwezi
                        </h5>
                        <canvas id="monthlyRevenueChart" height="100"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="top-products">
                        <h5 class="mb-3">
                            <i class="fas fa-star text-warning me-2"></i>
                            Bidhaa Zinazouzwa Zaidi
                        </h5>
                        <?php foreach ($topProducts as $index => $product): ?>
                            <div class="product-item">
                                <div class="d-flex align-items-center">
                                    <div class="product-rank <?php echo $index < 3 ? 'top-' . ($index + 1) : ''; ?> me-3">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                        <small class="text-muted"><?php echo $product['total_sold']; ?> zilizouzwa</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="amount-badge">
                                        TZS <?php echo number_format($product['total_revenue']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card sales-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Muamala
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Muamala</th>
                                    <th>Mteja</th>
                                    <th>Bidhaa</th>
                                    <th>Kiasi</th>
                                    <th>Njia ya Malipo</th>
                                    <th>Hali</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="transaction-icon me-3">
                                                    <i class="fas fa-receipt"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo htmlspecialchars($item['transaction_id']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['description'] ?? 'Muamala wa kawaida'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span><?php echo htmlspecialchars($item['customer_name'] ?? 'Mteja'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($item['product_name'] ?? 'Bidhaa'); ?>
                                        </td>
                                        <td>
                                            <span class="amount-badge">
                                                TZS <?php echo number_format($item['amount']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="payment-method-badge">
                                                <?php
                                                switch ($item['payment_method'] ?? 'unknown') {
                                                    case 'mpesa':
                                                        echo 'M-Pesa';
                                                        break;
                                                    case 'card':
                                                        echo 'Kadi';
                                                        break;
                                                    case 'bank':
                                                        echo 'Benki';
                                                        break;
                                                    case 'cash':
                                                        echo 'Pesa Taslimu';
                                                        break;
                                                    default:
                                                        echo 'Haijulikani';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($item['status'] ?? 'pending') {
                                                                                    case 'completed':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    case 'failed':
                                                                                        echo 'danger';
                                                                                        break;
                                                                                    case 'refunded':
                                                                                        echo 'warning';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'warning';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'pending') {
                                                    case 'completed':
                                                        echo 'Imekamilika';
                                                        break;
                                                    case 'failed':
                                                        echo 'Imefeli';
                                                        break;
                                                    case 'refunded':
                                                        echo 'Imerejeshwa';
                                                        break;
                                                    default:
                                                        echo 'Inasubiri';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($item['date_created'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="viewTransaction(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="editTransaction(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteTransaction(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Transactions pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Monthly Revenue Chart
        const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyData = <?php echo json_encode($monthlyStats); ?>;

        const labels = monthlyData.map(item => item.month);
        const data = monthlyData.map(item => item.revenue);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Mapato (TZS)',
                    data: data,
                    borderColor: '#662e91',
                    backgroundColor: 'rgba(102, 46, 145, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TZS ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // View transaction details
        function viewTransaction(transactionId) {
            // This would typically load transaction details via AJAX
            alert('Tazama maelezo ya muamala #' + transactionId);
        }

        // Edit transaction
        function editTransaction(transactionId) {
            // This would typically redirect to edit page
            window.location.href = '/admin/edit-transaction.php?id=' + transactionId;
        }

        // Delete transaction
        function deleteTransaction(transactionId) {
            if (confirm('Je, una uhakika unataka kufuta muamala huu? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="transaction_id" value="${transactionId}">
                    <input type="hidden" name="action" value="delete">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Date range validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('Tarehe ya mwanzo haiwezi kuwa baada ya tarehe ya mwisho.');
                return false;
            }
        });
    </script>
</body>

</html>