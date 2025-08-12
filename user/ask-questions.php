<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for questions - in real app this would come from database
$userQuestions = [
    [
        'id' => 1,
        'question' => 'Je, ni njia zipi bora za kuanza biashara ya mtandaoni?',
        'category' => 'Biashara',
        'date_asked' => '2024-03-15',
        'status' => 'answered',
        'expert_name' => 'Dr. Mwambene',
        'answer' => 'Kuna njia nyingi za kuanza biashara ya mtandaoni. Unaweza kuanza na social media marketing, e-commerce, au huduma za digital...'
    ],
    [
        'id' => 2,
        'question' => 'Ninawezaje kuboresha SEO ya tovuti yangu?',
        'category' => 'Digital Marketing',
        'date_asked' => '2024-03-10',
        'status' => 'pending',
        'expert_name' => null,
        'answer' => null
    ],
    [
        'id' => 3,
        'question' => 'Je, ni mipango ipi ya malipo inayopendwa zaidi?',
        'category' => 'Biashara',
        'date_asked' => '2024-03-08',
        'status' => 'answered',
        'expert_name' => 'Mama Fatuma',
        'answer' => 'Mipango ya malipo inategemea aina ya biashara yako. M-Pesa na Airtel Money ni maarufu zaidi...'
    ]
];

$answeredQuestions = count(array_filter($userQuestions, function ($q) {
    return $q['status'] == 'answered';
}));
$pendingQuestions = count(array_filter($userQuestions, function ($q) {
    return $q['status'] == 'pending';
}));
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uliza Maswali - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-questions.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .question-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .question-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 188, 59, 0.2);
        }

        .question-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
        }

        .question-body {
            padding: 20px;
        }

        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-answered {
            background: var(--primary-color);
            color: black;
        }

        .status-pending {
            background: var(--secondary-color);
            color: white;
        }

        .ask-question-btn {
            background: var(--primary-color);
            color: black;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .ask-question-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .answer-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .expert-info {
            background: var(--secondary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
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
            $page_title = 'Uliza Maswali';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-question-circle me-2" style="color: var(--primary-color);"></i>
                            Uliza Maswali
                        </h1>
                        <p class="text-muted">Pata majibu kutoka kwa wataalam wa Panda Digital</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo count($userQuestions); ?></h3>
                                <p class="mb-0">Jumla ya Maswali</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $answeredQuestions; ?></h3>
                                <p class="mb-0">Maswali Yaliyojibiwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $pendingQuestions; ?></h3>
                                <p class="mb-0">Maswali Yanasubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ask New Question -->
                <div class="question-card">
                    <div class="question-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Uliza Swali Jipya
                        </h4>
                    </div>
                    <div class="question-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategoria</label>
                                    <select class="form-select" id="category" required>
                                        <option value="">Chagua kategoria</option>
                                        <option value="Biashara">Biashara</option>
                                        <option value="Digital Marketing">Digital Marketing</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="E-commerce">E-commerce</option>
                                        <option value="Nyingine">Nyingine</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label">Kipaumbele</label>
                                    <select class="form-select" id="priority">
                                        <option value="low">Chini</option>
                                        <option value="medium" selected>Kati</option>
                                        <option value="high">Juu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="question" class="form-label">Swali Lako</label>
                                <textarea class="form-control" id="question" rows="4" placeholder="Andika swali lako hapa..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn ask-question-btn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Tuma Swali
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- My Questions -->
                <div class="question-card">
                    <div class="question-header">
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Maswali Yangu
                        </h4>
                    </div>
                    <div class="question-body">
                        <?php if (empty($userQuestions)): ?>
                            <div class="empty-state">
                                <i class="fas fa-question-circle"></i>
                                <h5>Hujauliza swali lolote bado</h5>
                                <p class="text-muted">Anza kwa kuuliza swali la kwanza na upate majibu kutoka kwa wataalam</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($userQuestions as $question): ?>
                                <div class="question-item mb-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="category-badge me-3"><?php echo htmlspecialchars($question['category']); ?></span>
                                            <span class="status-badge status-<?php echo $question['status']; ?>">
                                                <?php
                                                if ($question['status'] == 'answered') {
                                                    echo 'Imekamilika';
                                                } else {
                                                    echo 'Inasubiri';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('d/m/Y', strtotime($question['date_asked'])); ?>
                                        </small>
                                    </div>

                                    <h6 class="mb-2"><?php echo htmlspecialchars($question['question']); ?></h6>

                                    <?php if ($question['status'] == 'answered' && $question['answer']): ?>
                                        <div class="answer-section">
                                            <div class="expert-info">
                                                <i class="fas fa-user-tie me-2"></i>
                                                Mtaalam: <?php echo htmlspecialchars($question['expert_name']); ?>
                                            </div>
                                            <p class="mb-0"><?php echo htmlspecialchars($question['answer']); ?></p>
                                        </div>
                                    <?php elseif ($question['status'] == 'pending'): ?>
                                        <div class="text-center py-3">
                                            <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Swali lako linasubiri majibu kutoka kwa mtaalam</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Help -->
                <div class="question-card">
                    <div class="question-header">
                        <h4 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Msaada wa Haraka
                        </h4>
                    </div>
                    <div class="question-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Maswali Yanayoulizwa Sana</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-chevron-right me-2" style="color: var(--primary-color);"></i>Jinsi ya kuanza biashara ya mtandaoni</li>
                                    <li><i class="fas fa-chevron-right me-2" style="color: var(--primary-color);"></i>Kuboresha SEO ya tovuti</li>
                                    <li><i class="fas fa-chevron-right me-2" style="color: var(--primary-color);"></i>Social media marketing</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>Mataalam Wetu</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-user-tie me-2" style="color: var(--primary-color);"></i>Dr. Mwambene - Biashara</li>
                                    <li><i class="fas fa-user-tie me-2" style="color: var(--primary-color);"></i>Mama Fatuma - Digital Marketing</li>
                                    <li><i class="fas fa-user-tie me-2" style="color: var(--primary-color);"></i>Bwana Juma - Social Media</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->


    <script>
        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Swali lako limetumwa! Mtaalam atakujibu hivi karibuni.');
        });
    </script>
</body>

</html>