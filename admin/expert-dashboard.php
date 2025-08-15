<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Expert.php';
require_once __DIR__ . '/../models/ExpertQuestion.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Check if expert is authorized
if (!isset($currentUser['expert_authorization']) || $currentUser['expert_authorization'] != 1) {
    header('Location: ' . app_url('expert/pending-authorization.php'));
    exit();
}

$expertModel = new Expert();
$expertQuestionModel = new ExpertQuestion();

// Get expert statistics
$pendingQuestions = $expertQuestionModel->getPendingQuestionsByExpert($currentUser['id']);
$answeredQuestions = $expertQuestionModel->getAnsweredQuestionsByExpert($currentUser['id']);
$totalEarnings = $expertModel->getTotalEarnings($currentUser['id']);
$monthlyEarnings = $expertModel->getMonthlyEarnings($currentUser['id']);

// Set page title
$page_title = 'Expert Dashboard';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - <?= htmlspecialchars($appConfig['name']) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=8">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/includes/admin_top_nav.php'; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title">Expert Dashboard</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/dashboard.php') ?>">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Expert Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></span>
                                <span class="user-role">Mtaalam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= number_format($pendingQuestions) ?></h3>
                                        <p class="stat-label">Maswali Yaliyosubiriwa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= number_format($answeredQuestions) ?></h3>
                                        <p class="stat-label">Maswali Yaliyojibiwa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number">TSh <?= number_format($totalEarnings) ?></h3>
                                        <p class="stat-label">Jumla ya Mapato</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number">TSh <?= number_format($monthlyEarnings) ?></h3>
                                        <p class="stat-label">Mapato ya Mwezi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Vitendo vya Haraka</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= app_url('expert/questions.php') ?>" class="btn btn-primary w-100">
                                            <i class="fas fa-question-circle me-2"></i>
                                            Tazama Maswali
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= app_url('expert/answered-questions.php') ?>" class="btn btn-success w-100">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Maswali Yaliyojibiwa
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= app_url('expert/profile.php') ?>" class="btn btn-info w-100">
                                            <i class="fas fa-user-edit me-2"></i>
                                            Badilisha Profaili
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?= app_url('expert/earnings.php') ?>" class="btn btn-warning w-100">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Tazama Mapato
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Questions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Maswali ya Hivi Karibuni</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $recentQuestions = $expertQuestionModel->getRecentQuestionsByExpert($currentUser['id'], 5);
                                if (!empty($recentQuestions)):
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Swali</th>
                                                <th>Mwanafunzi</th>
                                                <th>Tarehe</th>
                                                <th>Hali</th>
                                                <th>Vitendo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentQuestions as $question): ?>
                                            <tr>
                                                <td>
                                                    <div class="question-text">
                                                        <?= htmlspecialchars(substr($question['question'], 0, 100)) ?>...
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($question['student_name']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($question['created_at'])) ?></td>
                                                <td>
                                                    <?php if ($question['status'] == 0): ?>
                                                        <span class="badge bg-warning">Inasubiriwa</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Imekwisha</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= app_url('expert/view-question.php?id=' . $question['id']) ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Tazama
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Hakuna maswali ya hivi karibuni</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="<?= asset('js/script.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
