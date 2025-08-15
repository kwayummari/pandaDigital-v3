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

// Get all questions for this expert (pending and answered)
$allQuestions = $expertQuestionModel->getAllQuestionsByExpert($currentUser['id']);

// Set page title
$page_title = 'Ona Maswali Yote';
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
                            <h1 class="page-title">Ona Maswali Yote</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Ona Maswali</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <span class="badge bg-primary fs-6"><?= count($allQuestions) ?> Maswali Yote</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <!-- Filter Tabs -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="questionTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                            Yote (<?= count($allQuestions) ?>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                            Yaliyosubiriwa (<?= count(array_filter($allQuestions, fn($q) => $q['status'] == 0)) ?>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="answered-tab" data-bs-toggle="tab" data-bs-target="#answered" type="button" role="tab">
                                            Yaliyojibiwa (<?= count(array_filter($allQuestions, fn($q) => $q['status'] == 1)) ?>)
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Content -->
                <div class="tab-content" id="questionTabsContent">
                    <!-- All Questions Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <?php if (!empty($allQuestions)): ?>
                            <div class="row">
                                <?php foreach ($allQuestions as $question): ?>
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="card question-card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-question-circle text-primary me-2"></i>
                                                    Swali la <?= htmlspecialchars($question['student_name']) ?>
                                                </h6>
                                                <?php if ($question['status'] == 0): ?>
                                                    <span class="badge bg-warning">Inasubiriwa</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Imekwisha</span>
                                                <?php endif; ?>
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
                                                    <?php if ($question['status'] == 0): ?>
                                                        <a href="<?= app_url('admin/expert-answer-question.php?id=' . $question['id']) ?>" 
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fas fa-reply me-1"></i>
                                                            Jibu Swali
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= app_url('admin/expert-view-answer.php?id=' . $question['id']) ?>" 
                                                           class="btn btn-success btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            Tazama Jibu
                                                        </a>
                                                    <?php endif; ?>
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
                                            <h4 class="text-muted">Hakuna Maswali</h4>
                                            <p class="text-muted">Hivi sasa hakuna maswali yoyote.</p>
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

                    <!-- Pending Questions Tab -->
                    <div class="tab-pane fade" id="pending" role="tabpanel">
                        <?php 
                        $pendingQuestions = array_filter($allQuestions, fn($q) => $q['status'] == 0);
                        if (!empty($pendingQuestions)): 
                        ?>
                            <div class="row">
                                <?php foreach ($pendingQuestions as $question): ?>
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="card question-card h-100 border-warning">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-question-circle text-warning me-2"></i>
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
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="<?= app_url('admin/expert-answer-question.php?id=' . $question['id']) ?>" 
                                                       class="btn btn-warning btn-sm">
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
                                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                            <h4 class="text-success">Hakuna Maswali Yaliyosubiriwa</h4>
                                            <p class="text-muted">Maswali yote yamejibiwa!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Answered Questions Tab -->
                    <div class="tab-pane fade" id="answered" role="tabpanel">
                        <?php 
                        $answeredQuestions = array_filter($allQuestions, fn($q) => $q['status'] == 1);
                        if (!empty($answeredQuestions)): 
                        ?>
                            <div class="row">
                                <?php foreach ($answeredQuestions as $question): ?>
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="card question-card h-100 border-success">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-question-circle text-success me-2"></i>
                                                    Swali la <?= htmlspecialchars($question['student_name']) ?>
                                                </h6>
                                                <span class="badge bg-success">Imekwisha</span>
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
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="<?= app_url('admin/expert-view-answer.php?id=' . $question['id']) ?>" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        Tazama Jibu
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
                                            <h4 class="text-muted">Hakuna Maswali Yaliyojibiwa</h4>
                                            <p class="text-muted">Bado hujajibu maswali yoyote.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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
