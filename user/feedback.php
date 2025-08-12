<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for user feedback - in real app this would come from database
$userFeedbacks = [
    [
        'id' => 1,
        'type' => 'course_feedback',
        'title' => 'Ushauri wa Kozi ya Digital Marketing',
        'message' => 'Kozi hii ilikuwa nzuri sana. Nimejifunza mengi na nina ushauri wa kuboresha sehemu ya SEO...',
        'rating' => 5,
        'date_submitted' => '2024-03-15',
        'status' => 'reviewed',
        'response' => 'Asante kwa ushauri wako. Tumeuboresha sehemu ya SEO kama ulivyopendekeza.'
    ],
    [
        'id' => 2,
        'type' => 'platform_feedback',
        'title' => 'Uboreshaji wa Platform',
        'message' => 'Platform yenu ni nzuri lakini inahitaji kuboreshwa kwa upangaji wa kozi...',
        'rating' => 4,
        'date_submitted' => '2024-03-10',
        'status' => 'pending',
        'response' => null
    ],
    [
        'id' => 3,
        'type' => 'general_feedback',
        'title' => 'Ushauri wa Jumla',
        'message' => 'Nina ushauri wa kuongeza kozi za programming na web development...',
        'rating' => 5,
        'date_submitted' => '2024-03-08',
        'status' => 'reviewed',
        'response' => 'Ushauri wako umewezeshwa. Tumeanza kuandaa kozi za programming.'
    ]
];

