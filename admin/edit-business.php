<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Initialize models
$businessModel = new Business();

$success = '';
$error = '';
$business = null;

// Get business ID from URL
$businessId = $_GET['id'] ?? null;
if (!$businessId || !is_numeric($businessId)) {
    header('Location: businesses.php');
    exit;
}

// Get business data
$business = $businessModel->getBusinessByIdOld($businessId);
if (!$business) {
    header('Location: businesses.php');
    exit;
}

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_business') {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $maelezo = trim($_POST['maelezo']);

    if (empty($error)) {
        if ($businessModel->updateBusinessOld($businessId, $name, $location, $maelezo)) {
            $success = "Biashara imebadilishwa kikamilifu!";
            // Refresh business data
            $business = $businessModel->getBusinessByIdOld($businessId);
        } else {
            $error = "Imefeli kubadilisha biashara. Tafadhali jaribu tena.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hariri Biashara - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .edit-business-form {
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

        .business-info {
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
                            Hariri Biashara
                        </h1>
                        <p class="text-muted">Badilisha maelezo ya biashara</p>
                    </div>
                    <a href="businesses.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Biashara
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

        <!-- Business Information Display -->
        <div class="business-info">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Maelezo ya Biashara</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> <?= htmlspecialchars($business['id']) ?></p>
                    <p><strong>Mmiliki:</strong>
                        <?php
                        $ownerName = '';
                        if (!empty($business['first_name']) && !empty($business['last_name'])) {
                            $ownerName = $business['first_name'] . ' ' . $business['last_name'];
                        } elseif (!empty($business['username'])) {
                            $ownerName = $business['username'];
                        } else {
                            $ownerName = 'N/A';
                        }
                        echo htmlspecialchars($ownerName);
                        ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tarehe ya Uundaji:</strong>
                        <?php
                        $date = $business['date_created'] ?? '';
                        echo $date ? date('d/m/Y H:i', strtotime($date)) : 'N/A';
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Business Form -->
        <div class="edit-business-form">
            <form method="POST" action="" id="editBusinessForm">
                <input type="hidden" name="action" value="update_business">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            Jina la Biashara <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($business['name'] ?? '') ?>"
                            placeholder="Jina la biashara" required>
                        <div class="help-text">Jina kamili la biashara</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">
                            Sehemu <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="location" name="location"
                            value="<?= htmlspecialchars($business['location'] ?? '') ?>"
                            placeholder="Mahali pa biashara" required>
                        <div class="help-text">Mahali pa biashara (mji, mkoa, n.k.)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="maelezo" class="form-label">
                            Maelezo
                        </label>
                        <textarea class="form-control form-textarea" id="maelezo" name="maelezo"
                            rows="6" placeholder="Maelezo ya biashara..."><?= htmlspecialchars($business['maelezo'] ?? '') ?></textarea>
                        <div class="help-text">Maelezo zaidi kuhusu biashara (si lazima)</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="businesses.php" class="btn btn-outline-secondary">
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
        document.getElementById('editBusinessForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const location = document.getElementById('location').value.trim();

            if (!name || name.length < 2) {
                e.preventDefault();
                alert('Jina la biashara lazima liwe na herufi 2 au zaidi.');
                document.getElementById('name').focus();
                return false;
            }

            if (!location || location.length < 2) {
                e.preventDefault();
                alert('Sehemu lazima iwe na herufi 2 au zaidi.');
                document.getElementById('location').focus();
                return false;
            }
        });
    </script>
</body>

</html>