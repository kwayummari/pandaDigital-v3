<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Mock data for business activities - in real app this would come from database
$businessActivities = [
    [
        'id' => 1,
        'type' => 'product_sale',
        'title' => 'Uuzaji wa Bidhaa',
        'description' => 'Umeuza bidhaa 5 za Digital Marketing',
        'amount' => 25000,
        'date' => '2024-03-15',
        'status' => 'completed'
    ],
    [
        'id' => 2,
        'type' => 'service_provision',
        'title' => 'Utumishi wa Social Media',
        'description' => 'Umetoa huduma ya Social Media Management',
        'amount' => 15000,
        'date' => '2024-03-10',
        'status' => 'completed'
    ],
    [
        'id' => 3,
        'type' => 'consultation',
        'title' => 'Mashauriano ya Biashara',
        'description' => 'Umetoa mashauriano kwa mteja mpya',
        'amount' => 5000,
        'date' => '2024-03-08',
        'status' => 'pending'
    ]
];

$totalEarnings = array_sum(array_column($businessActivities, 'amount'));
$completedActivities = count(array_filter($businessActivities, function ($a) {
    return $a['status'] == 'completed';
}));
$pendingActivities = count(array_filter($businessActivities, function ($a) {
    return $a['status'] == 'pending';
}));
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biashara - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
    <style>
        .business-card {
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
            margin-bottom: 20px;
        }

        .business-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 188, 59, 0.2);
        }

        .business-header {
            background: var(--primary-color);
            color: black;
            border-radius: 13px 13px 0 0;
            padding: 20px;
        }

        .business-body {
            padding: 20px;
        }

        .amount-display {
            background: var(--secondary-color);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .activity-item {
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
        }

        .activity-item.completed {
            border-left-color: var(--primary-color);
        }

        .activity-item.pending {
            border-left-color: var(--secondary-color);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-completed {
            background: var(--primary-color);
            color: black;
        }

        .status-pending {
            background: var(--secondary-color);
            color: white;
        }

        .action-btn {
            background: var(--primary-color);
            color: black;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .action-btn:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Biashara';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-briefcase me-2" style="color: var(--primary-color);"></i>
                            Biashara
                        </h1>
                        <p class="text-muted">Dhibiti na uone maendeleo ya biashara yako</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <h3 class="mb-1">TSh <?php echo number_format($totalEarnings); ?></h3>
                                <p class="mb-0">Jumla ya Mapato</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $completedActivities; ?></h3>
                                <p class="mb-0">Shughuli Zilizokamilika</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-1"><?php echo $pendingActivities; ?></h3>
                                <p class="mb-0">Shughuli Zinasubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Overview -->
                <div class="business-card">
                    <div class="business-header">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Muhtasari wa Biashara
                        </h4>
                    </div>
                    <div class="business-body">
                        <div class="amount-display">
                            <h2 class="mb-1">TSh <?php echo number_format($totalEarnings); ?></h2>
                            <p class="mb-0">Mapato ya Jumla</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Shughuli za Hivi Karibuni</h6>
                                <p class="text-muted">Tazama shughuli zako za hivi karibuni na uone maendeleo</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="#" class="action-btn">
                                    <i class="fas fa-plus me-2"></i>
                                    Ongeza Shughuli
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="business-card">
                    <div class="business-header">
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Shughuli za Hivi Karibuni
                        </h4>
                    </div>
                    <div class="business-body">
                        <?php if (empty($businessActivities)): ?>
                            <div class="empty-state">
                                <i class="fas fa-briefcase"></i>
                                <h5>Huna shughuli za biashara bado</h5>
                                <p class="text-muted">Anza kufanya shughuli za biashara ili uone maendeleo yako</p>
                                <a href="#" class="action-btn">
                                    <i class="fas fa-plus me-2"></i>
                                    Anza Shughuli
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($businessActivities as $activity): ?>
                                <div class="activity-item <?php echo $activity['status']; ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                            <p class="mb-2 text-muted"><?php echo htmlspecialchars($activity['description']); ?></p>
                                            <div class="d-flex align-items-center">
                                                <span class="me-3">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($activity['date'])); ?>
                                                </span>
                                                <span class="me-3">
                                                    <i class="fas fa-money-bill me-1"></i>
                                                    TSh <?php echo number_format($activity['amount']); ?>
                                                </span>
                                                <span class="status-badge status-<?php echo $activity['status']; ?>">
                                                    <?php
                                                    if ($activity['status'] == 'completed') {
                                                        echo 'Imekamilika';
                                                    } else {
                                                        echo 'Inasubiri';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <a href="#" class="action-btn btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="business-card">
                    <div class="business-header">
                        <h4 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Vitendo vya Haraka
                        </h4>
                    </div>
                    <div class="business-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="#" class="action-btn w-100 text-center">
                                    <i class="fas fa-plus me-2"></i>
                                    Ongeza Shughuli
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="action-btn w-100 text-center">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Tazama Ripoti
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="action-btn w-100 text-center">
                                    <i class="fas fa-users me-2"></i>
                                    Wadau
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="action-btn w-100 text-center">
                                    <i class="fas fa-cog me-2"></i>
                                    Mipangilio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->

</body>

</html>