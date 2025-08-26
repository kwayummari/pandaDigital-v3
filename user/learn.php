<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answers'])) {
    try {
        $videoId = $_POST['video_id'] ?? null;
        $courseId = $_POST['course_id'] ?? null;
        $answers = $_POST['answers'] ?? [];

        if ($videoId && $courseId) {
            // Get question IDs and answer IDs from form
            $questionIds = $_POST['qn_id'] ?? [];
            $answerIds = $_POST['ans_id'] ?? [];

            if (empty($questionIds) || empty($answerIds)) {
                $quizError = "Tafadhali jibu maswali yote.";
            } else {
                // Convert form data to the format expected by submitQuizAnswers
                $answers = [];
                foreach ($questionIds as $index => $questionId) {
                    if (isset($answerIds[$index]) && !empty($answerIds[$index])) {
                        $answers[$questionId] = $answerIds[$index];
                    }
                }

                if (!empty($answers)) {
                    // Submit quiz answers
                    $result = $quizModel->submitQuizAnswers($currentUser['id'], $videoId, $answers);

                    if ($result) {
                        // Redirect to show results
                        header('Location: ' . app_url('user/learn.php') . '?course_id=' . $courseId . '&video_id=' . $videoId . '&message=quiz_completed');
                        exit();
                    } else {
                        $quizError = 'Kulikuwa na tatizo la kuhifadhi majibu yako.';
                    }
                } else {
                    $quizError = "Tafadhali jibu maswali yote.";
                }
            }
        }
    } catch (Exception $e) {
        $quizError = 'Kulikuwa na tatizo la kuhifadhi majibu yako.';
    }
}

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

// Debug: Check what questions we got
error_log("Questions for video $videoId: " . print_r($questions, true));

// Check if user has completed this video
$videoCompleted = $quizModel->hasCompletedQuiz($currentUser['id'], $videoId);

// Get user progress
$userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);

// Ensure userProgress has required keys
if (!isset($userProgress['completed'])) {
    $userProgress['completed'] = 0;
}
if (!isset($userProgress['total'])) {
    $userProgress['total'] = count($courseVideos);
}
if (!isset($userProgress['percentage'])) {
    $userProgress['percentage'] = $userProgress['total'] > 0 ? ($userProgress['completed'] / $userProgress['total']) * 100 : 0;
}

// Debug: Check user progress
error_log("User progress: " . print_r($userProgress, true));

