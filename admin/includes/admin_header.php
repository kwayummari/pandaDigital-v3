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
    <!-- Fallback CSS if asset helper fails -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>">

    <!-- Inline CSS for critical admin styles -->
    <style>
        /* Critical admin styles in case external CSS fails */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e2e8f0;
        }

        .main-content {
            flex: 1;
            background: #f8f9fa;
            margin-top: 70px;
        }

        .page-header {
            background: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .page-content {
            padding: 30px 20px 20px 20px;
        }

        .stats-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #662e91;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            position: relative;
            min-height: 120px;
        }

        .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            background: rgba(102, 46, 145, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #662e91;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar submenu styles */
        .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background: #f8f9fa;
        }

        .submenu li a {
            display: block;
            padding: 10px 20px 10px 52px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .submenu li a:hover {
            background: rgba(102, 46, 145, 0.05);
            color: #662e91;
            border-left-color: #662e91;
        }

        /* Logout button styles */
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            color: white;
            transform: translateY(-1px);
        }

        .logout-btn i {
            margin-right: 8px;
        }

        /* Sidebar navigation styles */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #FFC10B;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(102, 46, 145, 0.1);
            color: #662e91;
            border-left-color: #662e91;
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 0.9rem;
        }

        .nav-link.has-submenu {
            justify-content: space-between;
        }

        .submenu-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .nav-link[aria-expanded="true"] .submenu-arrow {
            transform: rotate(180deg);
        }

        /* Ensure submenu collapse works */
        .submenu.collapse:not(.show) {
            display: none;
        }

        .submenu.collapsing {
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        .submenu.collapse.show {
            display: block;
        }

        /* Sidebar user section */
        .sidebar-user {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8f9fa;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #662e91;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .user-info h6 {
            margin: 0 0 4px 0;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-info small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            background: #f8f9fa;
        }

        /* Nav list styling */
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 5px;
        }
    </style>

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
                <img src="<?= asset('images/logo/favicon.png') ?>" alt="Panda Digital" class="brand-logo">
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