<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";
require_once __DIR__ . "/../models/Feedback.php";
require_once __DIR__ . "/../models/Opportunity.php";
require_once __DIR__ . "/../models/Beneficiary.php";
require_once __DIR__ . "/../models/Sales.php";
require_once __DIR__ . "/../models/Download.php";
require_once __DIR__ . "/../models/Business.php";
require_once __DIR__ . "/../models/Ranking.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();

// Initialize models
$blogModel = new Blog();
$feedbackModel = new Feedback();
$opportunityModel = new Opportunity();
$beneficiaryModel = new Beneficiary();
$salesModel = new Sales();
$downloadModel = new Download();
$businessModel = new Business();
$rankingModel = new Ranking();

// Get statistics
$blogStats = $blogModel->getOverallBlogStats();
$feedbackStats = $feedbackModel->getOverallFeedbackStats();
$opportunityStats = $opportunityModel->getOverallOpportunityStats();
$beneficiaryStats = $beneficiaryModel->getOverallBeneficiaryStats();
$salesStats = $salesModel->getOverallSalesStats();
$downloadStats = $downloadModel->getOverallDownloadStats();
$businessStats = $businessModel->getOverallBusinessStats();
$rankingStats = $rankingModel->getOverallRankingStats();

// Get recent activities
$recentBlogs = $blogModel->getRecentBlogs(5);
$recentFeedback = $feedbackModel->getAllFeedbackForAdmin(1, 5);
$recentOpportunities = $opportunityModel->getAllOpportunitiesForAdmin(1, 5);
$recentSales = $salesModel->getAllTransactionsForAdmin(null, null, 1, 5);
$topRankings = $rankingModel->getTopPerformers('monthly', 5);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Panda Digital</title>

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
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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

        .stats-card.danger {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
        }

        .quick-action-card {
            background: white;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .quick-action-card:hover {
            border-left-color: var(--secondary-color);
            background: #f8f9fa;
        }

        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .trend-indicator {
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
        }

        .trend-up {
            color: #2ecc71;
        }

        .trend-down {
            color: #e74c3c;
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
                <a class="nav-link active" href="/admin/dashboard.php">
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
                <a class="nav-link" href="/admin/rankings.php">
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
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h2 mb-2">
                            <i class="fas fa-sun me-2"></i>
                            Habari za Asubuhi, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                        </h1>
                        <p class="mb-0 opacity-75">
                            Karibu kwenye paneli ya usimamizi wa Panda Digital. Hapa unaweza kudhibiti na kufuatilia shughuli zote za mfumo.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="trend-indicator">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo date('l, d M Y'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $rankingStats['total_users'] ?? 0; ?></div>
                            <div class="metric-label">Watumiaji Waliojitolea</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $blogStats['total_blogs'] ?? 0; ?></div>
                            <div class="metric-label">Blog Zilizochapishwa</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $opportunityStats['total_opportunities'] ?? 0; ?></div>
                            <div class="metric-label">Fursa Zilizotolewa</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo number_format($salesStats['total_revenue'] ?? 0); ?></div>
                            <div class="metric-label">Jumla ya Mapato (TZS)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card danger">
                        <div class="card-body text-center">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $feedbackStats['total_feedback'] ?? 0; ?></div>
                            <div class="metric-label">Maoni Yaliyotolewa</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $beneficiaryStats['total_beneficiaries'] ?? 0; ?></div>
                            <div class="metric-label">Wenyenyeji</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-download fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $downloadStats['total_downloads'] ?? 0; ?></div>
                            <div class="metric-label">Mafaili Yaliyoshushwa</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <div class="metric-value"><?php echo $businessStats['verified'] ?? 0; ?></div>
                            <div class="metric-label">Biashara Zilizothibitishwa</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Mwelekeo wa Watumiaji
                        </h5>
                        <canvas id="userTrendsChart" height="100"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-pie text-success me-2"></i>
                            Usambazaji wa Biashara
                        </h5>
                        <canvas id="businessTypeChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and Recent Activities -->
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt text-warning me-2"></i>
                                Vitendo vya Haraka
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="quick-action-card p-3">
                                <a href="/admin/add-blog.php" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-plus-circle text-success me-3"></i>
                                        <div>
                                            <div class="fw-bold">Ongeza Blog</div>
                                            <small class="text-muted">Chapisha makala mpya</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="quick-action-card p-3">
                                <a href="/admin/feedback.php" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comments text-info me-3"></i>
                                        <div>
                                            <div class="fw-bold">Tazama Maoni</div>
                                            <small class="text-muted">Fuatilia maoni ya watumiaji</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="quick-action-card p-3">
                                <a href="/admin/opportunities.php" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-briefcase text-warning me-3"></i>
                                        <div>
                                            <div class="fw-bold">Fursa Mpya</div>
                                            <small class="text-muted">Ongeza fursa za biashara</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="quick-action-card p-3">
                                <a href="/admin/rankings.php" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-trophy text-primary me-3"></i>
                                        <div>
                                            <div class="fw-bold">Uratibu wa Nguvu</div>
                                            <small class="text-muted">Fuatilia alama za watumiaji</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock text-info me-2"></i>
                                Shughuli za Hivi Karibuni
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentBlogs)): ?>
                                <?php foreach ($recentBlogs as $blog): ?>
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon bg-success me-3">
                                                <i class="fas fa-blog"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Blog mpya imeongezwa</div>
                                                <div class="text-muted"><?php echo htmlspecialchars($blog['title']); ?></div>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('d M Y H:i', strtotime($blog['date_created'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($recentFeedback)): ?>
                                <?php foreach ($recentFeedback as $feedback): ?>
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon bg-info me-3">
                                                <i class="fas fa-comment"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Maoni mapya yamefika</div>
                                                <div class="text-muted"><?php echo htmlspecialchars(substr($feedback['feedback_text'], 0, 50)) . '...'; ?></div>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('d M Y H:i', strtotime($feedback['date_created'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($recentSales)): ?>
                                <?php foreach ($recentSales as $sale): ?>
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon bg-warning me-3">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Mauzo mapya</div>
                                                <div class="text-muted">TZS <?php echo number_format($sale['amount']); ?></div>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('d M Y H:i', strtotime($sale['date_created'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-crown text-warning me-2"></i>
                                Wanaoongoza wa Mwezi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($topRankings as $index => $performer): ?>
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="position-relative">
                                            <?php if ($index < 3): ?>
                                                <div class="position-absolute top-0 start-50 translate-middle">
                                                    <i class="fas fa-medal fa-2x text-<?php
                                                                                        echo $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger');
                                                                                        ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="ranking-icon mx-auto mb-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($performer['user_name']); ?></div>
                                            <div class="text-muted">Level <?php echo $performer['level']; ?></div>
                                            <div class="badge bg-success"><?php echo number_format($performer['total_score']); ?> pts</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // User Trends Chart
        const userCtx = document.getElementById('userTrendsChart').getContext('2d');
        new Chart(userCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Watumiaji Waliojitolea',
                    data: [120, 190, 300, 500, 200, 300],
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Business Type Chart
        const businessCtx = document.getElementById('businessTypeChart').getContext('2d');
        new Chart(businessCtx, {
            type: 'doughnut',
            data: {
                labels: ['Reja Reja', 'Jumla', 'Huduma', 'Uzalishaji', 'Kilimo', 'Teknolojia'],
                datasets: [{
                    data: [30, 25, 20, 15, 7, 3],
                    backgroundColor: [
                        '#662e91',
                        '#FFC10B',
                        '#27ae60',
                        '#e74c3c',
                        '#f39c12',
                        '#3498db'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>