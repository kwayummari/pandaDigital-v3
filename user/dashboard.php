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

// Get user's enrolled courses
$enrolledCourses = $courseModel->getUserEnrolledCourses($currentUser['id'], 5);

// Get user's quiz statistics
$quizStats = $quizModel->getUserQuizStats($currentUser['id']);

// Get user's expert questions
$userQuestions = $expertQuestionModel->getUserQuestions($currentUser['id'], 5);

// Calculate overall learning progress
$totalProgress = 0;
$totalCourses = count($enrolledCourses);
if ($totalCourses > 0) {
    foreach ($enrolledCourses as $course) {
        $courseProgress = $courseModel->calculateCourseProgress($currentUser['id'], $course['id']);
        $totalProgress += $courseProgress['completion_percentage'];
    }
    $overallProgress = $totalProgress / $totalCourses;
} else {
    $overallProgress = 0;
}

// Get recent quiz attempts
$recentQuizAttempts = $quizModel->getUserQuizAttempts($currentUser['id'], 5);

// Get courses user has actually interacted with (based on quiz activity)
$activeCourses = $courseModel->getUserActiveCourses($currentUser['id'], 5);

// Use active courses if no enrolled courses, otherwise use enrolled courses
$displayCourses = !empty($enrolledCourses) ? $enrolledCourses : $activeCourses;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panda Digital</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('images/logo/logo.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('images/logo/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo/logo.png') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logo/logo.png') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <!-- Custom CSS -->
    <style>
        .course-list-item {
            transition: background-color 0.2s ease;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .course-list-item:hover {
            background-color: #f8f9fa;
        }

        .course-list-item:last-child {
            border-bottom: none !important;
        }

        .course-thumbnail img {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-info h6 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .course-action .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        /* Mobile-specific dashboard fixes */
        @media (max-width: 768px) {
            .dashboard-container {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }

            .main-content {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }

            .content-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
                padding: 10px !important;
            }

            .row {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }

            .col-md-3,
            .col-md-6,
            .col-lg-8,
            .col-lg-4 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                padding: 15px !important;
                margin: 0 !important;
            }

            .card {
                width: 100% !important;
                max-width: 100% !important;
                margin: 5px 0 !important;
                overflow: hidden !important;
            }

            .stats-card {
                width: 100% !important;
                max-width: 100% !important;
                margin: 5px 0 !important;
            }

            .action-btn {
                width: 100% !important;
                max-width: 100% !important;
                margin: 5px 0 !important;
            }

            /* Fix Kozi Zako section layout on mobile */
            .course-list-item {
                flex-direction: column !important;
                align-items: flex-start !important;
                padding: 15px !important;
            }

            .course-thumbnail {
                margin-bottom: 10px !important;
                margin-right: 0 !important;
            }

            .course-info {
                margin-bottom: 10px !important;
                width: 100% !important;
            }

            .course-info h6 {
                max-width: 100% !important;
                margin-bottom: 5px !important;
            }

            .course-info p {
                margin-bottom: 10px !important;
            }

            .course-action {
                width: 100% !important;
                text-align: center !important;
            }

            .course-action .btn {
                width: 100% !important;
                margin-top: 5px !important;
            }
        }

        @media (max-width: 576px) {
            .content-wrapper {
                padding: 10px !important;
            }

            .col-md-3,
            .col-md-6,
            .col-lg-8,
            .col-lg-4 {
                padding: 10px !important;
            }

            .card {
                margin: 2px 0 !important;
            }

            .stats-card {
                margin: 2px 0 !important;
            }

            .action-btn {
                margin: 2px 0 !important;
            }

            /* Additional mobile fixes for Kozi Zako section */
            .course-list-item {
                padding: 10px !important;
            }

            .course-thumbnail {
                margin-bottom: 8px !important;
            }

            .course-info {
                margin-bottom: 8px !important;
            }

            .course-action .btn {
                padding: 8px 16px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="<?= app_url('user/dashboard.php') ?>" class="sidebar-brand">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span>Panda Digital</span>
                </a>
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar">
                    <i class="fas fa-user-circle fa-2x"></i>
                </div>
                <div class="user-info">
                    <h6 class="mb-0"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h6>
                    <small class="text-muted"><?php echo ucfirst($currentUser['role']); ?></small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item active">
                        <a href="<?= app_url('user/dashboard.php') ?>" class="nav-link">
                            <span>Nyumbani</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/courses.php') ?>" class="nav-link">
                            <span>Kozi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/majibu-yangu.php') ?>" class="nav-link">
                            <span>Majibu Yangu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/certificates.php') ?>" class="nav-link">
                            <span>Vyeti Vyagu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/business.php') ?>" class="nav-link">
                            <span>Biashara</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/ask-questions.php') ?>" class="nav-link">
                            <span>Uliza Maswali</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/profile.php') ?>" class="nav-link">
                            <i class="fas fa-user-edit me-2"></i>
                            <span>Badilisha Wasifu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/certificate-history.php') ?>" class="nav-link">
                            <span>Historia ya Vyeti</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/skill-level.php') ?>" class="nav-link">
                            <span>Daraja la Uwezo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/feedback.php') ?>" class="nav-link">
                            <span>Toa Mrejesho</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= app_url('logout.php') ?>" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Toka</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation Bar -->
            <nav class="top-navbar">
                <div class="nav-left">
                    <button class="sidebar-toggle d-lg-none" id="topSidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- <h4 class="page-title mb-0">Dashboard</h4> -->
                </div>
                <div class="nav-right">
                    <div class="user-dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($currentUser['first_name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= app_url('user/dashboard.php') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                            <li><a class="dropdown-item" href="<?= app_url('user/courses.php') ?>">
                                    <i class="fas fa-book me-2"></i>Kozi
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= app_url('logout.php') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Toka
                                </a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content-wrapper">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1 class="mb-3">
                        Karibu tena, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                    </h1>
                    <p class="lead mb-0">
                        Endelea na safari yako ya kujifunza na uendelee na maendeleo yako
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h4 class="mb-4">
                        Vitendo vya Haraka
                    </h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/courses.php') ?>" class="action-btn">
                                <span class="fw-bold">Tazama Kozi</span>
                                <small class="text-muted">Jisajili kwenye kozi mpya</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/courses.php#enrolled') ?>" class="action-btn">
                                <span class="fw-bold">Endelea Kusoma</span>
                                <small class="text-muted">Rudi kwenye kozi uliyosajiliwa</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/ask-questions.php') ?>" class="action-btn">
                                <span class="fw-bold">Uliza Swali</span>
                                <small class="text-muted">Pata msaada kutoka kwa mitaalam</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/profile.php') ?>" class="action-btn">
                                <span class="fw-bold">Wasifu Wangu</span>
                                <small class="text-muted">Badilisha maelezo yako</small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $totalCourses; ?></h3>
                                <p class="mb-0">Kozi Zilizosajiliwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $quizStats['videos_completed']; ?></h3>
                                <p class="mb-0">Masomo Yaliyokamilika</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo $quizStats['total_questions_answered']; ?></h3>
                                <p class="mb-0">Maswali Yaliyojibiwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card warning">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo round($quizStats['average_score'] ?? 0); ?>%</h3>
                                <p class="mb-0">Wastani wa Alama</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="mb-3">
                                    Maendeleo Yako ya Jumla
                                </h5>
                                <div class="progress-circle">
                                    <div class="progress-text"><?php echo round($overallProgress ?? 0); ?>%</div>
                                </div>
                                <p class="text-muted mb-0">
                                    Umeendelea na <strong><?php echo count($displayCourses); ?></strong> kozi
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    Mafanikio Yako
                                </h5>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="fw-bold fs-4" style="color: var(--primary-color);">
                                            <?php echo $quizStats['correct_answers']; ?>
                                        </div>
                                        <small class="text-muted">Majibu Sahihi</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold fs-4" style="color: var(--secondary-color);">
                                            <?php echo $quizStats['videos_completed']; ?>
                                        </div>
                                        <small class="text-muted">Masomo Yaliyokamilika</small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Usahihi wa Majibu</small>
                                        <small class="text-muted"><?php echo round($quizStats['average_score'] ?? 0); ?>%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $quizStats['average_score'] ?? 0; ?>%; background-color: var(--primary-color);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity and Enrolled Courses -->
                <div class="row">
                    <!-- Recent Activity -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    Shughuli za Hivi Karibuni
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentQuizAttempts) && empty($userQuestions)): ?>
                                    <div class="text-center py-4">
                                        <h6>Hakuna shughuli za hivi karibuni</h6>
                                        <p class="text-muted">Anza kujifunza ili uone shughuli zako hapa</p>
                                    </div>
                                <?php else: ?>
                                    <?php
                                    $allActivities = [];

                                    // Add quiz attempts
                                    foreach ($recentQuizAttempts as $attempt) {
                                        $allActivities[] = [
                                            'type' => 'quiz',
                                            'title' => 'Umejibu jaribio la ' . $attempt['video_title'],
                                            'description' => 'Alama: ' . $attempt['score_percentage'] . '%',
                                            'date' => $attempt['date_created'],
                                            'icon' => 'fas fa-question-circle',
                                            'color' => 'var(--primary-color)'
                                        ];
                                    }

                                    // Add expert questions
                                    foreach ($userQuestions as $question) {
                                        $status = $question['status'] == '1' ? 'Imejibiwa' : 'Inasubiri jibu';
                                        $allActivities[] = [
                                            'type' => 'question',
                                            'title' => 'Umeuliza swali kwa mtaalam',
                                            'description' => 'Hali: ' . $status,
                                            'date' => $question['date_created'],
                                            'icon' => 'fas fa-question-circle',
                                            'color' => $question['status'] == '1' ? 'var(--primary-color)' : 'var(--secondary-color)'
                                        ];
                                    }

                                    // Sort by date (newest first)
                                    usort($allActivities, function ($a, $b) {
                                        return strtotime($b['date']) - strtotime($a['date']);
                                    });

                                    // Show only first 5 activities
                                    $recentActivities = array_slice($allActivities, 0, 5);
                                    ?>

                                    <?php foreach ($recentActivities as $activity): ?>
                                        <div class="activity-item <?php echo $activity['type']; ?>">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                                    <p class="mb-1 text-muted"><?php echo htmlspecialchars($activity['description']); ?></p>
                                                    <small class="text-muted">
                                                        <?php echo date('d M Y H:i', strtotime($activity['date'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Courses -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    Kozi Zako
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($displayCourses)): ?>
                                    <div class="text-center py-4">
                                        <h6>Hujajisajili kwenye kozi yoyote</h6>
                                        <p class="text-muted">Jisajili kwenye kozi moja au zaidi ili uanze kujifunza</p>
                                        <a href="<?= app_url('user/courses.php') ?>" class="btn btn-primary-custom text-white">
                                            Tazama Kozi
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($displayCourses as $course): ?>
                                        <div class="course-list-item d-flex align-items-center p-2 border-bottom">
                                            <div class="course-thumbnail me-3">
                                                <?php if (!empty($course['photo'])): ?>
                                                    <img src="<?= app_url($courseModel->getImageUrl($course['photo'])) ?>"
                                                        alt="<?= htmlspecialchars($course['name']) ?>"
                                                        class="rounded"
                                                        style="width: 50px; height: 50px; object-fit: cover;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <!-- <div class="course-placeholder d-flex align-items-center justify-content-center rounded bg-light"
                                                        style="width: 50px; height: 50px; display: none;">
                                                        <i class="fas fa-book text-muted" style="font-size: 16px;"></i>
                                                    </div> -->
                                                <?php else: ?>
                                                    <div class="course-placeholder d-flex align-items-center justify-content-center rounded bg-light"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="fas fa-book text-muted" style="font-size: 16px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="course-info flex-grow-1">
                                                <h6 class="mb-1 text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($course['name']); ?></h6>
                                                <p class="mb-0 text-muted small">
                                                    <?php echo $course['total_videos']; ?> masomo • <?php echo $course['total_questions']; ?> maswali
                                                </p>
                                            </div>
                                            <div class="course-action">
                                                <a href="<?= app_url('user/course.php?id=' . $course['id']) ?>"
                                                    class="btn btn-outline-primary btn-sm">
                                                    Endelea
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if (count($displayCourses) >= 5): ?>
                                        <div class="text-center mt-3">
                                            <a href="<?= app_url('user/courses.php') ?>" class="btn btn-outline-primary btn-sm">
                                                Tazama Zote
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
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
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
                const sidebar = document.querySelector('.sidebar');
                const dashboardContainer = document.querySelector('.dashboard-container');

                sidebarToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        sidebar.classList.toggle('collapsed');
                        dashboardContainer.classList.toggle('sidebar-collapsed');
                    });
                });

                // Close sidebar on mobile when clicking outside
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
                        if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                            sidebar.classList.remove('collapsed');
                            dashboardContainer.classList.remove('sidebar-collapsed');
                        }
                    }
                });
            });
        </script>

        <!-- Profile Completion Modal -->
        <div class="modal fade" id="profileCompletionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kamilisha Profaili Yako</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeProfileModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tafadhali kamilisha maelezo yako ya msingi ili uweze kutumia huduma zote za jukwaa.
                        </div>

                        <form id="profileCompletionForm">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div id="first_nameGroup" class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="profileFirstName" class="form-label">Jina la Kwanza *</label>
                                    <input type="text" class="form-control" id="profileFirstName" name="first_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="profileLastName" class="form-label">Jina la Mwisho *</label>
                                    <input type="text" class="form-control" id="profileLastName" name="last_name">
                                </div>
                            </div>

                            <div id="phoneGroup" class="mb-3">
                                <label for="profilePhone" class="form-label">Namba ya Simu *</label>
                                <input type="tel" class="form-control" id="profilePhone" name="phone" placeholder="Mfano: 0712345678">
                            </div>

                            <div id="regionGroup" class="mb-3">
                                <label for="profileRegion" class="form-label">Mkoa *</label>
                                <select class="form-select" id="profileRegion" name="region">
                                    <option value="">Chagua Mkoa</option>
                                    <option value="Arusha">Arusha</option>
                                    <option value="Dar es Salaam">Dar es Salaam</option>
                                    <option value="Dodoma">Dodoma</option>
                                    <option value="Geita">Geita</option>
                                    <option value="Iringa">Iringa</option>
                                    <option value="Kagera">Kagera</option>
                                    <option value="Katavi">Katavi</option>
                                    <option value="Kigoma">Kigoma</option>
                                    <option value="Kilimanjaro">Kilimanjaro</option>
                                    <option value="Lindi">Lindi</option>
                                    <option value="Manyara">Manyara</option>
                                    <option value="Mara">Mara</option>
                                    <option value="Mbeya">Mbeya</option>
                                    <option value="Morogoro">Morogoro</option>
                                    <option value="Mtwara">Mtwara</option>
                                    <option value="Mwanza">Mwanza</option>
                                    <option value="Njombe">Njombe</option>
                                    <option value="Pemba North">Pemba North</option>
                                    <option value="Pemba South">Pemba South</option>
                                    <option value="Pwani">Pwani</option>
                                    <option value="Rukwa">Rukwa</option>
                                    <option value="Ruvuma">Ruvuma</option>
                                    <option value="Shinyanga">Shinyanga</option>
                                    <option value="Simiyu">Simiyu</option>
                                    <option value="Singida">Singida</option>
                                    <option value="Songwe">Songwe</option>
                                    <option value="Tabora">Tabora</option>
                                    <option value="Tanga">Tanga</option>
                                    <option value="Unguja North">Unguja North</option>
                                    <option value="Unguja South">Unguja South</option>
                                    <option value="Zanzibar Central">Zanzibar Central</option>
                                    <option value="Zanzibar Urban">Zanzibar Urban</option>
                                    <option value="Zanzibar West">Zanzibar West</option>
                                </select>
                            </div>

                            <div id="genderGroup" class="mb-3">
                                <label for="profileGender" class="form-label">Jinsia *</label>
                                <select class="form-select" id="profileGender" name="gender">
                                    <option value="">Chagua Jinsia</option>
                                    <option value="Mwanamke">Mwanamke</option>
                                    <option value="Mwanaume">Mwanaume</option>
                                </select>
                            </div>

                            <div id="dateOfBirthGroup" class="mb-3">
                                <label for="profileDateOfBirth" class="form-label">Tarehe ya Kuzaliwa *</label>
                                <input type="date" class="form-control" id="profileDateOfBirth" name="date_of_birth">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <span class="btn-text">Hifadhi Maelezo</span>
                                <span class="btn-loading d-none">
                                    <span class="loading"></span> Inahifadhi...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Container for Messages -->
        <div id="alertContainer" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;"></div>

        <!-- Profile Completion JavaScript -->
        <script>
            // Function to show/hide profile fields based on what's missing
            function showMissingFields(missingFields) {
                const allFields = ['first_name', 'last_name', 'phone', 'region', 'gender', 'date_of_birth'];

                allFields.forEach(field => {
                    const fieldGroup = document.getElementById(field + 'Group');
                    if (fieldGroup) {
                        if (missingFields.includes(field)) {
                            fieldGroup.style.display = 'block';
                            const input = fieldGroup.querySelector('input, select');
                            if (input) input.required = true;
                        } else {
                            fieldGroup.style.display = 'none';
                            const input = fieldGroup.querySelector('input, select');
                            if (input) input.required = false;
                        }
                    }
                });
            }

            // Function to show alerts
            function showAlert(type, message) {
                const alertContainer = document.getElementById('alertContainer');
                const alertId = 'alert-' + Date.now();

                const alertHtml = `
                    <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                alertContainer.insertAdjacentHTML('beforeend', alertHtml);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }

            // Check profile completion when page loads
            document.addEventListener('DOMContentLoaded', function() {
                checkProfileCompletion();
            });

            // Function to check profile completion by fetching fresh data from database
            function checkProfileCompletion() {
                // Fetch fresh user data from database
                fetch('<?= app_url("api/get-user-profile.php") ?>', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            console.log('Fetched fresh user data:', user);

                            // Check what fields are missing and show appropriate form
                            const missingFields = [];
                            if (!user.first_name || user.first_name === '' || user.first_name === 'null') missingFields.push('first_name');
                            if (!user.last_name || user.last_name === '' || user.last_name === 'null') missingFields.push('last_name');
                            if (!user.phone || user.phone === 'null' || user.phone === '' || user.phone === "'null'") missingFields.push('phone');
                            if (!user.region || user.region === 'null' || user.region === '' || user.region === "'null'") missingFields.push('region');
                            if (!user.gender || user.gender === 'null' || user.gender === '' || user.gender === "'null'") missingFields.push('gender');
                            if (!user.date_of_birth || user.date_of_birth === 'null' || user.date_of_birth === '' || user.date_of_birth === "'null'") missingFields.push('date_of_birth');

                            console.log('Missing fields:', missingFields);

                            if (missingFields.length > 0) {
                                console.log('Showing profile completion modal');
                                // Show profile completion modal
                                const profileModal = new bootstrap.Modal(document.getElementById('profileCompletionModal'));
                                profileModal.show();

                                // Show only the fields that are missing
                                showMissingFields(missingFields);

                                // Pre-fill existing data if available
                                if (user.first_name && user.first_name !== '' && user.first_name !== 'null') document.getElementById('profileFirstName').value = user.first_name;
                                if (user.last_name && user.last_name !== '' && user.last_name !== 'null') document.getElementById('profileLastName').value = user.last_name;
                                if (user.phone && user.phone !== 'null' && user.phone !== '' && user.phone !== "'null'") document.getElementById('profilePhone').value = user.phone;
                                if (user.region && user.region !== 'null' && user.region !== '' && user.region !== "'null'") document.getElementById('profileRegion').value = user.region;
                                if (user.gender && user.gender !== 'null' && user.gender !== '' && user.gender !== "'null'") document.getElementById('profileGender').value = user.gender;
                                if (user.date_of_birth && user.date_of_birth !== 'null' && user.date_of_birth !== '' && user.date_of_birth !== "'null'") document.getElementById('profileDateOfBirth').value = user.date_of_birth;
                            } else {
                                console.log('Profile is complete, no modal needed');
                            }
                        } else {
                            console.error('Failed to fetch user data:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching user data:', error);
                    });
            }

            // Handle profile completion form submission
            document.addEventListener('DOMContentLoaded', function() {
                const profileForm = document.getElementById('profileCompletionForm');
                if (profileForm) {
                    profileForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const btnText = submitBtn.querySelector('.btn-text');
                        const btnLoading = submitBtn.querySelector('.btn-loading');

                        // Show loading state
                        btnText.classList.add('d-none');
                        btnLoading.classList.remove('d-none');
                        submitBtn.disabled = true;

                        // Submit form data
                        fetch('<?= app_url("api/update-profile.php") ?>', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    showAlert('success', 'Maelezo yako yamehifadhiwa kwa mafanikio!');

                                    // Close modal after delay
                                    setTimeout(() => {
                                        const profileModal = bootstrap.Modal.getInstance(document.getElementById('profileCompletionModal'));
                                        profileModal.hide();
                                    }, 2000);

                                    // Reload page to reflect changes
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2500);
                                } else {
                                    showAlert('danger', data.message || 'Kuna tatizo, jaribu tena.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showAlert('danger', 'Kuna tatizo la mtandao, jaribu tena.');
                            })
                            .finally(() => {
                                // Reset button state
                                btnText.classList.remove('d-none');
                                btnLoading.classList.add('d-none');
                                submitBtn.disabled = false;
                            });
                    });
                }
            });
        </script>
</body>

</html>