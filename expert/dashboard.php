<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();
$expertQuestionModel = new ExpertQuestion();

// Get expert statistics
$expertStats = $expertQuestionModel->getExpertStats($currentUser['id']);

// Get pending questions
$pendingQuestions = $expertQuestionModel->getPendingQuestions($currentUser['id'], 5);

// Get recent answered questions
$recentAnswered = $expertQuestionModel->getAnsweredQuestions($currentUser['id'], 5);

// Calculate response time (average time to answer questions)
$responseTime = "N/A"; // This would need to be calculated based on actual data
$satisfactionRate = "N/A"; // This would need to be implemented

// Get today's date for greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Asubuhi njema";
} elseif ($hour < 17) {
    $greeting = "Mchana njema";
} else {
    $greeting = "Jioni njema";
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ya Mtaalam - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #8e44ad;
            --secondary-color: #9b59b6;
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
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
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

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--success-color) 0deg, #e9ecef 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--success-color);
        }

        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-5px);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .question-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: white;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .question-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .question-item.pending {
            border-left-color: var(--warning-color);
        }

        .question-item.answered {
            border-left-color: var(--success-color);
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

        .btn-warning-custom {
            background: var(--warning-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-warning-custom:hover {
            background: #d68910;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .expert-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/expert/dashboard.php">
                <i class="fas fa-user-graduate me-2"></i>
                Panda Digital - Mtaalam
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="/expert/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="/expert/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
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
                <div class="expert-badge d-inline-block mb-3">
                    <i class="fas fa-star me-2"></i>
                    Mtaalam Mwenye Uzoefu
                </div>
                <h1 class="mb-3">
                    <i class="fas fa-sun me-2"></i>
                    <?php echo $greeting; ?>, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                </h1>
                <p class="lead mb-0">
                    Endelea kusaidia wanafunzi na uendelee na maendeleo yako kama mtaalam
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h4 class="mb-4">
                    <i class="fas fa-bolt text-primary me-2"></i>
                    Vitendo vya Haraka
                </h4>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="/expert/questions.php" class="action-btn">
                            <i class="fas fa-question-circle"></i>
                            <span class="fw-bold">Jibu Maswali</span>
                            <small class="text-muted">Jibu maswali yaliyosubiri</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/expert/questions.php#answered" class="action-btn">
                            <i class="fas fa-history"></i>
                            <span class="fw-bold">Historia</span>
                            <small class="text-muted">Tazama maswali uliyoyajibu</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/expert/profile.php" class="action-btn">
                            <i class="fas fa-user-cog"></i>
                            <span class="fw-bold">Wasiliana Nasi</span>
                            <small class="text-muted">Badilisha maelezo yako</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/expert/analytics.php" class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span class="fw-bold">Takwimu</span>
                            <small class="text-muted">Tazama takwimu zako</small>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['total_questions']; ?></h3>
                            <p class="mb-0">Jumla ya Maswali</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['pending_questions']; ?></h3>
                            <p class="mb-0">Maswali Yaliyosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['answered_questions']; ?></h3>
                            <p class="mb-0">Maswali Yaliyojibiwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fa-2x mb-2"></i>
                            <h3 class="mb-1">
                                <?php
                                $percentage = $expertStats['total_questions'] > 0
                                    ? round(($expertStats['answered_questions'] / $expertStats['total_questions']) * 100)
                                    : 0;
                                echo $percentage . '%';
                                ?>
                            </h3>
                            <p class="mb-0">Asilimia ya Kujibiwa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="mb-3">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                Ufanisi Wako
                            </h5>
                            <div class="progress-circle" style="background: conic-gradient(var(--success-color) 0deg, var(--success-color) <?php echo $percentage * 3.6; ?>deg, #e9ecef <?php echo $percentage * 3.6; ?>deg, #e9ecef 360deg);">
                                <div class="progress-text"><?php echo $percentage; ?>%</div>
                            </div>
                            <p class="text-muted mb-0">
                                Umejibu <strong><?php echo $expertStats['answered_questions']; ?></strong> kati ya
                                <strong><?php echo $expertStats['total_questions']; ?></strong> maswali
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <i class="fas fa-trophy text-warning me-2"></i>
                                Takwimu za Mtaalam
                            </h5>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="text-success fw-bold fs-4">
                                        <?php echo $expertStats['answered_questions']; ?>
                                    </div>
                                    <small class="text-muted">Maswali Yaliyojibiwa</small>
                                </div>
                                <div class="col-6">
                                    <div class="text-primary fw-bold fs-4">
                                        <?php echo $expertStats['pending_questions']; ?>
                                    </div>
                                    <small class="text-muted">Maswali Yaliyosubiri</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Muda wa Kujibu</small>
                                    <small class="text-muted"><?php echo $responseTime; ?></small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">Wastani wa masaa 2</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Questions and Activity -->
            <div class="row">
                <!-- Pending Questions -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock text-warning me-2"></i>
                                Maswali Yaliyosubiri (<?php echo count($pendingQuestions); ?>)
                            </h5>
                            <a href="/expert/questions.php" class="btn btn-warning-custom text-white btn-sm">
                                <i class="fas fa-eye me-2"></i>
                                Tazama Yote
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pendingQuestions)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                    <h6>Hakuna maswali yaliyosubiri!</h6>
                                    <p class="text-muted">Wanafunzi wote wamepata majibu yao.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pendingQuestions as $question): ?>
                                    <div class="question-item pending">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($question['first_name'] . ' ' . $question['last_name']); ?>
                                                </h6>
                                                <p class="mb-2 text-muted small">
                                                    <?php echo htmlspecialchars(substr($question['qn'], 0, 100)) . '...'; ?>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d M Y H:i', strtotime($question['date_created'])); ?>
                                                </small>
                                            </div>
                                            <a href="/expert/questions.php" class="btn btn-warning-custom text-white btn-sm">
                                                <i class="fas fa-reply me-2"></i>
                                                Jibu
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Answered Questions -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Maswali Yaliyojibiwa Hivi Karibuni
                            </h5>
                            <a href="/expert/questions.php#answered" class="btn btn-primary-custom text-white btn-sm">
                                <i class="fas fa-eye me-2"></i>
                                Tazama Yote
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentAnswered)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-question-circle fa-2x text-muted mb-3"></i>
                                    <h6>Hujajibu maswali yoyote bado!</h6>
                                    <p class="text-muted">Anza kujibu maswali ili uone historia yako.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentAnswered as $question): ?>
                                    <div class="question-item answered">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($question['first_name'] . ' ' . $question['last_name']); ?>
                                                </h6>
                                                <p class="mb-2 text-muted small">
                                                    <?php echo htmlspecialchars(substr($question['qn'], 0, 100)) . '...'; ?>
                                                </p>
                                                <div class="alert alert-success py-2 mb-2">
                                                    <strong>Jibu lako:</strong><br>
                                                    <?php echo htmlspecialchars(substr($question['answer'], 0, 80)) . '...'; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d M Y H:i', strtotime($question['date_created'])); ?>
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

            <!-- Expert Tips and Motivation -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Vidokezo vya Mtaalam
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-clock text-primary fa-2x mb-3"></i>
                                <h6>Jibu Haraka</h6>
                                <p class="text-muted small">Jibu maswali kwa haraka iwezekanavyo ili uonekane kuwa mtaalam mwenye uzoefu.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-comments text-success fa-2x mb-3"></i>
                                <h6>Jibu Kwa Kina</h6>
                                <p class="text-muted small">Toa majibu ya kina na maelezo ya kutosha ili mwanafunzi afahamu vizuri.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-heart text-danger fa-2x mb-3"></i>
                                <h6>Uwe na Uvumilivu</h6>
                                <p class="text-muted small">Uwe na uvumilivu na uelewe kwamba kila mwanafunzi ana uwezo tofauti.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>