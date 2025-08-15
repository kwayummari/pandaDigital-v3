<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Fursa.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$fursaModel = new Fursa();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $error = 'Kichwa cha habari ni lazima';
        } elseif (empty($description)) {
            $error = 'Maelezo kuhusu fursa ni lazima';
        } else {
            // Handle file upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/Fursa/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $error = 'Uwezo wa picha tu: JPG, PNG, GIF';
                } else {
                    $fileName = 'fursa_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = $fileName;
                    } else {
                        $error = 'Imefeli kupakia picha';
                    }
                }
            }

            if (!isset($error)) {
                // Get current date and month
                $currentDate = date('Y-m-d H:i:s');
                $currentMonth = date('F'); // Full month name like "January"

                // Add opportunity using the Fursa model
                $result = $fursaModel->addOpportunity([
                    'name' => $name,
                    'description' => $description,
                    'image' => $imagePath,
                    'date' => $currentDate,
                    'month' => $currentMonth,
                    'date_created' => $currentDate
                ]);

                if ($result) {
                    header('Location: opportunities.php?success=1');
                    exit;
                } else {
                    $error = 'Haikuweza kuongeza fursa';
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error adding opportunity: " . $e->getMessage());
        $error = 'Kuna tatizo la kuongeza fursa';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Fursa Mpya - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .add-form {
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

        /* Layout fixes */
        .content-wrapper {
            padding: 20px 30px;
        }

        .add-form {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/admin_header.php'; ?>

    <div class="content-wrapper">
        <div class="add-form">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Ongeza Fursa Mpya
                </h4>
                <a href="opportunities.php" class="btn btn-secondary">
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
                        <label for="opportunityName" class="form-label">Kichwa Cha Habari *</label>
                        <input type="text" class="form-control" id="opportunityName" name="name"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        <small class="form-text text-muted">Jina la fursa linaloelezea yaliyomo</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="opportunityDescription" class="form-label">Maelezo Kuhusu Fursa *</label>
                    <textarea class="form-control" id="opportunityDescription" name="description" rows="6" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <small class="form-text text-muted">Yasizidi maneno 1000</small>
                </div>

                <div class="mb-3">
                    <label for="opportunityImage" class="form-label">Picha ya Fursa *</label>
                    <input type="file" class="form-control" id="opportunityImage" name="image" accept="image/*" required>
                    <small class="form-text text-muted">Uwezo wa picha tu: JPG, PNG, GIF</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="opportunities.php" class="btn btn-secondary">Futa</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Ongeza Fursa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>
</body>

</html>