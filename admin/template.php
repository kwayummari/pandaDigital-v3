<?php
// Template for creating new admin pages with the reusable structure
// Copy this file and modify it for your specific admin page

require_once __DIR__ . "/../middleware/AuthMiddleware.php";
// Add other required models here

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
// Initialize your models here
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php include __DIR__ . '/includes/admin_topnav.php'; ?>

            <div class="content-wrapper">
                <!-- Your page content goes here -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-icon-name text-primary me-2"></i>
                            Your Page Title
                        </h1>
                        <p class="text-muted">Your page description</p>
                    </div>
                </div>

                <!-- Your content here -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Content</h5>
                        <p class="card-text">This is where your page content goes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Your page-specific JavaScript goes here

        // Update page title in the top navigation
        document.addEventListener('DOMContentLoaded', function() {
            const pageTitle = document.getElementById('pageTitle');
            if (pageTitle) {
                pageTitle.textContent = 'Your Page Title';
            }
        });
    </script>

    <?php include __DIR__ . '/includes/admin_footer_common.php'; ?>