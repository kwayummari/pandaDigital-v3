<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Ranking.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$rankingModel = new Ranking();

// Handle ranking actions
if ($_POST && isset($_POST['action'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'recalculate_score') {
        $result = $rankingModel->recalculateUserScore($userId);
        if ($result) {
            $success = "Alama ya mtumiaji imehesabiwa upya!";
        } else {
            $error = "Imefeli kuhesabu upya alama ya mtumiaji. Tafadhali jaribu tena.";
        }
    } elseif ($action === 'adjust_score') {
        $adjustment = $_POST['score_adjustment'];
        $reason = $_POST['adjustment_reason'];
        $result = $rankingModel->adjustUserScore($userId, $adjustment, $reason);
        if ($result) {
            $success = "Alama ya mtumiaji imebadilishwa!";
        } else {
            $error = "Imefeli kubadilisha alama ya mtumiaji. Tafadhali jaribu tena.";
        }
    }
}

// Get ranking period
$period = $_GET['period'] ?? 'monthly'; // monthly, weekly, all_time

// Get all rankings with pagination
$page = $_GET['page'] ?? 1;
$perPage = 20;
$rankings = $rankingModel->getAllRankingsForAdmin($period, $page, $perPage);
$totalRankings = $rankingModel->getTotalRankings($period);
$totalPages = ceil($totalRankings / $perPage);

// Get ranking statistics
$rankingStats = $rankingModel->getOverallRankingStats($period);
$topPerformers = $rankingModel->getTopPerformers($period, 10);
$rankingTrends = $rankingModel->getRankingTrends($period);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usimamizi wa Uratibu wa Nguvu - Panda Digital</title>

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

        .rankings-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .rankings-table th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .rankings-table td {
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

        .ranking-icon {
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

        .rank-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .rank-1 {
            background: #ffd700;
        }

        .rank-2 {
            background: #c0c0c0;
        }

        .rank-3 {
            background: #cd7f32;
        }

        .rank-other {
            background: var(--primary-color);
        }

        .score-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .level-badge {
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

        .top-performers {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .performer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .performer-item:last-child {
            border-bottom: none;
        }

        .performer-rank {
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

        .performer-rank.top-1 {
            background: #ffd700;
        }

        .performer-rank.top-2 {
            background: #c0c0c0;
        }

        .performer-rank.top-3 {
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
                <a class="nav-link" href="/admin/sales.php">
                    <i class="fas fa-chart-line me-1"></i> Mauzo
                </a>
                <a class="nav-link" href="/admin/downloads.php">
                    <i class="fas fa-download me-1"></i> Kushusha
                </a>
                <a class="nav-link" href="/admin/businesses.php">
                    <i class="fas fa-building me-1"></i> Biashara
                </a>
                <a class="nav-link active" href="/admin/rankings.php">
                    <i class="fas fa-trophy me-1"></i> Uratibu
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
                        <i class="fas fa-trophy text-primary me-2"></i>
                        Usimamizi wa Uratibu wa Nguvu
                    </h1>
                    <p class="text-muted">Udhibiti alama na uratibu wa watumiaji</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalRankings; ?></h3>
                            <p class="mb-0">Watumiaji Waliojitolea</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-star fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $rankingStats['average_score'] ?? 0; ?></h3>
                            <p class="mb-0">Wastani wa Alama</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-crown fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $rankingStats['top_score'] ?? 0; ?></h3>
                            <p class="mb-0">Alama ya Juu</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-medal fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $rankingStats['active_users'] ?? 0; ?></h3>
                            <p class="mb-0">Watumiaji Waliojitolea</p>
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

            <!-- Period Filter -->
            <div class="search-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Tafuta mtumiaji...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="periodFilter" onchange="changePeriod(this.value)">
                            <option value="weekly" <?php echo $period == 'weekly' ? 'selected' : ''; ?>>Wiki Hii</option>
                            <option value="monthly" <?php echo $period == 'monthly' ? 'selected' : ''; ?>>Mwezi Huu</option>
                            <option value="all_time" <?php echo $period == 'all_time' ? 'selected' : ''; ?>>Muda Wote</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary-custom w-100" onclick="recalculateAllScores()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Hesabu Upya Alama
                        </button>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Mwelekeo wa Alama
                        </h5>
                        <canvas id="rankingTrendsChart" height="100"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="top-performers">
                        <h5 class="mb-3">
                            <i class="fas fa-crown text-warning me-2"></i>
                            Wanaoongoza
                        </h5>
                        <?php foreach ($topPerformers as $index => $performer): ?>
                            <div class="performer-item">
                                <div class="d-flex align-items-center">
                                    <div class="performer-rank <?php echo $index < 3 ? 'top-' . ($index + 1) : ''; ?> me-3">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($performer['user_name']); ?></div>
                                        <small class="text-muted">Level <?php echo $performer['level']; ?></small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="score-badge">
                                        <?php echo number_format($performer['total_score']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Rankings Table -->
            <div class="card rankings-table">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orodha ya Uratibu
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Uratibu</th>
                                    <th>Mtumiaji</th>
                                    <th>Alama</th>
                                    <th>Level</th>
                                    <th>Kozi</th>
                                    <th>Video</th>
                                    <th>Maswali</th>
                                    <th>Blog</th>
                                    <th>Vitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankings as $index => $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rank-badge <?php echo $index < 3 ? 'rank-' . ($index + 1) : 'rank-other'; ?> me-3">
                                                    <?php echo $index + 1; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ranking-icon me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo htmlspecialchars($item['user_name']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['email']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="score-badge">
                                                <?php echo number_format($item['total_score']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="level-badge">
                                                Level <?php echo $item['level']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $item['courses_completed'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo $item['videos_watched'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <?php echo $item['questions_answered'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo $item['blogs_published'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="viewUserDetails(<?php echo $item['user_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    onclick="recalculateScore(<?php echo $item['user_id']; ?>)">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning"
                                                    onclick="adjustScore(<?php echo $item['user_id']; ?>)">
                                                    <i class="fas fa-edit"></i>
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
                <nav aria-label="Rankings pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&period=<?php echo $period; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&period=<?php echo $period; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&period=<?php echo $period; ?>">
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
        // Ranking Trends Chart
        const ctx = document.getElementById('rankingTrendsChart').getContext('2d');
        const trendsData = <?php echo json_encode($rankingTrends); ?>;

        const labels = trendsData.map(item => item.date);
        const data = trendsData.map(item => item.average_score);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Wastani wa Alama',
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
                            stepSize: 100
                        }
                    }
                }
            }
        });

        // Change ranking period
        function changePeriod(period) {
            window.location.href = '/admin/rankings.php?period=' + period;
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // View user details
        function viewUserDetails(userId) {
            // This would typically load user details via AJAX
            alert('Tazama maelezo ya mtumiaji #' + userId);
        }

        // Recalculate user score
        function recalculateScore(userId) {
            if (confirm('Je, una uhakika unataka kuhesabu upya alama ya mtumiaji huyu?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="action" value="recalculate_score">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Adjust user score
        function adjustScore(userId) {
            const adjustment = prompt('Ingiza mabadiliko ya alama (mzuri au hasi):');
            if (adjustment !== null && !isNaN(adjustment)) {
                const reason = prompt('Sababu ya mabadiliko:');
                if (reason !== null) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="user_id" value="${userId}">
                        <input type="hidden" name="action" value="adjust_score">
                        <input type="hidden" name="score_adjustment" value="${adjustment}">
                        <input type="hidden" name="adjustment_reason" value="${reason}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        // Recalculate all scores
        function recalculateAllScores() {
            if (confirm('Je, una uhakika unataka kuhesabu upya alama za watumiaji wote? Hii inaweza kuchukua muda.')) {
                // This would typically trigger a background job
                alert('Kazi ya kuhesabu upya alama imeanza. Utapata taarifa ikimalizika.');
            }
        }
    </script>
</body>

</html>