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
$question = null;

// Get question ID from URL
$questionId = $_GET['id'] ?? null;
if (!$questionId || !is_numeric($questionId)) {
    header('Location: questions.php');
    exit;
}

// Get question data
$question = $questionModel->getQuestionById($questionId);
if (!$question) {
    header('Location: questions.php');
    exit;
}

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_question') {
    $questionText = trim($_POST['question_text']);
    $videoId = $_POST['video_id'];

    if (empty($error)) {
        try {
            if ($questionModel->updateQuestion($questionId, $questionText, $videoId)) {
                $success = "Swali limebadilishwa kikamilifu!";
                // Refresh question data
                $question = $questionModel->getQuestionById($questionId);
            } else {
                $error = "Imefeli kubadilisha swali. Tafadhali jaribu tena.";
            }
        } catch (Exception $e) {
            error_log("Error updating question: " . $e->getMessage());
            $error = "Imefeli kubadilisha swali. Tafadhali jaribu tena.";
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
    <title>Hariri Swali - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-question-form {
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

        .question-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-edit text-primary me-2"></i>
                            Hariri Swali
                        </h1>
                        <p class="text-muted">Badilisha maelezo ya swali</p>
                    </div>
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

        <!-- Question Information Display -->
        <div class="question-info">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Maelezo ya Swali</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> <?= htmlspecialchars($question['id']) ?></p>
                    <p><strong>Kozi:</strong> <?= htmlspecialchars($question['course_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Video:</strong> <?= htmlspecialchars($question['video_title'] ?? 'N/A') ?></p>
                    <p><strong>Video ID:</strong> <?= htmlspecialchars($question['video_id'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Edit Question Form -->
        <div class="edit-question-form">
            <form method="POST" action="" id="editQuestionForm">
                <input type="hidden" name="action" value="update_question">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="question_text" class="form-label">
                            Swali <span class="required">*</span>
                        </label>
                        <textarea class="form-control form-textarea" id="question_text" name="question_text"
                            rows="4" placeholder="Andika swali hapa..." required><?= htmlspecialchars($question['question_text'] ?? '') ?></textarea>
                        <div class="help-text">Swali kamili la masomo</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="video_id" class="form-label">
                            Chagua Video <span class="required">*</span>
                        </label>
                        <select class="form-select" id="video_id" name="video_id" required>
                            <option value="">--- Chagua Video ---</option>
                            <?php foreach ($videos as $video): ?>
                                <option value="<?= $video['id'] ?>" <?= ($question['video_id'] ?? '') == $video['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($video['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">Video ambayo swali itahusishwa nayo</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="questions.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Ghairi
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Hifadhi Mabadiliko
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>

    <script>
        // Form validation
        document.getElementById('editQuestionForm').addEventListener('submit', function(e) {
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
    </script>
</body>

</html>