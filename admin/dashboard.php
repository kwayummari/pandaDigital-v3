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
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";
require_once __DIR__ . "/../models/OngeaHub.php";
require_once __DIR__ . "/../models/Wanufaika.php";
require_once __DIR__ . "/../models/Fursa.php";
require_once __DIR__ . "/../models/Log.php";

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
$userModel = new User();
$courseModel = new Course();
$expertQuestionModel = new ExpertQuestion();
$ongeaHubModel = new OngeaHub();
$wanufaikaModel = new Wanufaika();
$fursaModel = new Fursa();
$logModel = new Log();

// Get statistics
$blogStats = $blogModel->getOverallBlogStats();
$feedbackStats = $feedbackModel->getOverallFeedbackStats();
$opportunityStats = $opportunityModel->getOverallOpportunityStats();
$beneficiaryStats = $beneficiaryModel->getOverallBeneficiaryStats();
$salesStats = $salesModel->getOverallSalesStats();
$downloadStats = $downloadModel->getOverallDownloadStats();
$businessStats = $businessModel->getOverallBusinessStats();
$rankingStats = $rankingModel->getOverallRankingStats();

// Get user statistics by role
$totalUsers = $userModel->getTotalUsers();
$userStatsByRole = $userModel->getUserStatsByRole();
$adminUsers = $userStatsByRole['admin'] ?? 0;
$studentUsers = $userStatsByRole['user'] ?? 0;
$expertUsers = $userStatsByRole['expert'] ?? 0;

// Get course and video statistics
$totalCourses = $courseModel->getTotalCourses();
$totalVideos = $courseModel->getTotalVideos();

// Get question statistics
$totalQuestions = $expertQuestionModel->getTotalQuestions();

// Get other statistics (using available methods)
$totalOngeaHub = 0; // Will implement when method is available
$totalWanufaika = 0; // Will implement when method is available
$totalFursa = 0; // Will implement when method is available
$totalNewsletter = 0; // Will implement when method is available
$totalLogs = 0; // Will implement when method is available

// Get recent activities
$recentBlogs = $blogModel->getRecentBlogs(5);
$recentFeedback = $feedbackModel->getAllFeedbackForAdmin(1, 5);
$recentOpportunities = $opportunityModel->getAllOpportunitiesForAdmin(1, 5);
$recentSales = $salesModel->getAllTransactionsForAdmin(null, null, 1, 5);
$topRankings = $rankingModel->getTopPerformers('monthly', 5);

// Calculate financial metrics if finance user
$financials = [];
if ($currentUser['email'] === 'finance@pandadigital.com') {
    $financials = $salesModel->getFinancialMetrics();
}
?>

<?php include __DIR__ . '/includes/admin_header.php'; ?>

<style>
    .action-card {
        background: linear-gradient(135deg, #662e91, #FFC10B) !important;
        color: white !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 46, 145, 0.3);
    }

    .action-card .card-icon {
        color: white !important;
    }

    .action-card .card-title {
        color: white !important;
    }

    .action-card .card-value {
        color: white !important;
    }

    .stats-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="mb-3">
        Karibu tena, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
    </h1>
    <p class="lead mb-0">
        Tazama takwimu na udhibiti mfumo wa Panda Digital
    </p>
</div>

<!-- Dashboard Content -->
<div class="row">
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
                <h6 class="card-title">Faida ya Kampuni (6%)</h6>
                <p class="card-value">TSh. <?= number_format($financials['company_profit'] ?? 0, 2) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/users/add-user.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h6 class="card-title">Sajili Mtumiaji Mpya</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="card-title">Watumiaji Wote</h6>
            <p class="card-value"><?= number_format($totalUsers) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h6 class="card-title">Utawala</h6>
            <p class="card-value"><?= number_format($adminUsers) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h6 class="card-title">Wanafunzi</h6>
            <p class="card-value"><?= number_format($studentUsers) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h6 class="card-title">Wataalamu</h6>
            <p class="card-value"><?= number_format($expertUsers) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/courses/add-course.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-book-medical"></i>
                </div>
                <h6 class="card-title">Sajili Kozi Mpya</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <h6 class="card-title">Kozi Zote</h6>
            <p class="card-value"><?= number_format($totalCourses) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/videos/add-video.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h6 class="card-title">Ongeza Video Mpya</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-video"></i>
            </div>
            <h6 class="card-title">Video Zote</h6>
            <p class="card-value"><?= number_format($totalVideos) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/questions/add-question.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h6 class="card-title">Ongeza Swali Jipya</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <h6 class="card-title">Maswali Yote</h6>
            <p class="card-value"><?= number_format($totalQuestions) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/feedback/view-feedback.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h6 class="card-title">Ona Mrejesho</h6>
                <p class="card-value"><i class="fas fa-eye"></i></p>
            </div>
        </a>
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

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/blog/write-blog.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h6 class="card-title">Andika Blogi</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-blog"></i>
            </div>
            <h6 class="card-title">Blogi Zote</h6>
            <p class="card-value"><?= number_format($blogStats['total_blogs'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/opportunities/add-opportunity.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h6 class="card-title">Andika Fursa</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h6 class="card-title">Fursa Zote</h6>
            <p class="card-value"><?= number_format($opportunityStats['total_opportunities'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="<?= app_url('admin/beneficiaries/add-beneficiary.php') ?>" class="text-decoration-none">
            <div class="stats-card position-relative action-card">
                <div class="card-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h6 class="card-title">Ongeza Wanufaika</h6>
                <p class="card-value"><i class="fas fa-plus"></i></p>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <h6 class="card-title">Wanufaika Wote</h6>
            <p class="card-value"><?= number_format($beneficiaryStats['total_beneficiaries'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-building"></i>
            </div>
            <h6 class="card-title">Biashara Zote</h6>
            <p class="card-value"><?= number_format($businessStats['total_businesses'] ?? 0) ?></p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h6 class="card-title">Mashindano</h6>
            <p class="card-value"><?= number_format($rankingStats['total_rankings'] ?? 0) ?></p>
        </div>
    </div>

    <!-- Message Balance Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card position-relative">
            <div class="card-icon">
                <i class="fas fa-sms"></i>
            </div>
            <h6 class="card-title">Salio la Meseji</h6>
            <p class="card-value">
                <?php
                // SMS Balance API call (similar to old dashboard)
                $username = '238b4b0ac1f3fbe1';
                $password = 'NTdjOWFlZTdlNDRhMDk5OWU4ZTU3NzFiYjU2YzMxNGM0MzE0YjViOThkMzM4MTVkOTJlMmQ3NjMwNzk3ZTdmMw==';
                $url = 'https://apisms.beem.africa/public/v1/vendors/balance';

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt_array($ch, array(
                    CURLOPT_HTTPGET => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:Basic ' . base64_encode("$username:$password"),
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($ch);
                $responseData = json_decode($response, true);
                $creditBalance = $responseData['data']['credit_balance'] ?? 'N/A';
                echo $creditBalance;
                curl_close($ch);
                ?>
            </p>
        </div>
    </div>


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