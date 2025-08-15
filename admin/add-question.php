<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Question.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$questionModel = new Question();

$success = '';
$error = '';

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_question') {
    $questionText = trim($_POST['question_text']);
    $videoId = $_POST['video_id'];

    // Validation
    if (empty($questionText)) {
        $error = "Swali ni lazima.";
    } elseif (strlen($questionText) < 5) {
        $error = "Swali lazima liwe na herufi 5 au zaidi.";
    } elseif (empty($videoId)) {
        $error = "Tafadhali chagua video.";
    } else {
        try {
            if ($questionModel->addQuestion($questionText, $videoId)) {
                $success = "Swali limeongezwa kikamilifu!";
                // Clear form data
                $_POST = array();
            } else {
                $error = "Imefeli kuongeza swali. Tafadhali jaribu tena.";
            }
        } catch (Exception $e) {
            error_log("Error adding question: " . $e->getMessage());
            $error = "Imefeli kuongeza swali. Tafadhali jaribu tena.";
        }
    }
}

// Get all videos for the dropdown
$videos = $questionModel->getAllVideos();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Swali - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .add-question-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select,
        .form-textarea {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .required {
            color: #dc3545;
        }

        .help-text {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2">
                        <i class="fas fa-plus-circle text-primary me-3"></i>
                        Ongeza Swali Jipya
                    </h1>
                    <p class="text-muted mb-0">Ongeza swali jipya la masomo kwenye mfumo</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="questions.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Maswali
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Question Form -->
        <div class="add-question-form">
            <form method="POST" action="" id="addQuestionForm">
                <input type="hidden" name="action" value="add_question">

                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-question-circle text-primary me-2"></i>
                        Maelezo ya Swali
                    </h5>

                    <div class="mb-3">
                        <label for="question_text" class="form-label">
                            Swali <span class="required">*</span>
                        </label>
                        <textarea class="form-control form-textarea" id="question_text" name="question_text"
                            rows="5" placeholder="Andika swali kamili la masomo hapa..." required><?= htmlspecialchars($_POST['question_text'] ?? '') ?></textarea>
                        <div class="help-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Swali lazima liwe na herufi 5 au zaidi na liwe na maana kamili
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="fas fa-video text-primary me-2"></i>
                        Chagua Video
                    </h5>

                    <div class="mb-3">
                        <label for="video_id" class="form-label">
                            Video <span class="required">*</span>
                        </label>
                        <select class="form-select" id="video_id" name="video_id" required>
                            <option value="">--- Chagua Video ---</option>
                            <?php foreach ($videos as $video): ?>
                                <option value="<?= $video['id'] ?>" <?= ($_POST['video_id'] ?? '') == $video['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($video['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Video ambayo swali itahusishwa nayo. Hii itasaidia wanafunzi kupata majibu sahihi
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="questions.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Ghairi
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Ongeza Swali
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="form-section">
            <h6 class="mb-3">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                Vidokezo vya Kuongeza Swali
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Swali lazima liwe na maana kamili</li>
                        <li><i class="fas fa-check text-success me-2"></i>Chagua video sahihi ya swali</li>
                        <li><i class="fas fa-check text-success me-2"></i>Hakikisha swali ni la masomo</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Swali lazima liwe na herufi 5 au zaidi</li>
                        <li><i class="fas fa-check text-success me-2"></i>Video lazima ihusishwe na swali</li>
                        <li><i class="fas fa-check text-success me-2"></i>Swali lazima liwe na muktadha</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>

    <script>
        // Form validation
        document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
            const questionText = document.getElementById('question_text').value.trim();
            const videoId = document.getElementById('video_id').value;

            if (!questionText || questionText.length < 5) {
                e.preventDefault();
                alert('Swali lazima liwe na herufi 5 au zaidi.');
                document.getElementById('question_text').focus();
                return false;
            }

            if (!videoId) {
                e.preventDefault();
                alert('Tafadhali chagua video.');
                document.getElementById('video_id').focus();
                return false;
            }
        });

        // Character counter for question text
        document.getElementById('question_text').addEventListener('input', function() {
            const length = this.value.length;
            const minLength = 5;
            const counter = document.getElementById('charCounter');

            if (length < minLength) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
    </script>
</body>

</html>