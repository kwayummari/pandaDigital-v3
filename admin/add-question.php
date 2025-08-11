<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

$success = $error = '';

// Handle form submission
if ($_POST && isset($_POST['add_question'])) {
    $videoId = $_POST['video_id'];
    $questionText = trim($_POST['question_text']);
    $questionType = $_POST['question_type'];
    $correctAnswer = trim($_POST['correct_answer']);
    $options = $_POST['options'] ?? [];

    // Validation
    if (empty($videoId) || empty($questionText) || empty($correctAnswer)) {
        $error = "Tafadhali jaza sehemu zote muhimu (Video, Swali, na Jibu Sahihi).";
    } elseif (strlen($questionText) < 10) {
        $error = "Swali lazima liwe na herufi 10 au zaidi.";
    } elseif (strlen($correctAnswer) < 2) {
        $error = "Jibu sahihi lazima liwe na herufi 2 au zaidi.";
    } elseif ($questionType === 'multiple_choice' && count(array_filter($options)) < 2) {
        $error = "Maswali ya chaguo lazima yawe na chaguo 2 au zaidi.";
    } else {
        // Add question
        $result = $courseModel->addQuestion($videoId, $questionText, $questionType, $correctAnswer, $options);
        if ($result) {
            $success = "Swali limeongezwa kikamilifu!";
            // Clear form data
            $questionText = $correctAnswer = '';
            $videoId = '';
            $options = ['', '', '', ''];
        } else {
            $error = "Imefeli kuongeza swali. Tafadhali jaribu tena.";
        }
    }
}

