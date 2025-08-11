<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/ExpertQuestion.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$userModel = new User();
$courseModel = new Course();
$expertQuestionModel = new ExpertQuestion();

// Get platform statistics
$totalUsers = $userModel->getTotalUsers();
$totalCourses = $courseModel->getTotalCourses();
$totalExpertQuestions = $expertQuestionModel->getTotalQuestions();
$pendingExpertRequests = $userModel->getPendingExpertRequests();

// Get user statistics by role
$userStats = $userModel->getUserStatsByRole();
$recentRegistrations = $userModel->getRecentRegistrations(5);

// Get today's date for greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Asubuhi njema";
} elseif ($hour < 17) {
    $greeting = "Mchana njema";
} else {
    $greeting = "Jioni njema";
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ya Admin - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #662e91;
            --secondary-color: #FFC10B;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d68910);
        }

        .stats-card.primary {
            background: linear-gradient(135deg, var(--primary-color), #8e44ad);
        }

        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-5px);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .user-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: white;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .user-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                Panda Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link" href="/admin/expert-requests.php">
                    <i class="fas fa-user-graduate me-1"></i> Maombi ya Mitaalam
                </a>
                <a class="nav-link" href="/admin/users.php">
                    <i class="fas fa-users me-1"></i> Watumiaji
                </a>
                <a class="nav-link" href="/admin/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="admin-badge d-inline-block mb-3">
                    <i class="fas fa-crown me-2"></i>
                    Msimamizi Mkuu
                </div>
                <h1 class="mb-3">
                    <i class="fas fa-sun me-2"></i>
                    <?php echo $greeting; ?>, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                </h1>
                <p class="lead mb-0">
                    Tazama muhtasari wa mfumo na usimamizi wa watumiaji
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h4 class="mb-4">
                    <i class="fas fa-bolt text-primary me-2"></i>
                    Vitendo vya Haraka
                </h4>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="/admin/users.php" class="action-btn">
                            <i class="fas fa-users"></i>
                            <span class="fw-bold">Usimamizi wa Watumiaji</span>
                            <small class="text-muted">Sajili na udhibiti watumiaji</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/courses.php" class="action-btn">
                            <i class="fas fa-book"></i>
                            <span class="fw-bold">Usimamizi wa Kozi</span>
                            <small class="text-muted">Ongeza na udhibiti kozi</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/expert-requests.php" class="action-btn">
                            <i class="fas fa-user-graduate"></i>
                            <span class="fw-bold">Maombi ya Mitaalam</span>
                            <small class="text-muted">Idhinisha au kataa maombi</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/reports.php" class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span class="fw-bold">Ripoti na Takwimu</span>
                            <small class="text-muted">Tazama ripoti za mfumo</small>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalUsers; ?></h3>
                            <p class="mb-0">Jumla ya Watumiaji</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalCourses; ?></h3>
                            <p class="mb-0">Jumla ya Kozi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-question-circle fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo $totalExpertQuestions; ?></h3>
                            <p class="mb-0">Maswali ya Mitaalam</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo count($pendingExpertRequests); ?></h3>
                            <p class="mb-0">Maombi Yaliyosubiri</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics by Role -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>
                                Watumiaji kwa Majukumu
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($userStats as $role => $count): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-primary me-3 fa-lg"></i>
                                        <span class="fw-bold">
                                            <?php
                                            switch ($role) {
                                                case 'user':
                                                    echo 'Wanafunzi';
                                                    break;
                                                case 'expert':
                                                    echo 'Mitaalam';
                                                    break;
                                                case 'admin':
                                                    echo 'Wasimamizi';
                                                    break;
                                                default:
                                                    echo ucfirst($role);
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold fs-4"><?php echo $count; ?></div>
                                        <small class="text-muted">
                                            <?php echo round(($count / $totalUsers) * 100); ?>%
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy text-warning me-2"></i>
                                Muhtasari wa Mfumo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="text-success fw-bold fs-4">
                                        <?php echo $userStats['user'] ?? 0; ?>
                                    </div>
                                    <small class="text-muted">Wanafunzi</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-primary fw-bold fs-4">
                                        <?php echo $userStats['expert'] ?? 0; ?>
                                    </div>
                                    <small class="text-muted">Mitaalam</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-info fw-bold fs-4">
                                        <?php echo $totalCourses; ?>
                                    </div>
                                    <small class="text-muted">Kozi</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-warning fw-bold fs-4">
                                        <?php echo count($pendingExpertRequests); ?>
                                    </div>
                                    <small class="text-muted">Maombi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and System Overview -->
            <div class="row">
                <!-- Recent User Registrations -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus text-success me-2"></i>
                                Watumiaji Waliosajiliwa Hivi Karibuni
                            </h5>
                            <a href="/admin/users.php" class="btn btn-primary-custom text-white btn-sm">
                                <i class="fas fa-eye me-2"></i>
                                Tazama Wote
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentRegistrations)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                    <h6>Hakuna watumiaji waliosajiliwa hivi karibuni</h6>
                                    <p class="text-muted">Watumiaji wote waliosajiliwa bado.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentRegistrations as $user): ?>
                                    <div class="user-item">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-user-circle text-primary fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">
                                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                                    </h6>
                                                    <p class="mb-1 text-muted small">
                                                        <?php echo htmlspecialchars($user['email']); ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d M Y H:i', strtotime($user['date_created'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?php
                                                                        switch ($user['role']) {
                                                                            case 'admin':
                                                                                echo 'danger';
                                                                                break;
                                                                            case 'expert':
                                                                                echo 'warning';
                                                                                break;
                                                                            default:
                                                                                echo 'success';
                                                                        }
                                                                        ?>">
                                                    <?php
                                                    switch ($user['role']) {
                                                        case 'admin':
                                                            echo 'Msimamizi';
                                                            break;
                                                        case 'expert':
                                                            echo 'Mtaalam';
                                                            break;
                                                        default:
                                                            echo 'Mwanafunzi';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- System Overview -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs text-primary me-2"></i>
                                Muhtasari wa Mfumo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Watumiaji Walioidhinishwa</small>
                                    <small class="text-muted">
                                        <?php
                                        $activeUsers = ($userStats['user'] ?? 0) + ($userStats['expert'] ?? 0) + ($userStats['admin'] ?? 0);
                                        echo $activeUsers;
                                        ?>
                                    </small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success"
                                        style="width: <?php echo ($activeUsers / $totalUsers) * 100; ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Kozi Zilizopakiwa</small>
                                    <small class="text-muted"><?php echo $totalCourses; ?></small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Maombi ya Mitaalam</small>
                                    <small class="text-muted"><?php echo count($pendingExpertRequests); ?></small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning"
                                        style="width: <?php echo count($pendingExpertRequests) > 0 ? 100 : 0; ?>%"></div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <a href="/admin/reports.php" class="btn btn-primary-custom text-white">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Tazama Ripoti Kamili
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health and Alerts -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5 class="mb-3">
                        <i class="fas fa-heartbeat text-success me-2"></i>
                        Hali ya Mfumo
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-server text-success fa-2x mb-3"></i>
                                <h6>Mfumo Unafanya Kazi</h6>
                                <p class="text-muted small">Hali ya mfumo ni nzuri na unafanya kazi kwa kawaida.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-database text-info fa-2x mb-3"></i>
                                <h6>Database Inafanya Kazi</h6>
                                <p class="text-muted small">Database inafanya kazi vizuri na data zote zipo salama.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-shield-alt text-warning fa-2x mb-3"></i>
                                <h6>Usalama Unafanya Kazi</h6>
                                <p class="text-muted small">Mifumo ya usalama inafanya kazi na kuhifadhi watumiaji.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <i class="fas fa-sync text-primary fa-2x mb-3"></i>
                                <h6>Backup Inafanya Kazi</h6>
                                <p class="text-muted small">Mifumo ya backup inafanya kazi na kuhifadhi data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>