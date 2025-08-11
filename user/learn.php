<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Get course ID and video ID from URL
$courseId = $_GET['course_id'] ?? null;
$videoId = $_GET['video_id'] ?? null;

if (!$courseId || !$videoId) {
    header('Location: /user/courses.php?error=invalid_parameters');
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

// Check payment status for paid courses
if ($course['courseIsPaidStatusId'] == 1 && $enrollment['payment_status'] != '1') {
    header('Location: /user/pay.php?id=' . $courseId);
    exit();
}

// Get course videos
$courseVideos = $courseModel->getCourseVideos($courseId);
$currentVideo = null;

foreach ($courseVideos as $video) {
    if ($video['id'] == $videoId) {
        $currentVideo = $video;
        break;
    }
}

if (!$currentVideo) {
    header('Location: /user/courses.php?error=video_not_found');
    exit();
}

// Get video questions
$questions = $courseModel->getVideoQuestions($videoId);

// Handle quiz submission
if ($_POST && isset($_POST['submit_quiz'])) {
    $answers = $_POST['answers'] ?? [];

    if (!empty($answers)) {
        $result = $quizModel->submitQuizAnswers($currentUser['id'], $videoId, $answers);
        if ($result) {
            $success = "Majibu yako yamekusanywa kikamilifu!";
            // Redirect to results page
            header('Location: /user/quiz-results.php?video_id=' . $videoId . '&course_id=' . $courseId);
            exit();
        } else {
            $error = "Imefeli kusanya majibu. Tafadhali jaribu tena.";
        }
    } else {
        $error = "Tafadhali jibu maswali yote.";
    }
}

// Check if user has already completed this quiz
$hasCompletedQuiz = $quizModel->hasCompletedQuiz($currentUser['id'], $videoId);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jifunze - <?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e67e22;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            background: #000;
            border-radius: 15px;
            overflow: hidden;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .lesson-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .lesson-item {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .lesson-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .lesson-item.active {
            background-color: var(--primary-color);
            color: white;
            border-left-color: var(--accent-color);
        }

        .lesson-item.completed {
            border-left-color: var(--success-color);
            background-color: #d4edda;
        }

        .lesson-item.completed::after {
            content: "✓";
            float: right;
            color: var(--success-color);
            font-weight: bold;
        }

        .quiz-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
        }

        .question-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .answer-option {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .answer-option:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
        }

        .answer-option.selected {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
        }

        .btn-submit {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-submit:hover {
            background: #229954;
            transform: translateY(-2px);
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/user/dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>
                Panda Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/user/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="/user/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Course Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="/user/courses.php" class="text-decoration-none">
                                            <i class="fas fa-book me-1"></i> Kozi
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="/user/course.php?id=<?php echo $courseId; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($course['name']); ?>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <?php echo htmlspecialchars($currentVideo['description']); ?>
                                    </li>
                                </ol>
                            </nav>

                            <h1 class="h3 mb-2"><?php echo htmlspecialchars($currentVideo['description']); ?></h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-play-circle me-1"></i>
                                Somo la <?php echo array_search($currentVideo, $courseVideos) + 1; ?> kati ya <?php echo count($courseVideos); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Video Player -->
                    <div class="card mb-4">
                        <div class="card-body p-0">
                            <div class="video-container">
                                <?php if (!empty($currentVideo['video_url'])): ?>
                                    <iframe src="<?php echo htmlspecialchars($currentVideo['video_url']); ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center text-white">
                                            <i class="fas fa-video fa-3x mb-3"></i>
                                            <h5>Video haijapatikana</h5>
                                            <p>Video hii bado haijapakiwa au haijapatikana.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Section -->
                    <?php if (!empty($questions) && !$hasCompletedQuiz): ?>
                        <div class="quiz-section">
                            <h3 class="mb-4">
                                <i class="fas fa-question-circle me-2"></i>
                                Jaribio la Maswali
                            </h3>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="quizForm">
                                <?php foreach ($questions as $index => $question): ?>
                                    <div class="question-card">
                                        <h5 class="mb-3">
                                            <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                            <?php echo htmlspecialchars($question['name']); ?>
                                        </h5>

                                        <?php
                                        $answerIds = explode(',', $question['answer_ids']);
                                        $answerNames = explode(',', $question['answer_names']);
                                        ?>

                                        <?php for ($i = 0; $i < count($answerIds); $i++): ?>
                                            <div class="answer-option" onclick="selectAnswer(this, '<?php echo $question['id']; ?>', '<?php echo $answerIds[$i]; ?>')">
                                                <input type="radio"
                                                    name="answers[<?php echo $question['id']; ?>]"
                                                    value="<?php echo $answerIds[$i]; ?>"
                                                    style="display: none;">
                                                <span class="answer-text">
                                                    <?php echo htmlspecialchars($answerNames[$i]); ?>
                                                </span>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php endforeach; ?>

                                <div class="text-center mt-4">
                                    <button type="submit" name="submit_quiz" class="btn btn-submit btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Maliza Jaribio
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($hasCompletedQuiz): ?>
                        <div class="alert alert-success text-center py-5">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>Umekamilisha somo hili!</h4>
                            <p>Umesha jibu maswali yote ya somo hili.</p>
                            <a href="/user/quiz-results.php?video_id=<?php echo $videoId; ?>&course_id=<?php echo $courseId; ?>"
                                class="btn btn-primary">
                                <i class="fas fa-chart-bar me-2"></i>
                                Tazama Matokeo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-3">
                    <!-- Course Progress -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Maendeleo ya Kozi
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $completedLessons = 0;
                            foreach ($courseVideos as $video) {
                                if ($quizModel->hasCompletedQuiz($currentUser['id'], $video['id'])) {
                                    $completedLessons++;
                                }
                            }
                            $progressPercentage = count($courseVideos) > 0 ? ($completedLessons / count($courseVideos)) * 100 : 0;
                            ?>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Maendeleo</small>
                                    <small class="text-muted"><?php echo round($progressPercentage); ?>%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success"
                                        style="width: <?php echo $progressPercentage; ?>%"></div>
                                </div>
                            </div>

                            <p class="mb-0 text-center">
                                <strong><?php echo $completedLessons; ?></strong> kati ya <strong><?php echo count($courseVideos); ?></strong> masomo yamekamilika
                            </p>
                        </div>
                    </div>

                    <!-- Course Lessons -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Masomo ya Kozi
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="lesson-list">
                                <?php foreach ($courseVideos as $index => $video): ?>
                                    <?php
                                    $isCurrentVideo = ($video['id'] == $videoId);
                                    $isCompleted = $quizModel->hasCompletedQuiz($currentUser['id'], $video['id']);
                                    $lessonClass = 'lesson-item';
                                    if ($isCurrentVideo) $lessonClass .= ' active';
                                    if ($isCompleted) $lessonClass .= ' completed';
                                    ?>

                                    <div class="<?php echo $lessonClass; ?>"
                                        onclick="window.location.href='?course_id=<?php echo $courseId; ?>&video_id=<?php echo $video['id']; ?>'">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <?php if ($isCompleted): ?>
                                                    <i class="fas fa-check-circle text-success"></i>
                                                <?php elseif ($isCurrentVideo): ?>
                                                    <i class="fas fa-play-circle text-primary"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-circle text-muted"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?php echo $index + 1; ?>. <?php echo htmlspecialchars($video['description']); ?></div>
                                                <small class="text-muted">
                                                    <?php echo $video['duration'] ?? 'Muda haujulikani'; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
        function selectAnswer(element, questionId, answerId) {
            // Remove selection from other options in this question
            const questionCard = element.closest('.question-card');
            questionCard.querySelectorAll('.answer-option').forEach(option => {
                option.classList.remove('selected');
                option.querySelector('input[type="radio"]').checked = false;
            });

            // Select this option
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;
        }

        // Form validation
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            const questions = document.querySelectorAll('.question-card');
            let allAnswered = true;

            questions.forEach(question => {
                const selectedAnswer = question.querySelector('input[type="radio"]:checked');
                if (!selectedAnswer) {
                    allAnswered = false;
                    question.style.border = '2px solid #dc3545';
                } else {
                    question.style.border = 'none';
                }
            });

            if (!allAnswered) {
                e.preventDefault();
                alert('Tafadhali jibu maswali yote kabla ya kuendelea.');
                return false;
            }
        });
    </script>
</body>

</html>