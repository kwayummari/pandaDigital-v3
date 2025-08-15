<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Check if expert is authorized
if (!isset($currentUser['expert_authorization']) || $currentUser['expert_authorization'] != 1) {
    header('Location: ' . app_url('expert/pending-authorization.php'));
    exit();
}

// Set page title
$page_title = 'Profaili Yangu';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - <?= htmlspecialchars($appConfig['name']) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=8">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/includes/expert_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/includes/admin_top_nav.php'; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title">Profaili Yangu</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Profaili</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <button type="button" class="btn btn-primary" onclick="editProfile()">
                                    <i class="fas fa-edit me-2"></i>
                                    Badilisha Profaili
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <div class="row">
                    <!-- Profile Information -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    Maelezo ya Msingi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Jina la Kwanza</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['first_name'] ?? 'Haijulikani') ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Jina la Mwisho</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['last_name'] ?? 'Haijulikani') ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Barua Pepe</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['email']) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Namba ya Simu</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['phone'] ?? 'Haijulikani') ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Mkoa</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['region'] ?? 'Haijulikani') ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Jukumu</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-warning">Mtaalam</span>
                                        </p>
                                    </div>
                                </div>

                                <?php if (!empty($currentUser['bio'])): ?>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted">Wasifu</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['bio']) ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Tarehe ya Kujisajili</label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($currentUser['date_created'])) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Hali ya Uthibitisho</label>
                                        <p class="form-control-plaintext">
                                            <?php if ($currentUser['expert_authorization'] == 1): ?>
                                                <span class="badge bg-success">Imethibitishwa</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Inasubiriwa</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Stats -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Takwimu
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="stat-item">
                                            <h3 class="text-primary">0</h3>
                                            <small class="text-muted">Maswali Yaliyojibiwa</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-item">
                                            <h3 class="text-success">0</h3>
                                            <small class="text-muted">Biashara</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-item">
                                            <h3 class="text-info">0</h3>
                                            <small class="text-muted">Matazamo</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="stat-item">
                                            <h3 class="text-warning">TSh 0</h3>
                                            <small class="text-muted">Mapato</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>
                                    Vitendo vya Haraka
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= app_url('admin/expert-questions.php') ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-question-circle me-2"></i>
                                        Tazama Maswali
                                    </a>
                                    <a href="<?= app_url('user/add-business.php') ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-plus me-2"></i>
                                        Ongeza Biashara
                                    </a>
                                    <a href="<?= app_url('admin/expert-earnings.php') ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Tazama Mapato
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="<?= asset('js/script.js') ?>?v=<?= time() ?>"></script>
    
    <script>
        function editProfile() {
            // Redirect to profile edit page
            window.location.href = '<?= app_url("user/edit-profile.php") ?>';
        }
    </script>
</body>
</html>
