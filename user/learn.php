<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Test database connection
try {
    $conn = $quizModel->getConnection();
    error_log("Database connection successful");
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
}

// Quiz submission will be handled after URL parameters are retrieved

// Set page title for navigation
$page_title = 'Jifunze Kozi';

// Get course ID and video ID from URL
$courseId = $_GET['course_id'] ?? null;
$videoId = $_GET['video_id'] ?? null;

// Debug: Log the received parameters
error_log("Received course_id: " . $courseId);
error_log("Received video_id: " . $videoId);

if (!$courseId) {
    header('Location: ' . app_url('user/courses.php') . '?error=invalid_course');
    exit();
}

// Get course information
$course = $courseModel->getCourseById($courseId, $currentUser['id']);
error_log("Course data: courseId=$courseId, course=" . print_r($course, true));

if (!$course) {
    error_log("Course not found, redirecting to courses.php");
    header('Location: ' . app_url('user/courses.php') . '?error=course_not_found');
    exit();
}

// Check if user is enrolled
$enrollmentData = $courseModel->isUserEnrolled($currentUser['id'], $courseId);
error_log("Enrollment check: userId=" . $currentUser['id'] . ", courseId=$courseId, enrollmentData=" . print_r($enrollmentData, true));

if (!$enrollmentData) {
    error_log("User not enrolled, redirecting to courses.php");
    header('Location: ' . app_url('user/courses.php') . '?error=not_enrolled');
    exit();
}

