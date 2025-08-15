<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Beneficiary.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$beneficiaryModel = new Beneficiary();

$success = '';
$error = '';
$beneficiary = null;

// Get beneficiary ID from URL
$beneficiaryId = $_GET['id'] ?? null;
if (!$beneficiaryId || !is_numeric($beneficiaryId)) {
    header('Location: beneficiaries.php');
    exit;
}

// Get beneficiary data
$beneficiary = $beneficiaryModel->getWanufaikaById($beneficiaryId);
if (!$beneficiary) {
    header('Location: beneficiaries.php');
    exit;
}

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_beneficiary') {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Handle file upload
    $photo = $beneficiary['photo']; // Keep existing photo by default
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/Wanufaika/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newPhoto = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newPhoto;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                // Delete old photo if it exists and is different
                if (!empty($beneficiary['photo']) && $beneficiary['photo'] !== $newPhoto) {
                    $oldPhotoPath = $uploadDir . $beneficiary['photo'];
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                $photo = $newPhoto;
            } else {
                $error = "Imefeli kupakia picha. Tafadhali jaribu tena.";
            }
        } else {
            $error = "Muonekano wa faili haujakubalika. Tafadhali tumia JPG, PNG au GIF tu.";
        }
    }

    if (empty($error)) {
        if ($beneficiaryModel->updateWanufaika($beneficiaryId, $name, $title, $description, $photo)) {
            $success = "Mwanufaika amebadilishwa kikamilifu!";
            // Refresh beneficiary data
            $beneficiary = $beneficiaryModel->getWanufaikaById($beneficiaryId);
        } else {
            $error = "Imefeli kubadilisha mwanufaika. Tafadhali jaribu tena.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Mwanufaika - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-beneficiary-form {
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

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #000;
            background-color: #f8f9fa;
        }

        .file-upload-area.dragover {
            border-color: #000;
            background-color: #f8f9fa;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .current-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            margin-bottom: 1rem;
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
                            Hariri Mwanufaika
                        </h1>
                        <p class="text-muted">Badilisha maelezo ya mwanufaika</p>
                    </div>
                    <a href="beneficiaries.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Wanufaika
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

        <!-- Edit Beneficiary Form -->
        <div class="edit-beneficiary-form">
            <form method="POST" action="" enctype="multipart/form-data" id="editBeneficiaryForm">
                <input type="hidden" name="action" value="update_beneficiary">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            Jina la Mwanufaika <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($beneficiary['name'] ?? '') ?>"
                            placeholder="Jina kamili la mwanufaika" required>
                        <div class="help-text">Jina kamili la mwanufaika</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">
                            Kichwa Cha Habari <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?= htmlspecialchars($beneficiary['title'] ?? '') ?>"
                            placeholder="Kichwa cha habari ya mafanikio" required>
                        <div class="help-text">Kichwa cha habari ya mafanikio</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="description" class="form-label">
                            Maelezo <span class="required">*</span>
                        </label>
                        <textarea class="form-control form-textarea" id="description" name="description"
                            rows="6" placeholder="Maelezo kamili ya mafanikio ya mwanufaika..." required><?= htmlspecialchars($beneficiary['description'] ?? '') ?></textarea>
                        <div class="help-text">Maelezo kamili ya jinsi mwanufaika alivyofanikiwa kupitia jukwaa la Panda Digital</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="photo" class="form-label">
                            Picha ya Mwanufaika
                        </label>

                        <!-- Current Image Display -->
                        <?php if (!empty($beneficiary['photo'])): ?>
                            <div class="mb-3">
                                <p class="text-muted mb-2">Picha ya Sasa:</p>
                                <img src="../uploads/Wanufaika/<?= htmlspecialchars($beneficiary['photo']) ?>"
                                    alt="Current Photo" class="current-image">
                            </div>
                        <?php endif; ?>

                        <div class="file-upload-area" onclick="document.getElementById('photo').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-1">Bofya au Buruta picha hapa</p>
                            <small class="text-muted">JPG, PNG, GIF (Max: 5MB)</small>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                style="display: none;" onchange="previewImage(this)">
                        </div>
                        <div id="imagePreview"></div>
                        <div class="help-text">Picha mpya ya mwanufaika (tupu kuweka picha ya sasa)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="beneficiaries.php" class="btn btn-outline-secondary">
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
        // File upload drag and drop
        const fileUploadArea = document.querySelector('.file-upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileUploadArea.classList.add('dragover');
        }

        function unhighlight(e) {
            fileUploadArea.classList.remove('dragover');
        }

        fileUploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('photo').files = files;
            previewImage(document.getElementById('photo'));
        }

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    img.alt = 'Preview';
                    preview.appendChild(img);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.getElementById('editBeneficiaryForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();

            if (!name || name.length < 2) {
                e.preventDefault();
                alert('Jina la mwanufaika lazima liwe na herufi 2 au zaidi.');
                document.getElementById('name').focus();
                return false;
            }

            if (!title || title.length < 5) {
                e.preventDefault();
                alert('Kichwa cha habari lazima kiwe na herufi 5 au zaidi.');
                document.getElementById('title').focus();
                return false;
            }

            if (!description || description.length < 20) {
                e.preventDefault();
                alert('Maelezo lazima yawe na herufi 20 au zaidi.');
                document.getElementById('description').focus();
                return false;
            }
        });
    </script>
</body>

</html>