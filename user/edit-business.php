<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

// Ensure user is logged in
$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$businessId = $_GET['business_id'] ?? null;

if (!$businessId) {
    header('Location: ' . app_url('user/business.php'));
    exit;
}

// Initialize business model
$businessModel = new Business();

// Get business details
$userBusinesses = $businessModel->getBusinessesByUserId($currentUser['id']);
$selectedBusiness = null;

foreach ($userBusinesses as $business) {
    if ($business['id'] == $businessId) {
        $selectedBusiness = $business;
        break;
    }
}

if (!$selectedBusiness) {
    header('Location: ' . app_url('user/business.php'));
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = trim($_POST['business_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $website = trim($_POST['website'] ?? '');

    // Validation
    if (empty($businessName)) {
        $message = 'Jina la biashara ni lazima';
        $messageType = 'danger';
    } elseif (empty($description)) {
        $message = 'Maelezo ya biashara ni lazima';
        $messageType = 'danger';
    } elseif (empty($location)) {
        $message = 'Mahali pa biashara ni lazima';
        $messageType = 'danger';
    } else {
        if ($businessModel->updateBusinessOldSystem($businessId, $businessName, $description, $location, $phone, $email, $website)) {
            $message = 'Biashara yako imesasishwa kwa mafanikio!';
            $messageType = 'success';

            // Refresh business data
            $selectedBusiness = $businessModel->getBusinessById($businessId);
        } else {
            $message = 'Kuna tatizo la kiufundi. Jaribu tena.';
            $messageType = 'danger';
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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: var(--gray-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--gray-dark);
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/user_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Hariri Biashara</h1>
                            <p class="text-muted">Sasisha taarifa za biashara yako</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= app_url('user/business.php') ?>">Biashara</a></li>
                                <li class="breadcrumb-item active">Hariri</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Edit Business Form -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body p-4">
                                <form method="POST" action="">
                                    <div class="row">
                                        <!-- Business Name -->
                                        <div class="col-md-12 mb-3">
                                            <label for="business_name" class="form-label">Jina la Biashara *</label>
                                            <input type="text" class="form-control" id="business_name" name="business_name"
                                                value="<?php echo htmlspecialchars($selectedBusiness['name'] ?? ''); ?>"
                                                placeholder="Jina la biashara yako" required>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-md-12 mb-3">
                                            <label for="description" class="form-label">Maelezo ya Biashara *</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"
                                                placeholder="Eleza biashara yako, bidhaa au huduma unazotoa..." required><?php echo htmlspecialchars($selectedBusiness['maelezo'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Location -->
                                        <div class="col-md-12 mb-3">
                                            <label for="location" class="form-label">Mahali pa Biashara *</label>
                                            <input type="text" class="form-control" id="location" name="location"
                                                value="<?php echo htmlspecialchars($selectedBusiness['location'] ?? ''); ?>"
                                                placeholder="Mtaa, jiji au mkoa" required>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Namba ya Simu</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="<?php echo htmlspecialchars($selectedBusiness['phone'] ?? ''); ?>"
                                                placeholder="+255 7XX XXX XXX">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Barua Pepe</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?php echo htmlspecialchars($selectedBusiness['email'] ?? ''); ?>"
                                                placeholder="email@example.com">
                                        </div>

                                        <!-- Website -->
                                        <div class="col-md-12 mb-4">
                                            <label for="website" class="form-label">Tovuti (Ikiwepo)</label>
                                            <input type="url" class="form-control" id="website" name="website"
                                                value="<?php echo htmlspecialchars($selectedBusiness['website'] ?? ''); ?>"
                                                placeholder="https://www.example.com">
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="col-12">
                                            <div class="d-flex gap-3 justify-content-end">
                                                <a href="<?= app_url('user/business.php?business_id=' . $businessId) ?>" class="btn btn-secondary">
                                                    Ghairi
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    Sasisha Biashara
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Business Status Info -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <h6 class="card-title">Hali ya Biashara</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Hali:</strong>
                                            <span class="badge bg-<?php echo ($selectedBusiness['status'] ?? '') == 'approved' ? 'success' : 'warning'; ?>">
                                                <?php echo ($selectedBusiness['status'] ?? '') == 'approved' ? 'Imekubaliwa' : 'Inasubiri'; ?>
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Tarehe ya Usajili:</strong>
                                            <?php echo date('d/m/Y', strtotime($selectedBusiness['date_created'] ?? 'now')); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>ID ya Biashara:</strong> <?php echo $businessId; ?></p>
                                        <p class="mb-0"><strong>Mmiliki:</strong> <?php echo htmlspecialchars($currentUser['name'] ?? ''); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>