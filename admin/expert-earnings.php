<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Expert.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Check if expert is authorized
if (!isset($currentUser['expert_authorization']) || $currentUser['expert_authorization'] != 1) {
    header('Location: ' . app_url('expert/pending-authorization.php'));
    exit();
}

$expertModel = new Expert();

// Get earnings data for this expert
$totalEarnings = $expertModel->getTotalEarnings($currentUser['id']);
$monthlyEarnings = $expertModel->getMonthlyEarnings($currentUser['id']);
$earningsHistory = $expertModel->getEarningsHistory($currentUser['id']);

// Set page title
$page_title = 'Mapato Yangu';
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
                            <h1 class="page-title">Mapato Yangu</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/expert-dashboard.php') ?>">Expert Dashboard</a></li>
                                    <li class="breadcrumb-item active">Mapato</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <div class="page-actions">
                                <button type="button" class="btn btn-success" onclick="requestWithdrawal()">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Omba Malipo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid">
                <!-- Earnings Overview Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number">TSh <?= number_format($totalEarnings, 0) ?></h3>
                                        <p class="stat-label">Mapato Yote</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number">TSh <?= number_format($monthlyEarnings, 0) ?></h3>
                                        <p class="stat-label">Mapato ya Mwezi</p>
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
                                        <h3 class="stat-number">TSh 0</h3>
                                        <p class="stat-label">Yanayosubiriwa</p>
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
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number">0</h3>
                                        <p class="stat-label">Malipo Yaliyotolewa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings Chart and Details -->
                <div class="row">
                    <!-- Earnings Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Mwelekeo wa Mapato
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Grafu ya Mapato</h5>
                                    <p class="text-muted">Hapa itaonekana grafu ya mapato yako kwa muda.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Maelezo ya Mapato
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="earnings-info">
                                    <div class="info-item mb-3">
                                        <h6 class="text-muted">Jinsi Mapato Yanavyopatikana</h6>
                                        <p class="small">Mapato yanapatikana kwa kujibu maswali ya wanafunzi na kutoa ushauri wa kitaaluma.</p>
                                    </div>
                                    <div class="info-item mb-3">
                                        <h6 class="text-muted">Kiwango cha Malipo</h6>
                                        <p class="small">TSh 1,000 kwa kila swali linalojibiwa na kuthibitishwa.</p>
                                    </div>
                                    <div class="info-item mb-3">
                                        <h6 class="text-muted">Muda wa Malipo</h6>
                                        <p class="small">Malipo yanafanywa mwishoni mwa kila mwezi au kwa ombi lao.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings History -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Historia ya Mapato
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($earningsHistory)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tarehe</th>
                                                    <th>Swali</th>
                                                    <th>Mwanafunzi</th>
                                                    <th>Kiasi</th>
                                                    <th>Hali</th>
                                                    <th>Vitendo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($earningsHistory as $earning): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($earning['date'])) ?></td>
                                                    <td>
                                                        <div class="question-preview">
                                                            <?= htmlspecialchars(substr($earning['question'], 0, 50)) ?>...
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($earning['student_name']) ?></td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            TSh <?= number_format($earning['amount'], 0) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($earning['status'] == 'paid'): ?>
                                                            <span class="badge bg-success">Imelipwa</span>
                                                        <?php elseif ($earning['status'] == 'pending'): ?>
                                                            <span class="badge bg-warning">Inasubiriwa</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Haijulikani</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= app_url('admin/expert-view-question.php?id=' . $earning['question_id']) ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-coins fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">Hakuna Historia ya Mapato</h4>
                                        <p class="text-muted">Bado hujapata mapato yoyote. Anza kwa kujibu maswali ya wanafunzi.</p>
                                        <a href="<?= app_url('admin/expert-questions.php') ?>" class="btn btn-primary">
                                            <i class="fas fa-question-circle me-2"></i>
                                            Tazama Maswali
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
        function requestWithdrawal() {
            if (confirm('Je, una uhakika unahitaji kuomba malipo? Malipo yatatumwa kwenye namba yako ya simu.')) {
                // Redirect to withdrawal request page or make AJAX call
                alert('Ombi lako la malipo limetumwa. Tutakupigia simu kuhusu malipo yako.');
            }
        }
    </script>
</body>
</html>
