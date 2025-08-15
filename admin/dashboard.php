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

// Calculate financial metrics if finance user
$financials = [];
if ($currentUser['email'] === 'finance@pandadigital.com') {
    // $financials = $salesModel->getFinancialMetrics(); // Method not implemented yet
    $financials = [
        'total_income' => 0,
        'total_sales' => 0,
        'company_profit' => 0
    ];
}
?>

<?php include __DIR__ . '/includes/admin_header.php'; ?>

<!-- Dashboard Content -->
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="card-title">Jumla ya Watumiaj</h6>
            <p class="card-value"><?= number_format($blogStats['total_users'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <h6 class="card-title">Jumla ya Kozi</h6>
            <p class="card-value"><?= number_format($blogStats['total_courses'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-video"></i>
            </div>
            <h6 class="card-title">Jumla ya Video</h6>
            <p class="card-value"><?= number_format($blogStats['total_videos'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-download"></i>
            </div>
            <h6 class="card-title">Vyeti Vilivyopakuliwa</h6>
            <p class="card-value"><?= number_format($downloadStats['total_downloads'] ?? 0) ?></p>
        </div>
    </div>

    <?php if ($currentUser['email'] === 'finance@pandadigital.com'): ?>
        <!-- Financial Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h6 class="card-title">Jumla ya Mapato</h6>
                <p class="card-value">TSh. <?= number_format($financials['total_income'] ?? 0, 2) ?></p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h6 class="card-title">Jumla ya Mauzo</h6>
                <p class="card-value"><?= number_format($financials['total_sales'] ?? 0) ?></p>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6 class="card-title">Faida ya Kampuni</h6>
                <p class="card-value">TSh. <?= number_format($financials['company_profit'] ?? 0, 2) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Content Overview -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Shughuli Zaidi ya Hivi Karibuni</h5>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <?php if (!empty($recentBlogs)): ?>
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="fas fa-blog"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Blogi Mpya: <?= htmlspecialchars($recentBlogs[0]['title'] ?? '') ?></h6>
                                <p class="text-muted mb-0">Imeandikwa na <?= htmlspecialchars($recentBlogs[0]['author_name'] ?? 'Mtaalamu') ?></p>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($recentBlogs[0]['date_created'] ?? 'now')) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($recentFeedback)): ?>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Mrejesho Mpya: <?= htmlspecialchars($recentFeedback[0]['subject'] ?? '') ?></h6>
                                <p class="text-muted mb-0">Kutoka kwa <?= htmlspecialchars($recentFeedback[0]['student_name'] ?? 'Mwanafunzi') ?></p>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($recentFeedback[0]['date_created'] ?? 'now')) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($recentOpportunities)): ?>
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Fursa Mpya: <?= htmlspecialchars($recentOpportunities[0]['title'] ?? '') ?></h6>
                                <p class="text-muted mb-0">Imeandikwa na <?= htmlspecialchars($recentOpportunities[0]['author_name'] ?? 'Mtaalamu') ?></p>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($recentOpportunities[0]['date_created'] ?? 'now')) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($recentSales)): ?>
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Mauzo Mpya: TSh. <?= number_format($recentSales[0]['amount'] ?? 0, 2) ?></h6>
                                <p class="text-muted mb-0">Kutoka kwa <?= htmlspecialchars($recentSales[0]['customer_name'] ?? 'Mteja') ?></p>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($recentSales[0]['date'] ?? 'now')) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Vitendo vya Haraka</h5>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= app_url('admin/users/add-user.php') ?>" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Sajili Mtumiaji</span>
                    </a>

                    <a href="<?= app_url('admin/courses/add-course.php') ?>" class="action-btn">
                        <i class="fas fa-book-medical"></i>
                        <span>Ongeza Kozi</span>
                    </a>

                    <a href="<?= app_url('admin/blog/write-blog.php') ?>" class="action-btn">
                        <i class="fas fa-edit"></i>
                        <span>Andika Blogi</span>
                    </a>

                    <a href="<?= app_url('admin/feedback/view-feedback.php') ?>" class="action-btn">
                        <i class="fas fa-comments"></i>
                        <span>Ona Mrejesho</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="row">
    <!-- User Growth Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ukuaji wa Watumiaji</h5>
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Course Performance Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Utendaji wa Kozi</h5>
            </div>
            <div class="card-body">
                <canvas id="coursePerformanceChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Wanafunzi Bora wa Mwezi</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($topRankings)): ?>
                    <div class="top-performers">
                        <?php foreach ($topRankings as $index => $performer): ?>
                            <div class="performer-item">
                                <div class="performer-rank">
                                    <?php if ($index === 0): ?>
                                        <i class="fas fa-crown text-warning"></i>
                                    <?php elseif ($index === 1): ?>
                                        <i class="fas fa-medal text-secondary"></i>
                                    <?php elseif ($index === 2): ?>
                                        <i class="fas fa-award text-bronze"></i>
                                    <?php else: ?>
                                        <span class="rank-number"><?= $index + 1 ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="performer-info">
                                    <h6 class="mb-1"><?= htmlspecialchars($performer['student_name']) ?></h6>
                                    <p class="text-muted mb-0">Alama: <?= $performer['score'] ?>%</p>
                                </div>
                                <div class="performer-score">
                                    <span class="badge bg-primary"><?= $performer['correct_answers'] ?> / <?= $performer['total_questions'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mt-3">Hakuna data ya utendaji bado</h6>
                        <p class="text-muted">Wanafunzi bado hawajaanza kufanya mitihani</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Watumiaji Wapya',
                data: [65, 78, 90, 105, 120, 135],
                borderColor: '#662e91',
                backgroundColor: 'rgba(102, 46, 145, 0.1)',
                tension: 0.4,
                fill: true
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
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Course Performance Chart
    const coursePerformanceCtx = document.getElementById('coursePerformanceChart').getContext('2d');
    new Chart(coursePerformanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Imekamilika', 'Inaendelea', 'Haijaanza'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#27ae60', '#f39c12', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>