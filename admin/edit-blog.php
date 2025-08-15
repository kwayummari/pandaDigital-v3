<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

$blogId = $_GET['id'] ?? null;
if (!$blogId) {
    header('Location: blogs.php');
    exit;
}

// Initialize models
$blogModel = new Blog();

// Get blog details
$blog = $blogModel->getBlogById($blogId);
if (!$blog) {
    header('Location: blogs.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $maelezo = trim($_POST['maelezo'] ?? '');

        if (empty($name)) {
            $error = 'Kichwa cha habari ni lazima';
        } else {
            // Handle file upload if new image is provided
            $photoPath = $blog['photo'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/Blog/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = 'Uwezo wa picha tu: JPG, PNG, GIF';
                } else {
                    $fileName = 'blog_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                        $photoPath = $fileName;
                    }
                }
            }

            if (!isset($error)) {
                // Update blog using the Blog model
                $result = $blogModel->updateBlog($blogId, [
                    'name' => $name,
                    'maelezo' => $maelezo,
                    'photo' => $photoPath
                ]);

                if ($result) {
                    header('Location: blogs.php?success=1');
                    exit;
                } else {
                    $error = 'Haikuweza kuhifadhi mabadiliko';
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error updating blog: " . $e->getMessage());
        $error = 'Kuna tatizo la kuhifadhi blog';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Blog - <?= htmlspecialchars($blog['name'] ?? 'N/A') ?></title>
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
                    <i class="fas fa-edit me-2"></i>Hariri Blog
                </h4>
                <a href="blogs.php" class="btn btn-secondary">
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
                    <div class="col-md-12 mb-3">
                        <label for="blogName" class="form-label">Kichwa Cha Habari *</label>
                        <input type="text" class="form-control" id="blogName" name="name"
                            value="<?= htmlspecialchars($blog['name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="blogMaelezo" class="form-label">Maelezo</label>
                    <textarea class="form-control" id="blogMaelezo" name="maelezo" rows="6"><?= htmlspecialchars($blog['maelezo'] ?? '') ?></textarea>
                    <small class="form-text text-muted">Yasizidi maneno 1000</small>
                </div>

                <div class="mb-3">
                    <label for="blogPhoto" class="form-label">Picha ya Blog</label>
                    <?php if (!empty($blog['photo'])): ?>
                        <div class="mb-2">
                            <strong>Picha ya Sasa:</strong><br>
                            <img src="../uploads/Blog/<?= htmlspecialchars($blog['photo']) ?>" alt="Current Blog Image" class="current-image">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="blogPhoto" name="photo" accept="image/*">
                    <small class="form-text text-muted">Acha tupu kuhifadhi picha ya sasa</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="blogs.php" class="btn btn-secondary">Futa</a>
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