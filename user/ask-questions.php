<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";

// Ensure user is logged in
$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$expertQuestionModel = new ExpertQuestion();

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // Validation
    if (empty($question)) {
        $message = 'Swali ni lazima';
        $messageType = 'danger';
    } else {
        if ($expertQuestionModel->askQuestion($currentUser['id'], $question, $category)) {
            $message = 'Swali lako limetumwa kwa mafanikio! Mtaalamu atakujibu hivi karibuni.';
            $messageType = 'success';

            // Clear form data
            $question = $category = '';
        } else {
            $message = 'Kuna tatizo la kiufundi. Jaribu tena.';
            $messageType = 'danger';
        }
    }
}

// Get user's questions
$userQuestions = $expertQuestionModel->getUserQuestions($currentUser['id']);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uliza Swali - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .question-item {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
        }

        .question-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .question-meta {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }



        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .expert-answer {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-top: 15px;
            border-radius: 0 8px 8px 0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-color);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/user_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Uliza Swali</h1>
                            <p class="text-muted">Uliza swali kwa wataalamu na upate majibu ya kitaalamu</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-question-circle text-primary me-2" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo count($userQuestions); ?></h3>
                                <p class="mb-0">Swali Zote</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo count(array_filter($userQuestions, function ($q) {
                                                        return $q['status'] === '1';
                                                    })); ?></h3>
                                <p class="mb-0">Yaliyojibiwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo count(array_filter($userQuestions, function ($q) {
                                                        return $q['status'] === '0';
                                                    })); ?></h3>
                                <p class="mb-0">Yanayosubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Ask Question Form -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-plus-circle text-primary me-2"></i>
                                    Uliza Swali Jipya
                                </h5>

                                <form method="POST" action="">
                                    <!-- Question -->
                                    <div class="mb-3">
                                        <label for="question" class="form-label">Swali Lako *</label>
                                        <textarea class="form-control" id="question" name="question" rows="4"
                                            placeholder="Andika swali lako hapa... Kwa mfano: Nifanyeje kuanza biashara ya mtandaoni?" required><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                                        <small class="text-muted">Eleza swali lako kwa ufasaha na uelewa</small>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Kategoria ya Swali</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">Chagua Kategoria (Si lazima)</option>
                                            <option value="business" <?php echo ($category ?? '') == 'business' ? 'selected' : ''; ?>>Biashara na Ujasiriamali</option>
                                            <option value="finance" <?php echo ($category ?? '') == 'finance' ? 'selected' : ''; ?>>Fedha na Uwekezaji</option>
                                            <option value="marketing" <?php echo ($category ?? '') == 'marketing' ? 'selected' : ''; ?>>Masoko na Utangazaji</option>
                                            <option value="technology" <?php echo ($category ?? '') == 'technology' ? 'selected' : ''; ?>>Teknolojia na Mtandao</option>
                                            <option value="legal" <?php echo ($category ?? '') == 'legal' ? 'selected' : ''; ?>>Sheria na Usimamizi</option>
                                            <option value="other" <?php echo ($category ?? '') == 'other' ? 'selected' : ''; ?>>Nyingine</option>
                                        </select>
                                        <small class="text-muted">Chagua kategoria ya swali (si lazima)</small>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Tumia Swali
                                    </button>
                                </form>

                                <!-- Tips -->
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Vidokezo</h6>
                                    <ul class="mb-0 small">
                                        <li>Andika swali kwa ufasaha na uelewa</li>
                                        <li>Chagua kategoria sahihi ya swali</li>
                                        <li>Mtaalamu atakujibu ndani ya siku 1-3</li>
                                        <li>Unaweza kuuliza swali nyingine kwa urahisi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-list text-primary me-2"></i>
                                    Swali Zangu
                                </h5>

                                <?php if (empty($userQuestions)): ?>
                                    <div class="empty-state">
                                        <i class="fas fa-question-circle"></i>
                                        <h5>Huna swali bado</h5>
                                        <p>Anza kwa kuuliza swali lako la kwanza kwa wataalamu wetu</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($userQuestions as $question): ?>
                                        <div class="question-item">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($question['qn']); ?></h6>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-<?php echo $question['status'] === '1' ? 'success' : 'warning'; ?> status-badge">
                                                        <?php echo $question['status'] === '1' ? 'Imekujibiwa' : 'Inasubiri'; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="question-meta">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($question['date_created'])); ?>
                                                </small>
                                            </div>

                                            <?php if ($question['status'] === '1' && !empty($question['answer'])): ?>
                                                <div class="expert-answer">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-user-tie text-primary me-2"></i>
                                                        <strong class="text-primary">Mtaalamu</strong>
                                                        <small class="text-muted ms-2">
                                                            <?php echo date('d/m/Y H:i', strtotime($question['date_created'])); ?>
                                                        </small>
                                                    </div>
                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($question['answer'])); ?></p>
                                                </div>
                                            <?php elseif ($question['status'] === '0'): ?>
                                                <div class="text-center py-3">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    <span class="text-muted">Swali lako linachambuliwa na mtaalamu. Utapata jibu hivi karibuni.</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>