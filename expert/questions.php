<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();
$expertQuestionModel = new ExpertQuestion();

// Get pending questions
$pendingQuestions = $expertQuestionModel->getPendingQuestions($currentUser['id']);
$answeredQuestions = $expertQuestionModel->getAnsweredQuestions($currentUser['id']);
$expertStats = $expertQuestionModel->getExpertStats($currentUser['id']);

// Handle question answering
if ($_POST && isset($_POST['answer_question'])) {
    $questionId = $_POST['question_id'];
    $answer = trim($_POST['answer']);

    if (!empty($answer)) {
        $result = $expertQuestionModel->answerQuestion($questionId, $answer, $currentUser['id']);
        if ($result) {
            $success = "Swali limejibiwa kikamilifu!";
            // Refresh data
            $pendingQuestions = $expertQuestionModel->getPendingQuestions($currentUser['id']);
            $answeredQuestions = $expertQuestionModel->getAnsweredQuestions($currentUser['id']);
            $expertStats = $expertQuestionModel->getExpertStats($currentUser['id']);
        } else {
            $error = "Imefeli kujibu swali. Tafadhali jaribu tena.";
        }
    } else {
        $error = "Tafadhali ingiza jibu.";
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maswali ya Mtaalam - Panda Digital</title>

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

        .stat-card {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .stat-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .question-card {
            border-left: 4px solid var(--primary-color);
        }

        .question-card.answered {
            border-left-color: var(--success-color);
        }

        .question-card.pending {
            border-left-color: var(--warning-color);
        }

        .btn-answer {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
        }

        .btn-answer:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
            border-radius: 25px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/expert/dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>
                Panda Digital - Mtaalam
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/expert/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link active" href="/expert/questions.php">
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
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-question-circle text-primary me-2"></i>
                        Usimamizi wa Maswali
                    </h1>
                    <p class="text-muted">Jibu maswali kutoka kwa wanafunzi wako</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['total_questions']; ?></h3>
                            <p class="mb-0">Jumla ya Maswali</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['pending_questions']; ?></h3>
                            <p class="mb-0">Maswali Yaliyosubiri</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $expertStats['answered_questions']; ?></h3>
                            <p class="mb-0">Maswali Yaliyojibiwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card info">
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

            <!-- Questions Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="questionsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                <i class="fas fa-clock me-2"></i>
                                Maswali Yaliyosubiri (<?php echo count($pendingQuestions); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="answered-tab" data-bs-toggle="tab" data-bs-target="#answered" type="button" role="tab">
                                <i class="fas fa-check-circle me-2"></i>
                                Maswali Yaliyojibiwa (<?php echo count($answeredQuestions); ?>)
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="questionsTabsContent">
                        <!-- Pending Questions Tab -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <?php if (empty($pendingQuestions)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5>Hakuna maswali yaliyosubiri!</h5>
                                    <p class="text-muted">Wanafunzi wote wamepata majibu yao.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pendingQuestions as $question): ?>
                                    <div class="card question-card pending mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-user text-primary me-2"></i>
                                                        <?php echo htmlspecialchars($question['first_name'] . ' ' . $question['last_name']); ?>
                                                    </h6>
                                                    <p class="card-text">
                                                        <strong>Swali:</strong><br>
                                                        <?php echo nl2br(htmlspecialchars($question['qn'])); ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        <?php echo htmlspecialchars($question['phone']); ?> |
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d M Y H:i', strtotime($question['date_created'])); ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <button class="btn btn-answer text-white"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#answerModal"
                                                        data-question-id="<?php echo $question['id']; ?>"
                                                        data-question-text="<?php echo htmlspecialchars($question['qn']); ?>">
                                                        <i class="fas fa-reply me-2"></i>
                                                        Jibu Swali
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Answered Questions Tab -->
                        <div class="tab-pane fade" id="answered" role="tabpanel">
                            <?php if (empty($answeredQuestions)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                    <h5>Hakuna maswali yaliyojibiwa bado!</h5>
                                    <p class="text-muted">Jibu maswali yaliyosubiri ili kuona historia yako.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($answeredQuestions as $question): ?>
                                    <div class="card question-card answered mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <?php echo htmlspecialchars($question['first_name'] . ' ' . $question['last_name']); ?>
                                            </h6>
                                            <p class="card-text">
                                                <strong>Swali:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($question['qn'])); ?>
                                            </p>
                                            <div class="alert alert-success">
                                                <strong>Jibu lako:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($question['answer'])); ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?php echo htmlspecialchars($question['phone']); ?> |
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d M Y H:i', strtotime($question['date_created'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Answer Question Modal -->
    <div class="modal fade" id="answerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-reply text-primary me-2"></i>
                        Jibu Swali
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="question_id" id="modalQuestionId">

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Swali:</strong>
                            </label>
                            <div class="form-control-plaintext" id="modalQuestionText"></div>
                        </div>

                        <div class="mb-3">
                            <label for="answer" class="form-label">
                                <strong>Jibu lako:</strong>
                            </label>
                            <textarea class="form-control" id="answer" name="answer" rows="6"
                                placeholder="Andika jibu lako hapa..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Funga
                        </button>
                        <button type="submit" name="answer_question" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Tuma Jibu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Handle modal data
        document.addEventListener('DOMContentLoaded', function() {
            const answerModal = document.getElementById('answerModal');
            if (answerModal) {
                answerModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const questionId = button.getAttribute('data-question-id');
                    const questionText = button.getAttribute('data-question-text');

                    document.getElementById('modalQuestionId').value = questionId;
                    document.getElementById('modalQuestionText').textContent = questionText;
                });
            }
        });
    </script>
</body>

</html>