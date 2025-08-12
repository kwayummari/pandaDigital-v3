<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Get course ID from URL
$courseId = $_GET['id'] ?? null;

if (!$courseId) {
    header('Location: /user/courses.php?error=invalid_course');
    exit();
}

// Get course information
$course = $courseModel->getCourseById($courseId, $currentUser['id']);
if (!$course) {
    header('Location: /user/courses.php?error=course_not_found');
    exit();
}

// Check if user is enrolled
$enrollment = $courseModel->isUserEnrolled($currentUser['id'], $courseId);
if (!$enrollment) {
    header('Location: /user/courses.php?error=not_enrolled');
    exit();
}

// Get course videos
$courseVideos = $courseModel->getCourseVideos($courseId);

// Get course statistics
$courseStats = $courseModel->getCourseStats($courseId);

// Calculate user's progress
$userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);

// Get first video for starting
$firstVideo = !empty($courseVideos) ? $courseVideos[0] : null;
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        :root {
            --primary-color: #ffbc3b;
            --secondary-color: #5f4594;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: var(--secondary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .course-header {
            background: var(--primary-color);
            color: black;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
        }

        .course-image {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .progress-bar {
            height: 12px;
            border-radius: 6px;
        }

        .lesson-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .lesson-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            background: white;
        }

        .lesson-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .lesson-item.active {
            border-left-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .lesson-item.completed {
            border-left-color: var(--success-color);
            background: #d4edda;
        }

        .lesson-item.completed::after {
            content: "✓";
            float: right;
            color: var(--success-color);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .btn-start {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-start:hover {
            background: #229954;
            transform: translateY(-2px);
        }

        .btn-continue {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-continue:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .badge-free {
            background: var(--success-color);
            color: white;
            font-size: 0.9rem;
            padding: 8px 15px;
        }

        .badge-paid {
            background: var(--warning-color);
            color: white;
            font-size: 0.9rem;
            padding: 8px 15px;
        }

        .course-description {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .alert {
            border-radius: 10px;
            border: none;
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
            $page_title = htmlspecialchars($course['name']);
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Course Header -->
                <div class="course-header text-center">
                    <div class="course-image">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="mb-3"><?php echo htmlspecialchars($course['name']); ?></h1>
                    <p class="lead mb-3">
                        <?php
                        if (!empty($course['description'])) {
                            echo htmlspecialchars($course['description']);
                        } else {
                            echo 'Maelezo ya kozi hayajapatikana.';
                        }
                        ?>
                    </p>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <?php if ($course['courseIsPaidStatusId'] == 1): ?>
                            <span class="badge badge-paid">
                                <i class="fas fa-credit-card me-1"></i>
                                Kozi ya Kulipwa
                            </span>
                        <?php else: ?>
                            <span class="badge badge-free">
                                <i class="fas fa-gift me-1"></i>
                                Kozi ya Bure
                            </span>
                        <?php endif; ?>

                        <span class="badge bg-info">
                            <i class="fas fa-users me-1"></i>
                            <?php echo $courseStats['total_students']; ?> Wanafunzi
                        </span>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $courseStats['total_lessons']; ?></div>
                        <div class="stat-label">Jumla ya Masomo</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon text-success">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $courseStats['total_questions']; ?></div>
                        <div class="stat-label">Jumla ya Maswali</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-value">
                            <?php
                            // Estimate course duration (this would need to be calculated based on actual video lengths)
                            echo "~" . round($courseStats['total_lessons'] * 15 / 60, 1) . "h";
                            ?>
                        </div>
                        <div class="stat-label">Muda wa Kusoma</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon text-info">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-value">
                            <?php
                            if ($courseStats['average_score'] > 0) {
                                echo round($courseStats['average_score']) . '%';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                        <div class="stat-label">Wastani wa Alama</div>
                    </div>
                </div>

                <!-- User Progress Section -->
                <div class="progress-section">
                    <h4 class="mb-4">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Maendeleo Yako
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Maendeleo ya Kozi</small>
                                    <small class="text-muted"><?php echo round($userProgress['completion_percentage']); ?>%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success"
                                        style="width: <?php echo $userProgress['completion_percentage']; ?>%"></div>
                                </div>
                            </div>

                            <p class="mb-0">
                                Umejibu <strong><?php echo $userProgress['answered_questions']; ?></strong> kati ya
                                <strong><?php echo $userProgress['total_questions']; ?></strong> maswali
                            </p>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Usahihi wa Majibu</small>
                                    <small class="text-muted"><?php echo round($userProgress['accuracy_percentage']); ?>%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info"
                                        style="width: <?php echo $userProgress['accuracy_percentage']; ?>%"></div>
                                </div>
                            </div>

                            <p class="mb-0">
                                Majibu sahihi: <strong><?php echo $userProgress['correct_answers']; ?></strong> kati ya
                                <strong><?php echo $userProgress['answered_questions']; ?></strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Course Description -->
                <div class="course-description">
                    <h4 class="mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Maelezo zaidi ya Kozi
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Kuhusu Kozi Hii</h6>
                            <p class="text-muted">
                                <?php
                                if (!empty($course['description'])) {
                                    echo htmlspecialchars($course['description']);
                                } else {
                                    echo 'Maelezo ya kozi hayajapatikana. Kozi hii ina masomo muhimu na maswali ya kujipima ili kuhakikisha umefahamu vizuri.';
                                }
                                ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6>Unachojifunza</h6>
                            <ul class="text-muted">
                                <li>Masomo <?php echo $courseStats['total_lessons']; ?> ya kina</li>
                                <li>Maswali <?php echo $courseStats['total_questions']; ?> ya kujipima</li>
                                <li>Ufahamu wa kina wa mada</li>
                                <li>Uzoefu wa vitendo</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Course Lessons -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Masomo ya Kozi (<?php echo count($courseVideos); ?>)
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($courseVideos)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                <h5>Hakuna masomo yaliyopakiwa bado!</h5>
                                <p class="text-muted">Masomo ya kozi hii bado hayajapakiwa. Tafadhali subiri au wasiliana na msimamizi.</p>
                            </div>
                        <?php else: ?>
                            <div class="lesson-list">
                                <?php foreach ($courseVideos as $index => $video): ?>
                                    <?php
                                    $isCompleted = $quizModel->hasCompletedQuiz($currentUser['id'], $video['id']);
                                    $lessonClass = 'lesson-item';
                                    if ($isCompleted) $lessonClass .= ' completed';
                                    ?>

                                    <div class="<?php echo $lessonClass; ?>"
                                        onclick="window.location.href='/user/learn.php?course_id=<?php echo $courseId; ?>&video_id=<?php echo $video['id']; ?>'">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <?php if ($isCompleted): ?>
                                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-play-circle text-primary fa-lg"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">
                                                        <?php echo $index + 1; ?>. <?php echo htmlspecialchars($video['description']); ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo $video['duration'] ?? 'Muda haujulikani'; ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <?php if ($isCompleted): ?>
                                                    <span class="badge bg-success">Imekamilika</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Haijakamilika</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <?php if ($firstVideo): ?>
                        <?php if ($userProgress['completion_percentage'] == 0): ?>
                            <a href="/user/learn.php?course_id=<?php echo $courseId; ?>&video_id=<?php echo $firstVideo['id']; ?>"
                                class="btn btn-start text-white btn-lg">
                                <i class="fas fa-play me-2"></i>
                                Anza Kusoma
                            </a>
                        <?php else: ?>
                            <a href="/user/learn.php?course_id=<?php echo $courseId; ?>&video_id=<?php echo $firstVideo['id']; ?>"
                                class="btn btn-continue text-white btn-lg">
                                <i class="fas fa-play me-2"></i>
                                Endelea Kusoma
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <a href="/user/courses.php" class="btn btn-outline-secondary btn-lg ms-3">
                        <i class="fas fa-arrow-left me-2"></i>
                        Rudi kwenye Kozi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->

</body>

</html>