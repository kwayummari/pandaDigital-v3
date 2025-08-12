<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Set page title for navigation
$page_title = 'Maelezo ya Kozi';

// Get course ID from URL
$courseId = $_GET['id'] ?? null;

if (!$courseId) {
    header('Location: ' . app_url('user/courses.php') . '?error=invalid_course');
    exit();
}

// Get course information
$course = $courseModel->getCourseById($courseId, $currentUser['id']);
if (!$course) {
    header('Location: ' . app_url('user/courses.php') . '?error=course_not_found');
    exit();
}

// Check if user is enrolled
$isEnrolled = $courseModel->isUserEnrolled($currentUser['id'], $courseId);

// Get course statistics
$courseStats = $courseModel->getCourseStats($courseId);
$totalStudents = $courseStats['total_students'];
$totalLessons = $courseStats['total_lessons'];
$totalQuestions = $courseStats['total_questions'];

// Get course lessons (videos)
$courseLessons = $courseModel->getCourseVideos($courseId);

// Get user progress if enrolled
$userProgress = null;
if ($isEnrolled) {
    $userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/style.css'); ?>?v=8">
</head>

<body>
    <div class="dashboard-container course-overview-page">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <!-- Messages -->
            <?php if (isset($_GET['message'])): ?>
                <?php if ($_GET['message'] === 'enrollment_success'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Hongera!</strong> Umesajili kwenye kozi hii kwa mafanikio. Sasa unaweza kuanza kujifunza.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['message'] === 'already_enrolled'): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Umesajili!</strong> Tayari umejisajili kwenye kozi hii. Unaweza kuendelea kujifunza.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Course Header -->
            <div class="course-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="course-title mb-2"><?php echo htmlspecialchars($course['name']); ?></h1>
                        <p class="course-description text-muted mb-0">
                            <?php echo htmlspecialchars($course['description'] ?? 'Kozi ya kujifunza na kujenga ujuzi wa kidigitali.'); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php if ($isEnrolled): ?>
                            <a href="<?php echo app_url('user/learn.php'); ?>?course_id=<?php echo $courseId; ?>&video_id=1" class="btn btn-primary">
                                Endelea Kusoma
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary" onclick="enrollCourse(<?php echo $courseId; ?>)">
                                Jiunge Bure
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo app_url('user/courses.php'); ?>" class="btn btn-outline-secondary ms-2">
                            Rudi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalStudents; ?></div>
                        <div class="stat-label">Wanafunzi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalLessons; ?></div>
                        <div class="stat-label">Masomo</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalQuestions; ?></div>
                        <div class="stat-label">Maswali</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">Bure</div>
                        <div class="stat-label">Bei</div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="row">
                <!-- Course Description -->
                <div class="col-md-8">
                    <div class="content-card mb-4">
                        <h3 class="card-title">Kuhusu Kozi Hii</h3>
                        <div class="card-content">
                            <?php if (!empty($course['long_description'])): ?>
                                <?php echo nl2br(htmlspecialchars($course['long_description'])); ?>
                            <?php else: ?>
                                <p>Kozi hii inakupa ufahamu wa kina juu ya mada muhimu za kidigitali. Kupitia masomo na mazoezi, utajifunza jinsi ya kutumia teknolojia kwa ufanisi na kuunda suluhisho za kidigitali.</p>
                                <p>Kozi inajumuisha video za mafunzo, maswali ya majaribio, na mazoezi ya vitendo ambayo yatakusaidia kujenga ujuzi wa kweli na uwezo wa kutumia maarifa yako katika hali halisi.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Course Lessons -->
                    <div class="content-card">
                        <h3 class="card-title">Mipango ya Masomo</h3>
                        <div class="lessons-list">
                            <?php if (!empty($courseLessons)): ?>
                                <?php foreach ($courseLessons as $index => $lesson): ?>
                                    <div class="lesson-item">
                                        <div class="lesson-number"><?php echo $index + 1; ?></div>
                                        <div class="lesson-content">
                                            <h5 class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></h5>
                                            <div class="lesson-meta">
                                                <span class="lesson-type">Video na Maswali</span>
                                                <?php if ($isEnrolled && isset($lesson['completed']) && $lesson['completed']): ?>
                                                    <span class="lesson-status completed">✓ Imekamilika</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-lessons">
                                    <p class="text-muted">Masomo yataongezwa hivi karibuni.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="col-md-4">
                    <div class="content-card mb-4">
                        <h3 class="card-title">Onyesho la Kozi</h3>
                        <div class="course-features">
                            <div class="feature-item">
                                <span class="feature-icon">📹</span>
                                <span class="feature-text"><?php echo $totalLessons; ?> Video za Mafunzo</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">❓</span>
                                <span class="feature-text"><?php echo $totalQuestions; ?> Maswali ya Majaribio</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">📱</span>
                                <span class="feature-text">Inafikiwa kwenye Simu na Kompyuta</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">⏰</span>
                                <span class="feature-text">Ufikiaji wa Maisha</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🆓</span>
                                <span class="feature-text">Kozi ya Bure Kabisa</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🇹🇿</span>
                                <span class="feature-text">Lugha: Kiswahili</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="content-card">
                        <h3 class="card-title">Vipengele vya Kozi</h3>
                        <div class="course-info">
                            <div class="info-item">
                                <strong>Mwalimu:</strong> Panda Digital Team
                            </div>
                            <div class="info-item">
                                <strong>Muda:</strong> <?php echo $totalLessons; ?> Masomo
                            </div>
                            <div class="info-item">
                                <strong>Ngazi:</strong> Mwanzo
                            </div>
                            <div class="info-item">
                                <strong>Lugha:</strong> Kiswahili
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function enrollCourse(courseId) {
            if (confirm('Je, unahitaji kujisajili kwenye kozi hii?')) {
                // Here you would typically make an AJAX call to enroll the user
                // For now, we'll redirect to a simple enrollment process
                window.location.href = '<?php echo app_url("user/enroll.php"); ?>?course_id=' + courseId;
            }
        }

        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }
        });
    </script>
</body>

</html>