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

// Get answered questions for this expert
$answeredQuestions = $expertQuestionModel->getAnsweredQuestionsByExpert($currentUser['id']);

// Set page title
$page_title = 'Maswali Yaliyojibiwa';
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
                            <h1 class="page-title">Maswali Yaliyojibiwa</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Waliojibiwa</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <span class="badge bg-success fs-6"><?= count($answeredQuestions) ?> Maswali Yaliyojibiwa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <?php if (!empty($answeredQuestions)): ?>
                    <div class="row">
                        <?php foreach ($answeredQuestions as $question): ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="card question-card h-100 border-success">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Swali la <?= htmlspecialchars($question['student_name']) ?>
                                        </h6>
                                        <span class="badge bg-success">Imekwisha</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="question-content mb-3">
                                            <h6 class="text-muted mb-2">Swali:</h6>
                                            <p class="question-text">
                                                <?= htmlspecialchars($question['question']) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="answer-content mb-3">
                                            <h6 class="text-success mb-2">Jibu Lako:</h6>
                                            <div class="answer-text bg-light p-3 rounded">
                                                <?= htmlspecialchars($question['answer'] ?? 'Jibu halijulikani') ?>
                                            </div>
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

                                        <?php if (!empty($question['answered_at'])): ?>
                                            <div class="answer-meta mb-3">
                                                <small class="text-success">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Ulijibu: <?= date('d/m/Y H:i', strtotime($question['answered_at'])) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="<?= app_url('admin/expert-view-answer.php?id=' . $question['id']) ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-eye me-1"></i>
                                                Tazama Jibu
                                            </a>
                                            <a href="<?= app_url('admin/expert-edit-answer.php?id=' . $question['id']) ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                Badilisha Jibu
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
                                    <h4 class="text-muted">Hakuna Maswali Yaliyojibiwa</h4>
                                    <p class="text-muted">Bado hujajibu maswali yoyote. Anza kwa kujibu maswali yaliyosubiriwa.</p>
                                    <a href="<?= app_url('admin/expert-questions.php') ?>" class="btn btn-primary">
                                        <i class="fas fa-question-circle me-2"></i>
                                        Tazama Maswali Yaliyosubiriwa
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
