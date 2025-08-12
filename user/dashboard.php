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
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #ffbc3b;
            --secondary-color: #5f4594;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
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
            background: white;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
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
            background: linear-gradient(135deg, var(--accent-color), #d35400);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #138496);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary-color);
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
            color: var(--secondary-color);
        }

        .course-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-image {
            height: 120px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .course-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-footer {
            margin-top: auto;
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

        .activity-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: white;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-item.quiz {
            border-left-color: var(--primary-color);
        }

        .activity-item.question {
            border-left-color: var(--secondary-color);
        }

        .activity-item.course {
            border-left-color: var(--primary-color);
        }

        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--secondary-color);
            transition: all 0.3s ease;
            background: white;
            border: 1px solid #e9ecef;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: black;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .action-btn:hover i {
            color: black;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: var(--secondary-color);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .sidebar-brand:hover {
            color: white;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
        }

        .sidebar-user {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            margin-bottom: 10px;
        }

        .user-info h6 {
            color: white;
            margin-bottom: 5px;
        }

        .user-info small {
            color: rgba(255, 255, 255, 0.7);
        }

        .sidebar-nav {
            padding: 20px 0;
            flex: 1;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
            background: rgba(0, 0, 0, 0.2);
            border-left-color: var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-footer .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 10px 0;
        }

        .sidebar-footer .nav-link:hover {
            color: var(--primary-color);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            background: #f8f9fa;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Navigation Bar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .page-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-left: 15px;
        }

        .nav-right .user-dropdown .btn {
            color: var(--secondary-color);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(95, 69, 148, 0.1);
            border: none;
        }

        .nav-right .user-dropdown .btn:hover {
            background: rgba(95, 69, 148, 0.2);
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 30px;
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.collapsed {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-container.sidebar-collapsed .main-content {
                margin-left: 0;
            }
        }

        @media (min-width: 992px) {
            .sidebar-toggle {
                display: none;
            }
        }

        /* Update existing styles to use primary/secondary colors */
        .welcome-section {
            background: var(--primary-color);
            color: black;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: var(--primary-color);
            color: black;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .stats-card.success {
            background: var(--secondary-color);
            color: white;
        }

        .stats-card.info {
            background: var(--primary-color);
            color: black;
        }

        .stats-card.warning {
            background: var(--secondary-color);
            color: white;
        }

        .course-image {
            background: var(--primary-color);
            color: black;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            color: black;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .activity-item {
            border-left: 4px solid var(--primary-color);
        }

        .action-btn {
            background: white;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: black;
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/courses.php') ?>" class="nav-link">
                            <i class="fas fa-book"></i>
                            <span>Kozi Zangu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('user/profile.php') ?>" class="nav-link">
                            <i class="fas fa-user-cog"></i>
                            <span>Wasifu Wangu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('uliza-swali.php') ?>" class="nav-link">
                            <i class="fas fa-question-circle"></i>
                            <span>Uliza Swali</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('kozi.php') ?>" class="nav-link">
                            <i class="fas fa-search"></i>
                            <span>Tafuta Kozi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('fursa.php') ?>" class="nav-link">
                            <i class="fas fa-lightbulb"></i>
                            <span>Fursa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('habari.php') ?>" class="nav-link">
                            <i class="fas fa-newspaper"></i>
                            <span>Habari</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('soko.php') ?>" class="nav-link">
                            <i class="fas fa-store"></i>
                            <span>Soko</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('biashara.php') ?>" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Biashara</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('wanufaika.php') ?>" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Wanufaika</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('ongea-hub.php') ?>" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Ongea Hub</span>
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
                            <li><a class="dropdown-item" href="<?= app_url('user/profile.php') ?>">
                                    <i class="fas fa-user-cog me-2"></i>Wasifu Wangu
                                </a></li>
                            <li><a class="dropdown-item" href="<?= app_url('user/courses.php') ?>">
                                    <i class="fas fa-book me-2"></i>Kozi Zangu
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
                        <i class="fas fa-sun me-2"></i>
                        Karibu tena, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                    </h1>
                    <p class="lead mb-0">
                        Endelea na safari yako ya kujifunza na uendelee na maendeleo yako
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h4 class="mb-4">
                        <i class="fas fa-bolt" style="color: var(--primary-color);" class="me-2"></i>
                        Vitendo vya Haraka
                    </h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/courses.php') ?>" class="action-btn">
                                <i class="fas fa-book"></i>
                                <span class="fw-bold">Tazama Kozi</span>
                                <small class="text-muted">Jisajili kwenye kozi mpya</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/courses.php#enrolled') ?>" class="action-btn">
                                <i class="fas fa-play-circle"></i>
                                <span class="fw-bold">Endelea Kusoma</span>
                                <small class="text-muted">Rudi kwenye kozi uliyosajiliwa</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('uliza-swali.php') ?>" class="action-btn">
                                <i class="fas fa-question-circle"></i>
                                <span class="fw-bold">Uliza Swali</span>
                                <small class="text-muted">Pata msaada kutoka kwa mitaalam</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= app_url('user/profile.php') ?>" class="action-btn">
                                <i class="fas fa-user-cog"></i>
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
                                <i class="fas fa-book fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $totalCourses; ?></h3>
                                <p class="mb-0">Kozi Zilizosajiliwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-play-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $quizStats['videos_completed']; ?></h3>
                                <p class="mb-0">Masomo Yaliyokamilika</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $quizStats['total_questions_answered']; ?></h3>
                                <p class="mb-0">Maswali Yaliyojibiwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stats-card warning">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-2x mb-2"></i>
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
                                    <i class="fas fa-chart-line" style="color: var(--primary-color);" class="me-2"></i>
                                    Maendeleo Yako ya Jumla
                                </h5>
                                <div class="progress-circle" style="background: conic-gradient(var(--success-color) 0deg, var(--success-color) <?php echo $overallProgress * 3.6; ?>deg, #e9ecef <?php echo $overallProgress * 3.6; ?>deg, #e9ecef 360deg);">
                                    <div class="progress-text"><?php echo round($overallProgress); ?>%</div>
                                </div>
                                <p class="text-muted mb-0">
                                    Umeendelea na <strong><?php echo $totalCourses; ?></strong> kozi
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    <i class="fas fa-trophy" style="color: var(--primary-color);" class="me-2"></i>
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
                                    <i class="fas fa-history me-2"></i>
                                    Shughuli za Hivi Karibuni
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentQuizAttempts) && empty($userQuestions)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
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
                                                <div class="me-3">
                                                    <i class="<?php echo $activity['icon']; ?> fa-lg" style="color: <?php echo $activity['color']; ?>;"></i>
                                                </div>
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
                                    <i class="fas fa-book me-2"></i>
                                    Kozi Zako
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($enrolledCourses)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book-open fa-2x text-muted mb-3"></i>
                                        <h6>Hujajisajili kwenye kozi yoyote</h6>
                                        <p class="text-muted">Jisajili kwenye kozi moja au zaidi ili uanze kujifunza</p>
                                        <a href="<?= app_url('user/courses.php') ?>" class="btn btn-primary-custom text-white">
                                            <i class="fas fa-plus me-2"></i>
                                            Tazama Kozi
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($enrolledCourses as $course): ?>
                                        <div class="course-card mb-3">
                                            <div class="course-image">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="card-body course-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h6>
                                                <p class="card-text text-muted small">
                                                    <?php echo $course['total_videos']; ?> masomo • <?php echo $course['total_questions']; ?> maswali
                                                </p>
                                            </div>
                                            <div class="card-footer course-footer bg-transparent border-0 p-2">
                                                <a href="<?= app_url('user/course.php?id=' . $course['id']) ?>"
                                                    class="btn btn-primary-custom text-white btn-sm w-100">
                                                    <i class="fas fa-play me-2"></i>
                                                    Endelea
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if (count($enrolledCourses) >= 5): ?>
                                        <div class="text-center mt-3">
                                            <a href="<?= app_url('user/courses.php') ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-2"></i>
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