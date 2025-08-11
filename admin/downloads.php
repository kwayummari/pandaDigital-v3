<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Download.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$downloadModel = new Download();

// Handle download actions
if ($_POST && isset($_POST['action'])) {
    $downloadId = $_POST['download_id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $result = $downloadModel->deleteDownload($downloadId);
        if ($result) {
            $success = "Rekodi ya kushusha imefutwa kikamilifu!";
        } else {
            $error = "Imefeli kufuta rekodi ya kushusha. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'block_user') {
        $result = $downloadModel->blockUserDownloads($_POST['user_id']);
        if ($result) {
            $success = "Mtumiaji amezuiwa kushusha faili!";
        } else {
            $error = "Imefeli kuzuia mtumiaji kushusha faili. Tafadhali jaribu tena.";
        }
    }
}

// Get date range for filtering
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get all downloads with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$downloads = $downloadModel->getAllDownloadsForAdmin($startDate, $endDate, $page, $perPage);
$totalDownloads = $downloadModel->getTotalDownloads($startDate, $endDate);
$totalPages = ceil($totalDownloads / $perPage);

// Get download statistics
$downloadStats = $downloadModel->getOverallDownloadStats($startDate, $endDate);
$topDownloadedFiles = $downloadModel->getTopDownloadedFiles($startDate, $endDate);
$downloadTrends = $downloadModel->getDownloadTrends($startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Kushusha Faili - Panda Digital</title>

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

        .downloads-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .downloads-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .downloads-table td {
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

        .download-icon {
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

        .file-type-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .download-count-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .top-files {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .file-item:last-child {
            border-bottom: none;
        }

        .file-rank {
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

        .file-rank.top-1 {
            background: #ffd700;
        }

        .file-rank.top-2 {
            background: #c0c0c0;
        }

        .file-rank.top-3 {
            background: #cd7f32;
        }

        .file-size {
            font-size: 0.9rem;
            color: #6c757d;
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
                <a class="nav-link" href="/admin/sales.php">
                    <i class="fas fa-chart-line me-1"></i> Mauzo
                </a>
                <a class="nav-link active" href="/admin/downloads.php">
                    <i class="fas fa-download me-1"></i> Kushusha
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
                        <i class="fas fa-download text-primary me-2"></i>
                        Usimamizi wa Kushusha Faili
                    </h1>
                    <p class="text-muted">Udhibiti rekodi za kushusha faili na vifaa vya mafunzo</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-download fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo number_format($downloadStats['total_downloads'] ?? 0); ?></h3>
                            <p class="mb-0">Kushusha Kote</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $downloadStats['unique_users'] ?? 0; ?></h3>
                            <p class="mb-0">Watumiaji Walioshusha</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-file fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $downloadStats['unique_files'] ?? 0; ?></h3>
                            <p class="mb-0">Faili Zilizoshushwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-hdd fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $downloadStats['total_size'] ?? 0; ?> MB</h3>
                            <p class="mb-0">Jumla ya Ukubwa</p>
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
                        <a href="/admin/downloads.php" class="btn btn-outline-secondary">
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
                            Mwelekeo wa Kushusha
                        </h5>
                        <canvas id="downloadTrendsChart" height="100"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="top-files">
                        <h5 class="mb-3">
                            <i class="fas fa-star text-warning me-2"></i>
                            Faili Zinazoshushwa Zaidi
                        </h5>
                        <?php foreach ($topDownloadedFiles as $index => $file): ?>
                            <div class="file-item">
                                <div class="d-flex align-items-center">
                                    <div class="file-rank <?php echo $index < 3 ? 'top-' . ($index + 1) : ''; ?> me-3">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($file['file_name']); ?></div>
                                        <small class="text-muted file-size"><?php echo $file['file_size']; ?> MB</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="download-count-badge">
                                        <?php echo $file['download_count']; ?> zilizoshushwa
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Downloads Table -->
            <div class="card downloads-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Kushusha
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Faili</th>
                                    <th>Mtumiaji</th>
                                    <th>Aina ya Faili</th>
                                    <th>Ukubwa</th>
                                    <th>IP Address</th>
                                    <th>Hali</th>
                                    <th>Tarehe</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($downloads as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#<?php echo $item['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="download-icon me-3">
                                                    <i class="fas fa-file-download"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo htmlspecialchars($item['file_name']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['file_path']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <span><?php echo htmlspecialchars($item['user_name'] ?? 'Mtumiaji'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="file-type-badge">
                                                <?php
                                                $extension = pathinfo($item['file_name'], PATHINFO_EXTENSION);
                                                switch (strtolower($extension)) {
                                                    case 'pdf':
                                                        echo 'PDF';
                                                        break;
                                                    case 'doc':
                                                    case 'docx':
                                                        echo 'Word';
                                                        break;
                                                    case 'xls':
                                                    case 'xlsx':
                                                        echo 'Excel';
                                                        break;
                                                    case 'ppt':
                                                    case 'pptx':
                                                        echo 'PowerPoint';
                                                        break;
                                                    case 'jpg':
                                                    case 'jpeg':
                                                    case 'png':
                                                        echo 'Picha';
                                                        break;
                                                    case 'mp4':
                                                    case 'avi':
                                                    case 'mov':
                                                        echo 'Video';
                                                        break;
                                                    case 'mp3':
                                                    case 'wav':
                                                        echo 'Sauti';
                                                        break;
                                                    case 'zip':
                                                    case 'rar':
                                                        echo 'Zipped';
                                                        break;
                                                    default:
                                                        echo strtoupper($extension);
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="download-count-badge">
                                                <?php echo $item['file_size']; ?> MB
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($item['ip_address'] ?? 'Haijulikani'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-status bg-<?php
                                                                                switch ($item['status'] ?? 'completed') {
                                                                                    case 'completed':
                                                                                        echo 'success';
                                                                                        break;
                                                                                    case 'failed':
                                                                                        echo 'danger';
                                                                                        break;
                                                                                    case 'cancelled':
                                                                                        echo 'warning';
                                                                                        break;
                                                                                    default:
                                                                                        echo 'success';
                                                                                }
                                                                                ?>">
                                                <?php
                                                switch ($item['status'] ?? 'completed') {
                                                    case 'completed':
                                                        echo 'Imekamilika';
                                                        break;
                                                    case 'failed':
                                                        echo 'Imefeli';
                                                        break;
                                                    case 'cancelled':
                                                        echo 'Imeghairiwa';
                                                        break;
                                                    default:
                                                        echo 'Imekamilika';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d M Y H:i', strtotime($item['date_created'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="viewDownload(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning"
                                                    onclick="blockUser(<?php echo $item['user_id']; ?>)">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteDownload(<?php echo $item['id']; ?>)">
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
                <nav aria-label="Downloads pagination" class="mt-4">
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
        // Download Trends Chart
        const ctx = document.getElementById('downloadTrendsChart').getContext('2d');
        const trendsData = <?php echo json_encode($downloadTrends); ?>;

        const labels = trendsData.map(item => item.date);
        const data = trendsData.map(item => item.downloads);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kushusha',
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
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // View download details
        function viewDownload(downloadId) {
            // This would typically load download details via AJAX
            alert('Tazama maelezo ya kushusha #' + downloadId);
        }

        // Block user downloads
        function blockUser(userId) {
            if (confirm('Je, una uhakika unataka kuzuia mtumiaji huyu kushusha faili? Unaweza kuondoa kizuizi baadaye.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="block_user">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete download record
        function deleteDownload(downloadId) {
            if (confirm('Je, una uhakika unataka kufuta rekodi hii ya kushusha? Kitendo hiki hakiwezi kubatilishwa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="download_id" value="${downloadId}">
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