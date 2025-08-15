<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Business.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Check if expert is authorized
if (!isset($currentUser['expert_authorization']) || $currentUser['expert_authorization'] != 1) {
    header('Location: ' . app_url('expert/pending-authorization.php'));
    exit();
}

$businessModel = new Business();

// Get businesses owned by this expert
$expertBusinesses = $businessModel->getBusinessesByOwner($currentUser['id']);

// Set page title
$page_title = 'Biashara Zangu';
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
                            <h1 class="page-title">Biashara Zangu</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Biashara Zangu</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <a href="<?= app_url('user/add-business.php') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Ongeza Biashara
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= count($expertBusinesses) ?></h3>
                                        <p class="stat-label">Biashara Zote</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= count(array_filter($expertBusinesses, fn($b) => $b['verification_status'] == 'verified')) ?></h3>
                                        <p class="stat-label">Zilizothibitishwa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= count(array_filter($expertBusinesses, fn($b) => $b['verification_status'] == 'pending')) ?></h3>
                                        <p class="stat-label">Zinasubiriwa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number"><?= array_sum(array_column($expertBusinesses, 'views')) ?></h3>
                                        <p class="stat-label">Jumla ya Matazamo</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Businesses List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Orodha ya Biashara</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($expertBusinesses)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Picha</th>
                                                    <th>Jina la Biashara</th>
                                                    <th>Aina</th>
                                                    <th>Mkoa</th>
                                                    <th>Hali</th>
                                                    <th>Matazamo</th>
                                                    <th>Tarehe</th>
                                                    <th>Vitendo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($expertBusinesses as $business): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($business['image'])): ?>
                                                            <img src="<?= asset('uploads/businesses/' . $business['image']) ?>" 
                                                                 alt="<?= htmlspecialchars($business['name']) ?>" 
                                                                 class="business-thumbnail" 
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                        <?php else: ?>
                                                            <div class="business-thumbnail-placeholder" 
                                                                 style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-building text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="business-name">
                                                            <strong><?= htmlspecialchars($business['name']) ?></strong>
                                                            <?php if (!empty($business['description'])): ?>
                                                                <br><small class="text-muted"><?= htmlspecialchars(substr($business['description'], 0, 50)) ?>...</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?= htmlspecialchars($business['category'] ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($business['region'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($business['verification_status'] == 'verified'): ?>
                                                            <span class="badge bg-success">Imethibitishwa</span>
                                                        <?php elseif ($business['verification_status'] == 'pending'): ?>
                                                            <span class="badge bg-warning">Inasubiriwa</span>
                                                        <?php elseif ($business['verification_status'] == 'rejected'): ?>
                                                            <span class="badge bg-danger">Imekataliwa</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Haijulikani</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-eye me-1"></i>
                                                            <?= number_format($business['views'] ?? 0) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d/m/Y', strtotime($business['date_created'])) ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= app_url('user/view-business.php?id=' . $business['id']) ?>" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Tazama">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="<?= app_url('user/edit-business.php?id=' . $business['id']) ?>" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="Badilisha">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Futa"
                                                                    onclick="deleteBusiness(<?= $business['id'] ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-building fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">Huna Biashara Yoyote</h4>
                                        <p class="text-muted">Anza kwa kuongeza biashara yako ya kwanza.</p>
                                        <a href="<?= app_url('user/add-business.php') ?>" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Ongeza Biashara
                                        </a>
                                    </div>
                                <?php endif; ?>
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
        function deleteBusiness(businessId) {
            if (confirm('Je, una uhakika unahitaji kufuta biashara hii? Kitendo hiki hakiwezi kubatilishwa.')) {
                // Redirect to delete page or make AJAX call
                window.location.href = '<?= app_url("user/delete-business.php?id=") ?>' + businessId;
            }
        }
    </script>
</body>
</html>
