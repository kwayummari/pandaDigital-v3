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

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('assets/images/logo/favicon.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('assets/images/logo/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('assets/images/logo/favicon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('assets/images/logo/favicon.png') ?>">

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
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php include __DIR__ . '/admin_topnav.php'; ?>

            <div class="content-wrapper">