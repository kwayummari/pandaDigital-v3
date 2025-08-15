<?php
require_once __DIR__ . "/../../config/init.php";
require_once __DIR__ . "/../../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">

    <!-- Chart.js for admin dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="<?= app_url('admin/dashboard.php') ?>" class="sidebar-brand">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span>Panda Digital</span>
                </a>
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar">
                    <i class="fas fa-user-circle fa-2x"></i>
                </div>
                <div class="user-info">
                    <h6 class="mb-0"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h6>
                    <small class="text-muted"><?php echo ucfirst($currentUser['role']); ?></small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item active">
                        <a href="<?= app_url('admin/dashboard.php') ?>" class="nav-link">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            <span>Nyumbani</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/users.php') ?>" class="nav-link">
                            <i class="fas fa-users me-2"></i>
                            <span>Watumiaji</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/courses.php') ?>" class="nav-link">
                            <i class="fas fa-book me-2"></i>
                            <span>Kozi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/blogs.php') ?>" class="nav-link">
                            <i class="fas fa-blog me-2"></i>
                            <span>Blogi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/feedback.php') ?>" class="nav-link">
                            <i class="fas fa-comments me-2"></i>
                            <span>Mrejesho</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/opportunities.php') ?>" class="nav-link">
                            <i class="fas fa-bullseye me-2"></i>
                            <span>Fursa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/sales.php') ?>" class="nav-link">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span>Mauzo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/rankings.php') ?>" class="nav-link">
                            <i class="fas fa-trophy me-2"></i>
                            <span>Mashindano</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/beneficiaries.php') ?>" class="nav-link">
                            <i class="fas fa-hand-holding-heart me-2"></i>
                            <span>Wanafaidika</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/businesses.php') ?>" class="nav-link">
                            <i class="fas fa-building me-2"></i>
                            <span>Biashara</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/downloads.php') ?>" class="nav-link">
                            <i class="fas fa-download me-2"></i>
                            <span>Pakuli</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/videos.php') ?>" class="nav-link">
                            <i class="fas fa-video me-2"></i>
                            <span>Video</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= app_url('admin/questions.php') ?>" class="nav-link">
                            <i class="fas fa-question-circle me-2"></i>
                            <span>Maswali</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= app_url('logout.php') ?>" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Toka</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation Bar -->
            <nav class="top-navbar">
                <div class="nav-left">
                    <button class="sidebar-toggle d-lg-none" id="topSidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="page-title mb-0" id="pageTitle">Dashboard</h4>
                </div>
                <div class="nav-right">
                    <div class="user-dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <?php echo htmlspecialchars($currentUser['first_name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= app_url('admin/dashboard.php') ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                            <li><a class="dropdown-item" href="<?= app_url('admin/users/') ?>">
                                    <i class="fas fa-users me-2"></i>Watumiaji
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= app_url('logout.php') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Toka
                                </a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content-wrapper">