// Check if user has already answered questions for this video
$hasAnswered = $quizModel->hasCompletedQuiz($currentUser['id'], $videoId);
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

    <style>
        /* Quiz Section Styling */
        .quiz-section .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .quiz-section .card-header {
            background: linear-gradient(135deg, var(--primary-color), #667eea);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
            padding: 20px;
        }

        .quiz-section .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .card-header-action {
            float: right;
        }

        .question-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .question-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .question-number {
            background: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .required-badge {
            color: #dc3545;
            font-weight: bold;
            margin-left: 10px;
            font-size: 18px;
        }

        .question-text {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 20px;
        }

        .answers-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .answer-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .answer-option:hover {
            border-color: var(--primary-color);
            background: #f8f9ff;
        }

        .answer-option.correct-answer {
            border-color: #28a745;
            background: #d4edda;
        }

        .answer-option.wrong-answer {
            border-color: #dc3545;
            background: #f8d7da;
        }

        .answer-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin: 0;
            width: 100%;
        }

        .answer-label input[type="radio"] {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .answer-text {
            font-size: 15px;
            color: #333;
            line-height: 1.5;
        }

        .btn-lg {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
        }

        /* Progress sidebar styling */
        .progress-info {
            margin-bottom: 20px;
        }

        .stats-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-item i {
            font-size: 20px;
            width: 24px;
        }

        .stat-item div {
            flex-grow: 1;
        }

        .stat-item strong {
            display: block;
            font-size: 14px;
            color: #333;
        }

        .stat-item small {
            color: #6c757d;
            font-size: 12px;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .quiz-section .card-header {
                padding: 15px;
            }

            .question-card {
                padding: 15px;
            }

            .question-text {
                font-size: 15px;
            }

            .answer-option {
                padding: 12px;
            }

            .btn-lg {
                padding: 10px 25px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container course-learning-page">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <!-- Messages -->
            <?php if (isset($_GET['message'])): ?>
                <?php if ($_GET['message'] === 'quiz_completed'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Hongera!</strong> Umekamilisha somo hili kwa mafanikio. Unaweza kuendelea na somo linalofuata.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($quizError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Kosa!</strong> <?php echo htmlspecialchars($quizError); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Debug Information (remove in production) -->
            <?php if (isset($_GET['debug'])): ?>
                <div class="alert alert-warning">
                    <strong>Debug Info:</strong><br>
                    Course ID: <?php echo $courseId; ?><br>
                    Video ID: <?php echo $videoId; ?><br>
                    Questions Count: <?php echo count($questions); ?><br>
                    User Progress: <?php echo print_r($userProgress, true); ?><br>
                    Has Answered: <?php echo $hasAnswered ? 'Yes' : 'No'; ?><br>
                    Video Completed: <?php echo $videoCompleted ? 'Yes' : 'No'; ?>
                </div>
            <?php endif; ?>

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
                            <div class="card">
                                <div class="card-header">
                                    <h5>
                                        <i class="fas fa-question-circle"></i>
                                        Maswali ya Majaribio (<?php echo count($questions); ?>)
                                    </h5>
                                    <?php if ($hasAnswered): ?>
                                        <div class="card-header-action">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Umekamilisha
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if ($hasAnswered): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i>
                                            Umekwisha jibu maswali haya. Unaweza kurudia kama unataka kuboresha matokeo yako.
                                        </div>
                                    <?php endif; ?>

                                    <form method="post" id="quizForm" class="quiz-form">
                                        <input type="hidden" name="video_id" value="<?php echo $videoId; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $courseId; ?>">

                                        <?php foreach ($questions as $index => $question): ?>
                                            <?php
                                            // Get answers for this question
                                            $answers = $quizModel->getAnswersByQuestion($question['id']);

                                            // Get user's previous answer if exists
                                            $userAnswerId = null;
                                            if ($hasAnswered) {
                                                $userAnswerId = $quizModel->getUserAnswerForQuestion($currentUser['id'], $question['id']);
                                            }
                                            ?>
                                            <div class="question-card mb-4">
                                                <div class="question-header">
                                                    <span class="question-number">Swali <?php echo $index + 1; ?></span>
                                                    <span class="required-badge">*</span>
                                                </div>
                                                <div class="question-text">
                                                    <?php echo nl2br(htmlspecialchars($question['question'])); ?>
                                                </div>
                                                <div class="answers-container">
                                                    <?php if (!empty($answers)): ?>
                                                        <?php foreach ($answers as $answer): ?>
                                                            <?php
                                                            $answerId = $answer['id'];
                                                            $answerText = htmlspecialchars($answer['answer_text']);
                                                            $isCorrect = $answer['is_correct'] == 'true';
                                                            $isSelected = ($userAnswerId == $answerId);
                                                            ?>
                                                            <div class="answer-option <?php echo $hasAnswered && $isSelected ? ($isCorrect ? 'correct-answer' : 'wrong-answer') : ''; ?>">
                                                                <label class="answer-label">
                                                                    <input type="radio"
                                                                        name="ans_id[<?php echo $index; ?>]"
                                                                        value="<?php echo $answerId; ?>"
                                                                        <?php echo $isSelected ? 'checked' : ''; ?>
                                                                        <?php echo $hasAnswered ? 'disabled' : 'required'; ?>>
                                                                    <span class="answer-text"><?php echo $answerText; ?></span>
                                                                    <?php if ($hasAnswered && $isCorrect): ?>
                                                                        <i class="fas fa-check-circle text-success ms-2"></i>
                                                                    <?php elseif ($hasAnswered && $isSelected && !$isCorrect): ?>
                                                                        <i class="fas fa-times-circle text-danger ms-2"></i>
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="alert alert-warning">
                                                            Hakuna majibu yaliyopatikana kwa swali hili.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <input type="hidden" name="qn_id[]" value="<?php echo $question['id']; ?>">
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (!$hasAnswered): ?>
                                            <div class="text-center mt-4">
                                                <button type="submit" name="submit_answers" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-check"></i> Tuma Majibu
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center mt-4">
                                                <button type="submit" name="submit_answers" class="btn btn-warning btn-lg">
                                                    <i class="fas fa-redo"></i> Rudia Majaribio
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Hakuna maswali ya majaribio kwa somo hili. Unaweza kuendelea na somo linalofuata.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Course Navigation -->
                <div class="col-md-4">
                    <!-- Progress Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-line"></i> Maendeleo Yangu</h6>
                        </div>
                        <div class="card-body">
                            <div class="progress-info mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Maswali yaliyojibiwa:</span>
                                    <strong><?php echo $userProgress['completed']; ?>/<?php echo $userProgress['total']; ?></strong>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" style="width: <?php echo $userProgress['percentage']; ?>%"></div>
                                </div>
                                <small class="text-muted">
                                    <?php echo round($userProgress['percentage']); ?>% yamekamilika
                                </small>
                            </div>

                            <div class="stats-grid">
                                <div class="stat-item">
                                    <i class="fas fa-video text-primary"></i>
                                    <div>
                                        <strong>Somo hili</strong>
                                        <small><?php echo count($questions); ?> maswali</small>
                                    </div>
                                </div>
                                <?php if ($hasAnswered): ?>
                                    <div class="stat-item">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <div>
                                            <strong>Umekamilisha</strong>
                                            <small>Somo hili</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

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