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
    <title><?= htmlspecialchars($appConfig['name']) ?> - Admin Panel</title>
    <meta name="description" content="Panda Digital Admin Panel - Manage platform content and users">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo/favicon.png') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Chart.js for admin dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!-- Sidebar Toggle -->
            <button class="btn btn-link sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand -->
            <a class="navbar-brand" href="<?= app_url('admin/dashboard.php') ?>">
                <img src="<?= asset('images/logo/favicon.png') ?>" alt="Panda Digital" class="brand-logo me-2">
            </a>

            <!-- Right Side Navigation -->
            <div class="navbar-nav ms-auto">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Arifa</h6>
                        </li>
                        <li><a class="dropdown-item" href="#">Mtumiaji mpya amejisajili</a></li>
                        <li><a class="dropdown-item" href="#">Ombi la mtaalam limewasili</a></li>
                        <li><a class="dropdown-item" href="#">Mrejesho mpya umewasili</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Tazama zote</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="d-none d-md-block"><?= htmlspecialchars($currentUser['first_name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Akaunti</h6>
                        </li>
                        <li><a class="dropdown-item" href="<?= app_url('admin/profile.php') ?>">
                                <i class="fas fa-user me-2"></i>Wasifu
                            </a></li>
                        <li><a class="dropdown-item" href="<?= app_url('admin/settings.php') ?>">
                                <i class="fas fa-cog me-2"></i>Mipangilio
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= app_url('logout.php') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Toka
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title" id="pageTitle">Dashboard</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= app_url('admin/dashboard.php') ?>">Admin</a></li>
                                    <li class="breadcrumb-item active" aria-current="page" id="breadcrumbCurrent">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                <div class="container-fluid">