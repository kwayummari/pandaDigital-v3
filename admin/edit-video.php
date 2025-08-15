<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Video.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$videoModel = new Video();

$success = '';
$error = '';
$video = null;

// Get video ID from URL
$videoId = $_GET['id'] ?? null;
if (!$videoId || !is_numeric($videoId)) {
    header('Location: videos.php');
    exit;
}

// Get video data
$video = $videoModel->getVideoById($videoId);
if (!$video) {
    header('Location: videos.php');
    exit;
}

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_video') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $courseId = $_POST['course_id'];

    if (empty($error)) {
        try {
            if ($videoModel->updateVideo($videoId, $name, $description, $courseId)) {
                $success = "Video imebadilishwa kikamilifu!";
                // Refresh video data
                $video = $videoModel->getVideoById($videoId);
            } else {
                $error = "Imefeli kubadilisha video. Tafadhali jaribu tena.";
            }
        } catch (Exception $e) {
            error_log("Error updating video: " . $e->getMessage());
            $error = "Imefeli kubadilisha video. Tafadhali jaribu tena.";
        }
    }
}

// Get all courses for the dropdown
$courses = $videoModel->getAllCourses();
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Video - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-video-form {
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

        .video-info {
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
                            Hariri Video
                        </h1>
                        <p class="text-muted">Badilisha maelezo ya video</p>
                    </div>
                    <a href="videos.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Video
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

        <!-- Video Information Display -->
        <div class="video-info">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Maelezo ya Video</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> <?= htmlspecialchars($video['id']) ?></p>
                    <p><strong>Kozi:</strong> <?= htmlspecialchars($video['course_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Video URL:</strong></p>
                    <div class="text-break">
                        <a href="<?= htmlspecialchars($video['name']) ?>" target="_blank" class="text-decoration-none">
                            <?= htmlspecialchars($video['name']) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Video Form -->
        <div class="edit-video-form">
            <form method="POST" action="" id="editVideoForm">
                <input type="hidden" name="action" value="update_video">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            Linki ya Video <span class="required">*</span>
                        </label>
                        <input type="url" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($video['name'] ?? '') ?>"
                            placeholder="https://www.youtube.com/watch?v=..." required>
                        <div class="help-text">Linki kamili ya video (YouTube, Vimeo, n.k.)</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="course_id" class="form-label">
                            Chagua Kozi <span class="required">*</span>
                        </label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">--- Chagua Kozi ---</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" <?= ($video['course_id'] ?? '') == $course['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($course['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">Kozi ambayo video itahusishwa nayo</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">
                            Maelezo Mafupi
                        </label>
                        <textarea class="form-control form-textarea" id="description" name="description"
                            rows="4" placeholder="Maelezo mafupi ya video..."><?= htmlspecialchars($video['description'] ?? '') ?></textarea>
                        <div class="help-text">Maelezo ya video (si lazima, isizidi herufi 70)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="videos.php" class="btn btn-outline-secondary">
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
        document.getElementById('editVideoForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const courseId = document.getElementById('course_id').value;
            const description = document.getElementById('description').value.trim();

            if (!name) {
                e.preventDefault();
                alert('Linki ya video ni lazima.');
                document.getElementById('name').focus();
                return false;
            }

            if (!courseId) {
                e.preventDefault();
                alert('Tafadhali chagua kozi.');
                document.getElementById('course_id').focus();
                return false;
            }

            if (description && description.length > 70) {
                e.preventDefault();
                alert('Maelezo yasiizidi herufi 70.');
                document.getElementById('description').focus();
                return false;
            }
        });
    </script>
</body>

</html>