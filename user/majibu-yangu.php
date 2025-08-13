<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();
$expertQuestionModel = new ExpertQuestion();

// Get user's quiz statistics from algorithm table
$quizStats = $quizModel->getUserQuizStats($currentUser['id']);

// Get user's quiz attempts
$quizAttempts = $quizModel->getUserQuizAttempts($currentUser['id'], 50);

// Get user's expert questions and answers
$userQuestions = $expertQuestionModel->getUserQuestions($currentUser['id'], 50);

// Calculate performance metrics
$totalAttempts = count($quizAttempts);
$totalQuestions = $quizStats['total_questions_answered'] ?? 0;
$correctAnswers = $quizStats['correct_answers'] ?? 0;
$averageScore = $quizStats['average_score'] ?? 0;
$completionRate = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

// Get performance by course
$coursePerformance = [];
foreach ($quizAttempts as $attempt) {
    $courseId = $attempt['course_id'];
    $courseName = $attempt['course_name'];

    if (!isset($coursePerformance[$courseId])) {
        $coursePerformance[$courseId] = [
            'name' => $courseName,
            'attempts' => 0,
            'total_score' => 0,
            'best_score' => 0,
            'last_attempt' => null
        ];
    }

    $coursePerformance[$courseId]['attempts']++;
    $coursePerformance[$courseId]['total_score'] += $attempt['score_percentage'];

    if ($attempt['score_percentage'] > $coursePerformance[$courseId]['best_score']) {
        $coursePerformance[$courseId]['best_score'] = $attempt['score_percentage'];
    }

    if (
        !$coursePerformance[$courseId]['last_attempt'] ||
        strtotime($attempt['date_created']) > strtotime($coursePerformance[$courseId]['last_attempt'] ?? '')
    ) {
        $coursePerformance[$courseId]['last_attempt'] = $attempt['date_created'];
    }
}

// Calculate average scores for each course
foreach ($coursePerformance as &$course) {
    $course['average_score'] = $course['attempts'] > 0 ?
        round($course['total_score'] / $course['attempts'], 1) : 0;
}

// Sort courses by average score (descending)
uasort($coursePerformance, function ($a, $b) {
    return $b['average_score'] <=> $a['average_score'];
});

// Get recent quiz results for detailed view
$recentResults = array_slice($quizAttempts, 0, 10);

// Calculate improvement trend
$improvementTrend = 0;
if (count($quizAttempts) >= 2) {
    $recentScores = array_slice(array_column($quizAttempts, 'score_percentage'), 0, 5);
    $olderScores = array_slice(array_column($quizAttempts, 'score_percentage'), -5);

    if (count($recentScores) > 0 && count($olderScores) > 0) {
        $recentAvg = array_sum($recentScores) / count($recentScores);
        $olderAvg = array_sum($olderScores) / count($olderScores);
        $improvementTrend = $olderAvg > 0 ? (($recentAvg - $olderAvg) / $olderAvg) * 100 : 0;
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Majibu Yangu - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        /* Additional styles for majibu yangu page */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stats-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 8px solid var(--primary-color);
        }

        .progress-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid var(--border-color);
            color: var(--secondary-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .table-hover tbody tr:hover {
            background: rgba(255, 188, 59, 0.05);
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge.bg-success {
            background: var(--success-color) !important;
        }

        .badge.bg-warning {
            background: var(--warning-color) !important;
        }

        .badge.bg-danger {
            background: var(--danger-color) !important;
        }

        .badge.bg-info {
            background: var(--info-color) !important;
        }

        .badge.bg-primary {
            background: var(--primary-color) !important;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.3;
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            margin-top: 1rem;
            font-weight: 600;
            color: var(--gray-color);
        }

        .empty-state p {
            color: var(--gray-color);
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Majibu Yangu';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Majibu Yangu na Uchambuzi
                        </h1>
                        <p class="text-muted">Tazama maendeleo yako, uchambuzi wa majibu, na takwimu za utendaji wako</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo $totalQuestions; ?></h3>
                                <p class="mb-0">Maswali Yaliyojibiwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo $correctAnswers; ?></h3>
                                <p class="mb-0">Majibu Sahihi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo round($completionRate, 1); ?>%</h3>
                                <p class="mb-0">Kiwango cha Usahihi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo round($averageScore, 1); ?>%</h3>
                                <p class="mb-0">Wastani wa Alama</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Overview -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Uchambuzi wa Utendaji</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($quizAttempts)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Kozi</th>
                                                    <th>Video</th>
                                                    <th>Alama</th>
                                                    <th>Tarehe</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($quizAttempts, 0, 10) as $result): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($result['course_name']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($result['video_title']); ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $result['score_percentage'] >= 80 ? 'success' : ($result['score_percentage'] >= 60 ? 'warning' : 'danger'); ?>">
                                                                <?php echo round($result['score_percentage'], 1); ?>%
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo date('d/m/Y H:i', strtotime($result['date_created'])); ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list"></i>
                                        <h5>Hakuna Matokeo Bado</h5>
                                        <p>Jisajili kwenye kozi na ujibu maswali ili kuona matokeo yako</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Lengo la Usahihi</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="progress-circle">
                                    <div class="progress-text"><?php echo round($completionRate); ?>%</div>
                                </div>
                                <p class="text-muted mb-0">
                                    Umejibu <strong><?php echo $totalQuestions; ?></strong> maswali
                                </p>
                                <?php if ($improvementTrend != 0): ?>
                                    <div class="mt-3">
                                        <?php if ($improvementTrend > 0): ?>
                                            <span class="badge bg-success">
                                                +<?php echo round($improvementTrend, 1); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <?php echo round($improvementTrend, 1); ?>%
                                            </span>
                                        <?php endif; ?>
                                        <small class="d-block text-muted mt-1">Mabadiliko ya Hivi Karibuni</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Expert Questions Section -->
                <?php if (!empty($userQuestions)): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Maswali Yangu kwa Wataalamu</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Swali</th>
                                                    <th>Jibu</th>
                                                    <th>Mtaalamu</th>
                                                    <th>Tarehe</th>
                                                    <th>Hali</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($userQuestions as $question): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($question['qn']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php if ($question['answer']): ?>
                                                                <?php echo htmlspecialchars(substr($question['answer'], 0, 100)) . '...'; ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">Bado haujibiwa</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($question['expert_name']): ?>
                                                                <?php echo htmlspecialchars($question['expert_name']); ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">Hajachaguliwa</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo date('d/m/Y H:i', strtotime($question['date_created'])); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <?php if ($question['answer']): ?>
                                                                <span class="badge bg-success">Imekamilika</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning">Inasubiri</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>