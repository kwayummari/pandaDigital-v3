<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/User.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

$courseId = $_GET['id'] ?? null;
if (!$courseId) {
    header('Location: courses.php');
    exit;
}

// Initialize models
$courseModel = new Course();
$userModel = new User();

// Get course details
$course = $courseModel->getCourseById($courseId);
if (!$course) {
    header('Location: courses.php');
    exit;
}

// Get all instructors for the dropdown
$instructors = $userModel->getAllInstructors();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title'] ?? '');
        $instructorId = $_POST['instructor_id'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
        $status = $_POST['status'] ?? 'draft';

        if (empty($title) || empty($instructorId)) {
            $error = 'Jina la kozi na mwalimu ni lazima';
        } else {
            // Handle file upload if new image is provided
            $photoPath = $course['photo'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/courses/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = 'Uwezo wa picha tu: JPG, PNG, GIF';
                } else {
                    $fileName = 'course_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $photoPath = $fileName;
                    }
                }
            }

            if (!isset($error)) {
                // Update course using the Course model
                $courseModel = new Course();
                $result = $courseModel->updateCourse($courseId, [
                    'name' => $title,
                    'description' => $description,
                    'instructor_id' => $instructorId,
                    'price' => $price > 0 ? $price : 0,
                    'status' => $status,
                    'photo' => $photoPath
                ]);

                if ($result) {
                    header('Location: courses.php?success=1');
                    exit;
                } else {
                    $error = 'Haikuweza kuhifadhi mabadiliko';
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error updating course: " . $e->getMessage());
        $error = 'Kuna tatizo la kuhifadhi kozi';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Kozi - <?= htmlspecialchars($course['title'] ?? 'N/A') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        .btn-primary {
            background: #000;
            border-color: #000;
        }

        .btn-primary:hover {
            background: #333;
            border-color: #333;
        }

        .btn-secondary {
            background: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
        }

        .current-image {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .edit-form {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <div class="edit-form">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Hariri Kozi
                </h4>
                <a href="courses.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Rudi Nyuma
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="courseTitle" class="form-label">Jina la Kozi *</label>
                        <input type="text" class="form-control" id="courseTitle" name="title"
                            value="<?= htmlspecialchars($course['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="courseInstructor" class="form-label">Mwalimu *</label>
                        <select class="form-select" id="courseInstructor" name="instructor_id" required>
                            <option value="">Chagua mwalimu</option>
                            <?php foreach ($instructors as $instructor): ?>
                                <option value="<?= $instructor['id'] ?>"
                                    <?= ($course['instructor_id'] ?? '') == $instructor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($instructor['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="courseDescription" class="form-label">Maelezo</label>
                    <textarea class="form-control" id="courseDescription" name="description" rows="4"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="coursePrice" class="form-label">Bei</label>
                        <input type="number" class="form-control" id="coursePrice" name="price"
                            value="<?= $course['price'] ?? 0 ?>" min="0" step="100">
                        <small class="form-text text-muted">Acha tupu kwa kozi ya bure</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="courseStatus" class="form-label">Hali</label>
                        <select class="form-select" id="courseStatus" name="status">
                            <option value="draft" <?= ($course['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Rasimu</option>
                            <option value="pending" <?= ($course['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Inasubiri</option>
                            <option value="published" <?= ($course['status'] ?? '') === 'published' ? 'selected' : '' ?>>Imechapishwa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="courseImage" class="form-label">Picha ya Kozi</label>
                    <?php if (!empty($course['photo'])): ?>
                        <div class="mb-2">
                            <strong>Picha ya Sasa:</strong><br>
                            <img src="../uploads/courses/<?= htmlspecialchars($course['photo']) ?>" alt="Current Course Image" class="current-image">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="courseImage" name="image" accept="image/*">
                    <small class="form-text text-muted">Acha tupu kuhifadhi picha ya sasa</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="courses.php" class="btn btn-secondary">Futa</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Hifadhi Mabadiliko
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>
</body>

</html>