$totalFeedbacks = count($userFeedbacks);
$reviewedFeedbacks = count(array_filter($userFeedbacks, function ($f) {
    return $f['status'] == 'reviewed';
}));
$pendingFeedbacks = count(array_filter($userFeedbacks, function ($f) {
    return $f['status'] == 'pending';
}));
$averageRating = array_sum(array_column($userFeedbacks, 'rating')) / count($userFeedbacks);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toa Mrejesho - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=3">
    <style>
        .feedback-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .feedback-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 188, 59, 0.2);
        }

        .feedback-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
        }

        .feedback-body {
            padding: 20px;
        }

        .rating-display {
            background: var(--secondary-color);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }

        .rating-stars {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .rating-stars .fas {
            color: var(--primary-color);
        }

        .feedback-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .feedback-item {
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .feedback-item:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feedback-type-badge {
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

        .status-reviewed {
            background: var(--primary-color);
            color: black;
        }

        .status-pending {
            background: var(--secondary-color);
            color: white;
        }

        .submit-feedback-btn {
            background: var(--primary-color);
            color: black;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .submit-feedback-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .response-section {
            background: rgba(255, 188, 59, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
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

        .star-rating {
            display: inline-block;
            direction: rtl;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ddd;
            padding: 0 2px;
        }

        .star-rating input:checked~label {
            color: var(--primary-color);
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
            color: var(--primary-color);
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
            $page_title = 'Toa Mrejesho';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-comment me-2" style="color: var(--primary-color);"></i>
                            Toa Mrejesho
                        </h1>
                        <p class="text-muted">Shiriki maoni yako na ushauri wa kuboresha huduma zetu</p>
                    </div>
                </div>

                <!-- Rating Overview -->
                <div class="rating-display">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $averageRating): ?>
                                <i class="fas fa-star"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <h3 class="mb-2">Wastani wa Ushauri: <?php echo number_format($averageRating, 1); ?>/5</h3>
                    <p class="mb-0">Umeitoa ushauri <?php echo $totalFeedbacks; ?> mara</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-comment fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalFeedbacks; ?></h3>
                                <p class="mb-0">Jumla ya Ushauri</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $reviewedFeedbacks; ?></h3>
                                <p class="mb-0">Ushauri Uliotazamwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $pendingFeedbacks; ?></h3>
                                <p class="mb-0">Ushauri Unasubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit New Feedback -->
                <div class="feedback-card">
                    <div class="feedback-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Toa Ushauri Mpya
                        </h4>
                    </div>
                    <div class="feedback-body">
                        <form class="feedback-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="feedbackType" class="form-label">Aina ya Ushauri</label>
                                    <select class="form-select" id="feedbackType" required>
                                        <option value="">Chagua aina</option>
                                        <option value="course_feedback">Ushauri wa Kozi</option>
                                        <option value="platform_feedback">Uboreshaji wa Platform</option>
                                        <option value="general_feedback">Ushauri wa Jumla</option>
                                        <option value="bug_report">Ripoti ya Tatizo</option>
                                        <option value="feature_request">Ombi la Kipengele</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="feedbackRating" class="form-label">Ushauri (Alama)</label>
                                    <div class="star-rating">
                                        <input type="radio" name="rating" id="star5" value="5">
                                        <label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="star4" value="4">
                                        <label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="star3" value="3">
                                        <label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="star2" value="2">
                                        <label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="star1" value="1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="feedbackTitle" class="form-label">Kichwa cha Ushauri</label>
                                <input type="text" class="form-control" id="feedbackTitle" placeholder="Andika kichwa cha ushauri..." required>
                            </div>
                            <div class="mb-3">
                                <label for="feedbackMessage" class="form-label">Ushauri Wako</label>
                                <textarea class="form-control" id="feedbackMessage" rows="4" placeholder="Andika ushauri wako hapa..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn submit-feedback-btn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Tuma Ushauri
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- My Feedback History -->
                <div class="feedback-card">
                    <div class="feedback-header">
                        <h4 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Historia ya Ushauri Wangu
                        </h4>
                    </div>
                    <div class="feedback-body">
                        <?php if (empty($userFeedbacks)): ?>
                            <div class="empty-state">
                                <i class="fas fa-comment"></i>
                                <h5>Hujaitoa ushauri wowote bado</h5>
                                <p class="text-muted">Anza kwa kutoa ushauri wa kwanza na usaidie kuboresha huduma zetu</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($userFeedbacks as $feedback): ?>
                                <div class="feedback-item">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="feedback-type-badge me-3">
                                                <?php
                                                switch ($feedback['type']) {
                                                    case 'course_feedback':
                                                        echo 'Kozi';
                                                        break;
                                                    case 'platform_feedback':
                                                        echo 'Platform';
                                                        break;
                                                    case 'general_feedback':
                                                        echo 'Jumla';
                                                        break;
                                                    case 'bug_report':
                                                        echo 'Tatizo';
                                                        break;
                                                    case 'feature_request':
                                                        echo 'Kipengele';
                                                        break;
                                                    default:
                                                        echo 'Nyingine';
                                                }
                                                ?>
                                            </span>
                                            <span class="status-badge status-<?php echo $feedback['status']; ?>">
                                                <?php
                                                if ($feedback['status'] == 'reviewed') {
                                                    echo 'Imetazamwa';
                                                } else {
                                                    echo 'Inasubiri';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= $feedback['rating']): ?>
                                                        <i class="fas fa-star" style="color: var(--primary-color);"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-muted"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($feedback['date_submitted'])); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <h6 class="mb-2"><?php echo htmlspecialchars($feedback['title']); ?></h6>
                                    <p class="mb-3"><?php echo htmlspecialchars($feedback['message']); ?></p>

                                    <?php if ($feedback['status'] == 'reviewed' && $feedback['response']): ?>
                                        <div class="response-section">
                                            <h6><i class="fas fa-reply me-2" style="color: var(--primary-color);"></i>Jibu la Timu</h6>
                                            <p class="mb-0"><?php echo htmlspecialchars($feedback['response']); ?></p>
                                        </div>
                                    <?php elseif ($feedback['status'] == 'pending'): ?>
                                        <div class="text-center py-2">
                                            <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Ushauri wako unasubiri kutatuliwa</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Feedback Tips -->
                <div class="feedback-card">
                    <div class="feedback-header">
                        <h4 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Ushauri wa Kutoa Ushauri
                        </h4>
                    </div>
                    <div class="feedback-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Jinsi ya Kutoa Ushauri Mzuri</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Toa maelezo ya kina</li>
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Eleza tatizo au pendekezo</li>
                                    <li><i class="fas fa-check me-2" style="color: var(--primary-color);"></i>Toa mifano ya vitendo</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>Mada za Ushauri</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Ubora wa kozi</li>
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Uboreshaji wa platform</li>
                                    <li><i class="fas fa-arrow-right me-2" style="color: var(--primary-color);"></i>Kozi mpya</li>
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
            alert('Ushauri wako umetumwa! Asante kwa kusaidia kuboresha huduma zetu.');
        });
    </script>
</body>

</html>