// Check if course is paid and user has access - EXACTLY like old system
if ($course['courseIsPaidStatusId'] == 1) {
    try {
        $conn = $quizModel->getConnection();

        // Check payment status - EXACTLY like old system
        $accessQuery = "SELECT e.*, ct.status as payment_status, c.courseIsPaidStatusId 
                        FROM enrolled e 
                        JOIN course c ON c.id = e.course_id
                        LEFT JOIN courseTransactions ct ON ct.courseId = c.id AND ct.studentId = e.user_id
                        WHERE e.course_id = ? AND e.user_id = ?";
        $stmt = $conn->prepare($accessQuery);
        $stmt->execute([$courseId, $currentUser['id']]);
        $accessResult = $stmt->fetch();

        if (!$accessResult || $accessResult['payment_status'] != '1') {
            error_log("Payment required: userId=" . $currentUser['id'] . ", courseId=$courseId");
            header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&error=payment_required');
            exit();
        }
    } catch (Exception $e) {
        error_log("Error checking payment status: " . $e->getMessage());
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

// Get questions for this video - EXACTLY like old system
try {
    $conn = $quizModel->getConnection();

    // Get questions for this video
    $questionsQuery = "SELECT * FROM questions WHERE video_id = ? ORDER BY id ASC";
    $stmt = $conn->prepare($questionsQuery);
    if (!$stmt) {
        error_log("Prepare failed for questions query");
        die("Database error");
    }

    $stmt->execute([$videoId]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Found " . count($questions) . " questions for video $videoId");

    // Check if user has already answered questions for this video
    $answeredQuery = "SELECT DISTINCT qn_id FROM algorithm WHERE user_id = ? AND qn_id IN (SELECT id FROM questions WHERE video_id = ?)";
    $stmt = $conn->prepare($answeredQuery);
    if (!$stmt) {
        error_log("Prepare failed for answered query");
        die("Database error");
    }

    $stmt->execute([$currentUser['id'], $videoId]);
    $answeredResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $answeredQuestions = [];
    foreach ($answeredResult as $row) {
        $answeredQuestions[] = $row['qn_id'];
    }


    $hasAnswered = !empty($answeredQuestions);
    error_log("User has answered: " . ($hasAnswered ? 'YES' : 'NO') . " (" . count($answeredQuestions) . " questions)");
} catch (Exception $e) {
    error_log("Error getting questions: " . $e->getMessage());
    $questions = [];
    $hasAnswered = false;
}

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

// Handle quiz submission - EXACTLY like old system
if (isset($_POST['submit_answers'])) {
    // Debug: Log what we received
    error_log("Quiz submission received!");
    error_log("POST data: " . print_r($_POST, true));

    $questionIds = $_POST['qn_id'] ?? [];
    $answerIds = $_POST['ans_id'] ?? [];

    if (!isset($currentUser['id']) || empty($currentUser['id'])) {
        $quizError = "❌ ERROR: User ID is not set!";
    } else if (empty($questionIds) || empty($answerIds)) {
        $quizError = "Tafadhali jibu maswali yote.";
    } else {
        try {
            $conn = $quizModel->getConnection();

            // Start transaction - EXACTLY like old system
            $conn->beginTransaction();

            // Delete existing answers for these questions - EXACTLY like old system
            if (!empty($questionIds)) {
                $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
                $deleteQuery = "DELETE FROM algorithm WHERE user_id = ? AND qn_id IN ($placeholders)";
                $stmt = $conn->prepare($deleteQuery);

                if (!$stmt) {
                    throw new Exception("Prepare delete failed");
                }

                $params = array_merge([$currentUser['id']], $questionIds);
                $stmt->execute($params);
                $deletedRows = $stmt->rowCount();
                error_log("🗑️ Deleted $deletedRows existing answers");
            }

            // Insert new answers - EXACTLY like old system
            $insertQuery = "INSERT INTO algorithm (qn_id, ans_id, user_id, date_created) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insertQuery);

            if (!$stmt) {
                throw new Exception("Prepare insert failed");
            }

            $insertCount = 0;
            foreach ($questionIds as $index => $questionId) {
                if (isset($answerIds[$index]) && !empty($answerIds[$index])) {
                    $stmt->execute([$questionId, $answerIds[$index], $currentUser['id']]);
                    $insertCount++;
                }
            }

            // Commit transaction
            $conn->commit();
            error_log("✅ Inserted $insertCount new answers");

            // Set success message and refresh to show results
            $quizSuccess = "Hongera! Umekamilisha somo hili. Unaweza kuendelea na somo linalofuata.";

            // Refresh the page to show results
            header('Location: ' . app_url('user/learn.php') . '?course_id=' . $courseId . '&video_id=' . $videoId . '&show_results=1');
            exit();
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollBack();
            error_log("Error submitting quiz answers: " . $e->getMessage());
            $quizError = 'Kulikuwa na tatizo la kuhifadhi majibu yako.';
        }
    }
}

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
            background: var(--primary-color);
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .answer-option .badge {
            font-size: 12px;
            padding: 4px 8px;
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

        /* Quiz Results Summary Styling */
        .quiz-results-summary {
            border: 2px solid #e9ecef;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .result-stat {
            padding: 15px;
        }

        .result-stat h3 {
            margin: 0;
            font-weight: bold;
            font-size: 2rem;
        }

        .result-stat small {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
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

            <?php if (isset($_GET['show_results']) && $_GET['show_results'] == '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Hongera!</strong> Umekamilisha somo hili kwa mafanikio. Unaweza kuendelea na somo linalofuata.
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

                        <!-- Navigation Buttons -->
                        <div class="navigation-buttons mt-3">
                            <?php
                            $currentIndex = array_search($videoId, array_column($courseVideos, 'id'));
                            $hasPrevious = $currentIndex > 0;
                            $hasNext = $currentIndex < count($courseVideos) - 1;
                            ?>

                            <?php if ($hasPrevious): ?>
                                <a href="<?php echo app_url('user/learn.php'); ?>?course_id=<?php echo $courseId; ?>&video_id=<?php echo $courseVideos[$currentIndex - 1]['id']; ?>"
                                    class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-arrow-left"></i> Somo la Nyuma
                                </a>
                            <?php endif; ?>

                            <?php if ($hasNext): ?>
                                <a href="<?php echo app_url('user/learn.php'); ?>?course_id=<?php echo $courseId; ?>&video_id=<?php echo $courseVideos[$currentIndex + 1]['id']; ?>"
                                    class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-arrow-right"></i> Somo Linalofuata
                                </a>
                            <?php endif; ?>
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

                                    <form method="post" id="quizForm" class="quiz-form" action="<?php echo $hasAnswered ? '#' : ''; ?>">
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

                                            // Check if we should show results
                                            $showResults = isset($_GET['show_results']) && $_GET['show_results'] == '1';

                                            // If showing results, we need to get the user's answers
                                            if ($showResults) {
                                                $userAnswerId = $quizModel->getUserAnswerForQuestion($currentUser['id'], $question['id']);
                                            }
                                            ?>
                                            <div class="question-card mb-4">
                                                <div class="question-header">
                                                    <span class="question-number">Swali <?php echo $index + 1; ?></span>
                                                    <span class="required-badge">*</span>
                                                </div>
                                                <div class="question-text">
                                                    <?php echo nl2br(htmlspecialchars($question['name'])); ?>
                                                </div>
                                                <div class="answers-container">
                                                    <?php if (!empty($answers)): ?>
                                                        <?php if ($hasAnswered && !$showResults): ?>
                                                            <!-- Show only the user's selected answer when they've already answered -->
                                                            <?php
                                                            $userAnswer = null;
                                                            foreach ($answers as $answer) {
                                                                if ($answer['id'] == $userAnswerId) {
                                                                    $userAnswer = $answer;
                                                                    break;
                                                                }
                                                            }
                                                            if ($userAnswer): ?>
                                                                <div class="answer-option">
                                                                    <label class="answer-label">
                                                                        <input type="radio" checked disabled>
                                                                        <span class="answer-text"><?php echo htmlspecialchars($userAnswer['name']); ?></span>
                                                                        <span class="badge bg-info ms-2">Jibu lako</span>
                                                                    </label>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <!-- Show all answer options for new attempts or when showing results -->
                                                            <?php foreach ($answers as $answer): ?>
                                                                <?php
                                                                $answerId = $answer['id'];
                                                                $answerText = htmlspecialchars($answer['name']);
                                                                $isCorrect = $answer['status'] == 'true';
                                                                $isSelected = ($userAnswerId == $answerId);
                                                                ?>
                                                                <div class="answer-option <?php echo $showResults && $isSelected ? ($isCorrect ? 'correct-answer' : 'wrong-answer') : ''; ?>">
                                                                    <label class="answer-label">
                                                                        <input type="radio"
                                                                            name="ans_id[<?php echo $index; ?>]"
                                                                            value="<?php echo $answerId; ?>"
                                                                            <?php echo $isSelected ? 'checked' : ''; ?>
                                                                            <?php echo $showResults ? 'disabled' : 'required'; ?>>
                                                                        <span class="answer-text"><?php echo $answerText; ?></span>
                                                                        <?php if ($showResults && $isCorrect): ?>
                                                                            <i class="fas fa-check-circle text-success ms-2"></i>
                                                                        <?php elseif ($showResults && $isSelected && !$isCorrect): ?>
                                                                            <i class="fas fa-times-circle text-danger ms-2"></i>
                                                                        <?php endif; ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div class="alert alert-warning">
                                                            Hakuna majibu yaliyopatikana kwa swali hili.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <input type="hidden" name="qn_id[]" value="<?php echo $question['id']; ?>">
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (!$showResults && !$hasAnswered): ?>
                                            <div class="text-center mt-4">
                                                <button type="submit" name="submit_answers" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-check"></i> Tuma Majibu
                                                </button>
                                            </div>
                                        <?php elseif ($hasAnswered): ?>
                                            <div class="text-center mt-4">
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Umekwisha jibu maswali haya. Unaweza kurudia kama unataka kuboresha matokeo yako.
                                                </div>
                                                <button type="submit" name="submit_answers" class="btn btn-warning btn-lg" disabled>
                                                    <i class="fas fa-redo"></i> Rudia Majaribio (Imekamilika)
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <!-- Quiz Results Summary -->
                                            <div class="quiz-results-summary mb-4 p-4 bg-light rounded">
                                                <h5 class="text-center mb-3">
                                                    <i class="fas fa-trophy text-warning"></i>
                                                    Matokeo Yako
                                                </h5>
                                                <?php
                                                // Calculate user's score
                                                $totalQuestions = count($questions);
                                                $correctAnswers = 0;

                                                foreach ($questions as $question) {
                                                    $userAnswerId = $quizModel->getUserAnswerForQuestion($currentUser['id'], $question['id']);
                                                    if ($userAnswerId) {
                                                        // Check if answer is correct
                                                        $answers = $quizModel->getAnswersByQuestion($question['id']);
                                                        foreach ($answers as $answer) {
                                                            if ($answer['id'] == $userAnswerId && $answer['status'] == 'true') {
                                                                $correctAnswers++;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }

                                                $scorePercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
                                                ?>
                                                <div class="row text-center">
                                                    <div class="col-md-4">
                                                        <div class="result-stat">
                                                            <h3 class="text-primary"><?php echo $correctAnswers; ?></h3>
                                                            <small>Majibu Sahihi</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="result-stat">
                                                            <h3 class="text-success"><?php echo $totalQuestions; ?></h3>
                                                            <small>Maswali Yote</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="result-stat">
                                                            <h3 class="text-info"><?php echo $scorePercentage; ?>%</h3>
                                                            <small>Asilimia</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center mt-3">
                                                    <?php if ($scorePercentage >= 80): ?>
                                                        <span class="badge bg-success fs-6">
                                                            <i class="fas fa-star"></i> Umejifunza vizuri!
                                                        </span>
                                                    <?php elseif ($scorePercentage >= 60): ?>
                                                        <span class="badge bg-warning fs-6">
                                                            <i class="fas fa-thumbs-up"></i> Umejifunza!
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info fs-6">
                                                            <i class="fas fa-lightbulb"></i> Jaribu tena!
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="text-center mt-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <button type="submit" name="submit_answers" class="btn btn-warning btn-lg w-100">
                                                            <i class="fas fa-redo"></i> Rudia Majaribio
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php
                                                        // Find the next lesson
                                                        $currentIndex = array_search($videoId, array_column($courseVideos, 'id'));
                                                        $hasNext = $currentIndex !== false && $currentIndex < count($courseVideos) - 1;
                                                        ?>
                                                        <?php if ($hasNext): ?>
                                                            <a href="<?php echo app_url('user/learn.php'); ?>?course_id=<?php echo $courseId; ?>&video_id=<?php echo $courseVideos[$currentIndex + 1]['id']; ?>" class="btn btn-success btn-lg w-100">
                                                                <i class="fas fa-arrow-right"></i> Somo Linalofuata
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo app_url('user/courses.php'); ?>" class="btn btn-success btn-lg w-100">
                                                                <i class="fas fa-home"></i> Rudi kwenye Kozi
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
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

            // Prevent form submission if user has already answered
            const quizForm = document.getElementById('quizForm');
            if (quizForm) {
                const submitButton = quizForm.querySelector('button[type="submit"]');
                if (submitButton && submitButton.disabled) {
                    quizForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        alert('Umekwisha jibu maswali haya. Haiwezi kurudia tena.');
                    });
                }
            }
        });
    </script>
</body>

</html>