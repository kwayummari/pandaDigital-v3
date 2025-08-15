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
    </style>


    cursor: pointer;
    padding: 5px;
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
                    <h4 class="page-title mb-0">Dashboard</h4>
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
                            <a href="<?= app_url('uliza-swali.php') ?>" class="action-btn">
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
                                <h3 class="mb-1"><?php echo round($quizStats['average_score']); ?>%</h3>
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
                                    <div class="progress-text"><?php echo round($overallProgress); ?>%</div>
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
                                        <small class="text-muted"><?php echo round($quizStats['average_score']); ?>%</small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $quizStats['average_score']; ?>%; background-color: var(--primary-color);"></div>
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
</body>
</html>