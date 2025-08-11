<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Get video ID and course ID from URL
$videoId = $_GET['video_id'] ?? null;
$courseId = $_GET['course_id'] ?? null;

if (!$videoId || !$courseId) {
    header('Location: /user/courses.php?error=invalid_parameters');
    exit();
}

// Get course information
$course = $courseModel->getCourseById($courseId, $currentUser['id']);
if (!$course) {
    header('Location: /user/courses.php?error=course_not_found');
    exit();
}

// Get quiz results
$quizResults = $quizModel->getQuizResults($currentUser['id'], $videoId);
if (!$quizResults) {
    header('Location: /user/courses.php?error=quiz_not_found');
    exit();
}

// Get course videos for navigation
$courseVideos = $courseModel->getCourseVideos($courseId);
$currentVideoIndex = 0;
foreach ($courseVideos as $index => $video) {
    if ($video['id'] == $videoId) {
        $currentVideoIndex = $index;
        break;
    }
}

// Get next video for navigation
$nextVideo = null;
if ($currentVideoIndex < count($courseVideos) - 1) {
    $nextVideo = $courseVideos[$currentVideoIndex + 1];
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matokeo ya Jaribio - <?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

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
            --danger-color: #e74c3c;
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

        .score-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 8px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .score-excellent {
            background: var(--success-color);
        }

        .score-good {
            background: var(--warning-color);
        }

        .score-poor {
            background: var(--danger-color);
        }

        .question-result {
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .question-result.correct {
            border-left: 4px solid var(--success-color);
        }

        .question-result.incorrect {
            border-left: 4px solid var(--danger-color);
        }

        .answer-option {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 2px solid transparent;
        }

        .answer-option.correct {
            background-color: #d4edda;
            border-color: var(--success-color);
            color: #155724;
        }

        .answer-option.incorrect {
            background-color: #f8d7da;
            border-color: var(--danger-color);
            color: #721c24;
        }

        .answer-option.user-selected {
            background-color: #fff3cd;
            border-color: var(--warning-color);
            color: #856404;
        }

        .btn-next {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-next:hover {
            background: #229954;
            transform: translateY(-2px);
        }

        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .progress-bar {
            height: 10px;
            border-radius: 5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
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
                                Matokeo ya Jaribio
                            </li>
                        </ol>
                    </nav>

                    <h1 class="h3 mb-2">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Matokeo ya Jaribio
                    </h1>
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($course['name']); ?> -
                        Somo la <?php echo $currentVideoIndex + 1; ?> kati ya <?php echo count($courseVideos); ?>
                    </p>
                </div>
            </div>

            <!-- Score Summary -->
            <div class="score-card">
                <div class="score-circle <?php
                                            if ($quizResults['score_percentage'] >= 80) echo 'score-excellent';
                                            elseif ($quizResults['score_percentage'] >= 60) echo 'score-good';
                                            else echo 'score-poor';
                                            ?>">
                    <?php echo round($quizResults['score_percentage']); ?>%
                </div>
                <h2 class="mb-2">
                    <?php
                    if ($quizResults['score_percentage'] >= 80) echo 'Hongera! Umefanikiwa sana!';
                    elseif ($quizResults['score_percentage'] >= 60) echo 'Vizuri! Unaweza bora zaidi!';
                    else echo 'Jaribu tena! Usikate tamaa!';
                    ?>
                </h2>
                <p class="mb-0">
                    Umejibu <?php echo $quizResults['correct_answers']; ?> kati ya <?php echo $quizResults['total_questions']; ?> maswali kwa usahihi
                </p>
            </div>

            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $quizResults['total_questions']; ?></div>
                    <div class="stat-label">Jumla ya Maswali</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $quizResults['correct_answers']; ?></div>
                    <div class="stat-label">Majibu Sahihi</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $quizResults['total_questions'] - $quizResults['correct_answers']; ?></div>
                    <div class="stat-label">Majibu Makosa</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon text-info">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value"><?php echo $quizResults['grade']; ?></div>
                    <div class="stat-label">Alama</div>
                </div>
            </div>

            <!-- Course Progress -->
            <div class="progress-section">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2"></i>
                    Maendeleo ya Kozi
                </h5>

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
                        <small class="text-muted">Maendeleo ya Kozi</small>
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

            <!-- Detailed Results -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Matokeo ya Kila Swali
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($quizResults['questions'] as $index => $question): ?>
                        <div class="question-result <?php echo $question['is_correct'] ? 'correct' : 'incorrect'; ?>">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                        <?php echo htmlspecialchars($question['text']); ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <?php
                                        $answerClass = 'answer-option';
                                        if ($answer['is_correct']) $answerClass .= ' correct';
                                        if ($question['user_answer'] == $answer['id'] && !$answer['is_correct']) {
                                            $answerClass .= ' incorrect user-selected';
                                        } elseif ($question['user_answer'] == $answer['id'] && $answer['is_correct']) {
                                            $answerClass .= ' user-selected';
                                        }
                                        ?>
                                        <div class="<?php echo $answerClass; ?>">
                                            <?php if ($answer['is_correct']): ?>
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            <?php elseif ($question['user_answer'] == $answer['id'] && !$answer['is_correct']): ?>
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle text-muted me-2"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($answer['text']); ?>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if ($question['is_correct']): ?>
                                        <div class="alert alert-success mt-2 mb-0">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Jibu sahihi!</strong> Umejibu swali hili kwa usahihi.
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-danger mt-2 mb-0">
                                            <i class="fas fa-times-circle me-2"></i>
                                            <strong>Jibu si sahihi.</strong> Jibu sahihi lilikuwa:
                                            <?php
                                            foreach ($question['answers'] as $answer) {
                                                if ($answer['is_correct']) {
                                                    echo '<strong>' . htmlspecialchars($answer['text']) . '</strong>';
                                                    break;
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="text-center mt-4">
                <a href="/user/course.php?id=<?php echo $courseId; ?>" class="btn btn-outline-primary me-3">
                    <i class="fas fa-arrow-left me-2"></i>
                    Rudi kwenye Kozi
                </a>

                <?php if ($nextVideo): ?>
                    <a href="/user/learn.php?course_id=<?php echo $courseId; ?>&video_id=<?php echo $nextVideo['id']; ?>"
                        class="btn btn-next text-white">
                        <i class="fas fa-arrow-right me-2"></i>
                        Endelea na Somo Linalofuata
                    </a>
                <?php else: ?>
                    <div class="alert alert-success d-inline-block">
                        <i class="fas fa-trophy me-2"></i>
                        Umekamilisha kozi hii! Hongera!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>