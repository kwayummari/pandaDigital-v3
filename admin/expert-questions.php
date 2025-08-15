<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/ExpertQuestion.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Check if expert is authorized
if (!isset($currentUser['expert_authorization']) || $currentUser['expert_authorization'] != 1) {
    header('Location: ' . app_url('expert/pending-authorization.php'));
    exit();
}

$expertQuestionModel = new ExpertQuestion();

// Get pending questions for this expert
$pendingQuestions = $expertQuestionModel->getPendingQuestionsByExpert($currentUser['id']);

// Set page title
$page_title = 'Maswali Yaliyosubiriwa';
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
        <?php include __DIR__ . '/includes/expert_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/includes/admin_top_nav.php'; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title">Maswali Yaliyosubiriwa</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Maswali</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <span class="badge bg-warning fs-6"><?= count($pendingQuestions) ?> Maswali Yaliyosubiriwa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <?php if (!empty($pendingQuestions)): ?>
                    <div class="row">
                        <?php foreach ($pendingQuestions as $question): ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card question-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-question-circle text-primary me-2"></i>
                                            Swali la <?= htmlspecialchars($question['student_name']) ?>
                                        </h6>
                                        <span class="badge bg-warning">Inasubiriwa</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="question-content mb-3">
                                            <p class="question-text">
                                                <?= htmlspecialchars($question['question']) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="question-meta mb-3">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?= htmlspecialchars($question['student_name']) ?>
                                                    </small>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($question['created_at'])) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (!empty($question['category'])): ?>
                                            <div class="question-category mb-3">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <?= htmlspecialchars($question['category']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="<?= app_url('admin/expert-answer-question.php?id=' . $question['id']) ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-reply me-1"></i>
                                                Jibu Swali
                                            </a>
                                            <a href="<?= app_url('admin/expert-view-question.php?id=' . $question['id']) ?>" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye me-1"></i>
                                                Tazama
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">Hakuna Maswali Yaliyosubiriwa</h4>
                                    <p class="text-muted">Hivi sasa hakuna maswali yanayosubiri jibu lako.</p>
                                    <a href="<?= app_url('admin/expert-dashboard.php') ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Rudi Nyumbani
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="<?= asset('js/script.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
