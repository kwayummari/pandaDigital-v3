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
$page_title = 'Jifunze Kozi';

// Get course ID and video ID from URL
$courseId = $_GET['course_id'] ?? null;
$videoId = $_GET['video_id'] ?? null;

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
if (!$isEnrolled) {
    header('Location: ' . app_url('user/courses.php') . '?error=not_enrolled');
    exit();
}

// Check if course is paid and user has access
if ($course['courseIsPaidStatusId'] == 1) {
    $hasPaidAccess = $courseModel->hasPaidCourseAccess($currentUser['id'], $courseId);
    if (!$hasPaidAccess) {
        header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&error=payment_required');
        exit();
    }
}

// Get course videos/lessons
$courseVideos = $courseModel->getCourseVideos($courseId);
if (empty($courseVideos)) {
    header('Location: ' . app_url('user/courses.php') . '?error=no_videos');
    exit();
}

// If no specific video ID, start with the first video
if (!$videoId) {
    $videoId = $courseVideos[0]['id'];
}

// Get current video information
$currentVideo = null;
foreach ($courseVideos as $video) {
    if ($video['id'] == $videoId) {
        $currentVideo = $video;
        break;
    }
}

if (!$currentVideo) {
    header('Location: ' . app_url('user/courses.php') . '?error=video_not_found');
    exit();
}

// Get questions for this video
$questions = $quizModel->getQuestionsByVideo($videoId);

// Check if user has completed this video
$videoCompleted = $quizModel->hasCompletedQuiz($currentUser['id'], $videoId);

// Get user progress
$userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentVideo['description']); ?> - <?php echo htmlspecialchars($course['name']); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/style.css'); ?>?v=9">
</head>

<body>
    <div class="dashboard-container course-learning-page">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <!-- Course Header -->
            <div class="course-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="course-title"><?php echo htmlspecialchars($course['name']); ?></h1>
                        <p class="course-description text-muted"><?php echo htmlspecialchars($course['description']); ?></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="progress-info">
                            <div class="progress mb-2">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $userProgress['percentage']; ?>%"
                                    aria-valuenow="<?php echo $userProgress['percentage']; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($userProgress['percentage']); ?>%
                                </div>
                            </div>
                            <small class="text-muted"><?php echo $userProgress['completed']; ?> kati ya <?php echo $userProgress['total']; ?> masomo yamekamilika</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Video Player Section -->
                <div class="col-md-8">
                    <div class="video-section mb-4">
                        <div class="video-container">
                            <?php if (!empty($currentVideo['name'])): ?>
                                <iframe src="<?php echo htmlspecialchars($currentVideo['name']); ?>"
                                    frameborder="0"
                                    allowfullscreen
                                    class="w-100 rounded"
                                    style="height: 400px;">
                                </iframe>
                            <?php else: ?>
                                <div class="video-placeholder d-flex align-items-center justify-content-center bg-light rounded"
                                    style="height: 400px;">
                                    <span class="text-muted">Video haijapatikana</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="video-info mt-3">
                            <h3><?php echo htmlspecialchars($currentVideo['description']); ?></h3>
                            <?php if ($videoCompleted): ?>
                                <span class="badge bg-success">✓ Imekamilika</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quiz Section -->
                    <?php if (!empty($questions)): ?>
                        <div class="quiz-section">
                            <h4 class="mb-3">Maswali ya Majaribio</h4>
                            <form id="quizForm">
                                <input type="hidden" id="videoId" value="<?php echo $videoId; ?>">
                                <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">

                                <?php foreach ($questions as $index => $question): ?>
                                    <div class="question-item mb-4 p-3 border rounded">
                                        <h6 class="question-text mb-3">
                                            <?php echo ($index + 1) . '. ' . htmlspecialchars($question['question']); ?>
                                        </h6>

                                        <div class="options">
                                            <?php
                                            $options = json_decode($question['options'], true);
                                            if (is_array($options)):
                                                foreach ($options as $optionIndex => $option):
                                            ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="question_<?php echo $question['id']; ?>"
                                                            id="option_<?php echo $question['id']; ?>_<?php echo $optionIndex; ?>"
                                                            value="<?php echo $optionIndex; ?>" required>
                                                        <label class="form-check-label" for="option_<?php echo $question['id']; ?>_<?php echo $optionIndex; ?>">
                                                            <?php echo htmlspecialchars($option); ?>
                                                        </label>
                                                    </div>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <button type="submit" class="btn btn-primary" id="submitQuiz">
                                    <?php if ($videoCompleted): ?>
                                        Angalia Majibu
                                    <?php else: ?>
                                        Maliza Somo
                                    <?php endif; ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Course Navigation -->
                <div class="col-md-4">
                    <div class="course-navigation">
                        <h5 class="mb-3">Masomo</h5>
                        <div class="lessons-list">
                            <?php foreach ($courseVideos as $index => $video): ?>
                                <div class="lesson-item <?php echo ($video['id'] == $videoId) ? 'active' : ''; ?>">
                                    <a href="<?php echo app_url('user/learn.php'); ?>?course_id=<?php echo $courseId; ?>&video_id=<?php echo $video['id']; ?>"
                                        class="lesson-link">
                                        <div class="lesson-number"><?php echo $index + 1; ?></div>
                                        <div class="lesson-content">
                                            <h6 class="lesson-title"><?php echo htmlspecialchars($video['description']); ?></h6>
                                            <div class="lesson-meta">
                                                <span class="lesson-type">Video na Maswali</span>
                                                <?php if ($quizModel->hasCompletedQuiz($currentUser['id'], $video['id'])): ?>
                                                    <span class="lesson-status completed">✓ Imekamilika</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Quiz submission
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const videoId = document.getElementById('videoId').value;
            const courseId = document.getElementById('courseId').value;
            const formData = new FormData(this);

            // Add video and course IDs
            formData.append('videoId', videoId);
            formData.append('courseId', courseId);

            // Submit quiz
            fetch('<?php echo app_url('user/submit-quiz.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Hongera! Umekamilisha somo hili.');
                        location.reload(); // Reload to show completion status
                    } else {
                        alert('Kulikuwa na tatizo: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Kulikuwa na tatizo, jaribu tena');
                });
        });

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