// Get all videos for selection
$videos = $courseModel->getAllVideosWithCourses();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Swali - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #662e91;
            --secondary-color: #FFC10B;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
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

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 46, 145, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .question-preview {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .help-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .required {
            color: var(--accent-color);
        }

        .option-group {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .option-input {
            margin-bottom: 10px;
        }

        .question-type-info {
            background: var(--info-color);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Panda Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="/admin/questions.php">
                    <i class="fas fa-question-circle me-1"></i> Maswali
                </a>
                <a class="nav-link" href="/admin/videos.php">
                    <i class="fas fa-video me-1"></i> Video
                </a>
                <a class="nav-link" href="/admin/courses.php">
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
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Ongeza Swali Jipya
                    </h1>
                    <p class="text-muted">Ongeza swali jipya kwenye video</p>
                </div>
            </div>

            <!-- Question Preview Section -->
            <div class="question-preview">
                <i class="fas fa-question-circle fa-3x mb-3"></i>
                <h3>Ongeza Swali Jipya</h3>
                <p class="mb-0">Jaza maelezo ya swali ili wanafunzi waweze kujifunza</p>
            </div>

            <!-- Alerts -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add Question Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Maelezo ya Swali
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <!-- Video Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="video_id" class="form-label">
                                    Chagua Video <span class="required">*</span>
                                </label>
                                <select class="form-select" id="video_id" name="video_id" required>
                                    <option value="">-- Chagua Video --</option>
                                    <?php foreach ($videos as $video): ?>
                                        <option value="<?php echo $video['id']; ?>"
                                            <?php echo (isset($_POST['video_id']) && $_POST['video_id'] == $video['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($video['title'] . ' (' . $video['course_name'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Chagua video ambayo swali litakuwa sehemu yake</div>
                            </div>

                            <!-- Question Type -->
                            <div class="col-md-6 mb-3">
                                <label for="question_type" class="form-label">
                                    Aina ya Swali <span class="required">*</span>
                                </label>
                                <select class="form-select" id="question_type" name="question_type" required>
                                    <option value="multiple_choice" <?php echo (isset($_POST['question_type']) && $_POST['question_type'] == 'multiple_choice') ? 'selected' : ''; ?>>
                                        Swali la Chaguo
                                    </option>
                                    <option value="true_false" <?php echo (isset($_POST['question_type']) && $_POST['question_type'] == 'true_false') ? 'selected' : ''; ?>>
                                        Kweli au Si Kweli
                                    </option>
                                    <option value="short_answer" <?php echo (isset($_POST['question_type']) && $_POST['question_type'] == 'short_answer') ? 'selected' : ''; ?>>
                                        Majibu Mafupi
                                    </option>
                                </select>
                                <div class="help-text">Chagua aina ya swali unayotaka kuongeza</div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-4">
                            <label for="question_text" class="form-label">
                                Swali <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3"
                                placeholder="Andika swali hapa..." required><?php echo htmlspecialchars($_POST['question_text'] ?? ''); ?></textarea>
                            <div class="help-text">Swali kamili linaloeleweka na wanafunzi</div>
                        </div>

                        <!-- Question Type Info -->
                        <div class="question-type-info" id="questionTypeInfo">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Maelezo ya Aina ya Swali</h6>
                            <p class="mb-0" id="typeDescription">Chagua aina ya swali ili kuona maelezo zaidi.</p>
                        </div>

                        <!-- Multiple Choice Options -->
                        <div class="option-group" id="multipleChoiceOptions" style="display: none;">
                            <h6 class="mb-3">
                                <i class="fas fa-list-ul text-primary me-2"></i>
                                Chaguo za Swali
                            </h6>
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="option-input">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-circle text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" name="options[]"
                                            placeholder="Chaguo <?php echo $i + 1; ?>"
                                            value="<?php echo htmlspecialchars($_POST['options'][$i] ?? ''); ?>">
                                    </div>
                                </div>
                            <?php endfor; ?>
                            <div class="help-text">Jaza chaguo 2-4 za swali. Chaguo moja lazima iwe sahihi.</div>
                        </div>

                        <!-- Correct Answer -->
                        <div class="mb-4">
                            <label for="correct_answer" class="form-label">
                                Jibu Sahihi <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="correct_answer" name="correct_answer"
                                value="<?php echo htmlspecialchars($_POST['correct_answer'] ?? ''); ?>"
                                placeholder="Jibu sahihi la swali..." required>
                            <div class="help-text" id="correctAnswerHelp">Jibu sahihi la swali hili</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/admin/questions.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Rudi kwenye Maswali
                            </a>
                            <button type="submit" name="add_question" class="btn btn-primary-custom">
                                <i class="fas fa-plus me-2"></i>
                                Ongeza Swali
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle text-info me-2"></i>
                        Vidokezo vya Kuongeza Swali
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i>Jinsi ya Kuongeza Swali</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Chagua video sahihi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Swali liwe wazi na linaeleweka</li>
                                <li><i class="fas fa-check text-success me-2"></i>Jibu sahihi liwe sahihi</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chaguo ziwe na maana</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle text-info me-2"></i>Aina za Maswali</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-list text-primary me-2"></i><strong>Chaguo:</strong> Chaguo 2-4</li>
                                <li><i class="fas fa-toggle-on text-primary me-2"></i><strong>Kweli/Si Kweli:</strong> Jibu moja tu</li>
                                <li><i class="fas fa-keyboard text-primary me-2"></i><strong>Majibu Mafupi:</strong> Majibu ya maandishi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Question type change handler
        document.getElementById('question_type').addEventListener('change', function() {
            const questionType = this.value;
            const multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
            const typeDescription = document.getElementById('typeDescription');
            const correctAnswerHelp = document.getElementById('correctAnswerHelp');

            // Hide/show multiple choice options
            if (questionType === 'multiple_choice') {
                multipleChoiceOptions.style.display = 'block';
                typeDescription.innerHTML = '<strong>Swali la Chaguo:</strong> Wanafunzi watachagua jibu sahihi kutoka kwenye chaguo 2-4. Jaza chaguo zote na uweke jibu sahihi.';
                correctAnswerHelp.innerHTML = 'Jibu sahihi lazima liwe moja ya chaguo ulizozoweka hapo juu.';
            } else if (questionType === 'true_false') {
                multipleChoiceOptions.style.display = 'none';
                typeDescription.innerHTML = '<strong>Kweli au Si Kweli:</strong> Wanafunzi watachagua kati ya "Kweli" au "Si Kweli". Jibu sahihi lazima liwe moja ya hizi mbili.';
                correctAnswerHelp.innerHTML = 'Jibu sahihi lazima liwe "Kweli" au "Si Kweli".';
            } else if (questionType === 'short_answer') {
                multipleChoiceOptions.style.display = 'none';
                typeDescription.innerHTML = '<strong>Majibu Mafupi:</strong> Wanafunzi wataandika jibu lao. Jibu sahihi lazima liwe sahihi na linaeleweka.';
                correctAnswerHelp.innerHTML = 'Jibu sahihi la swali hili. Wanafunzi watalinganishwa na jibu hili.';
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const videoId = document.getElementById('video_id').value;
            const questionText = document.getElementById('question_text').value.trim();
            const correctAnswer = document.getElementById('correct_answer').value.trim();
            const questionType = document.getElementById('question_type').value;

            if (!videoId) {
                e.preventDefault();
                alert('Tafadhali chagua video.');
                document.getElementById('video_id').focus();
                return false;
            }

            if (!questionText || questionText.length < 10) {
                e.preventDefault();
                alert('Swali lazima liwe na herufi 10 au zaidi.');
                document.getElementById('question_text').focus();
                return false;
            }

            if (!correctAnswer || correctAnswer.length < 2) {
                e.preventDefault();
                alert('Jibu sahihi lazima liwe na herufi 2 au zaidi.');
                document.getElementById('correct_answer').focus();
                return false;
            }

            if (questionType === 'multiple_choice') {
                const options = document.querySelectorAll('input[name="options[]"]');
                const filledOptions = Array.from(options).filter(opt => opt.value.trim() !== '');

                if (filledOptions.length < 2) {
                    e.preventDefault();
                    alert('Maswali ya chaguo lazima yawe na chaguo 2 au zaidi.');
                    return false;
                }

                // Check if correct answer is one of the options
                const optionValues = filledOptions.map(opt => opt.value.trim().toLowerCase());
                if (!optionValues.includes(correctAnswer.toLowerCase())) {
                    e.preventDefault();
                    alert('Jibu sahihi lazima liwe moja ya chaguo ulizozoweka.');
                    return false;
                }
            }
        });

        // Initialize question type info on page load
        document.addEventListener('DOMContentLoaded', function() {
            const questionType = document.getElementById('question_type');
            if (questionType.value) {
                questionType.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>

</html>