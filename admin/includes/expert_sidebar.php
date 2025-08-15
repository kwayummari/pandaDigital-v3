<?php
// This file contains the reusable sidebar for expert pages
// Make sure $currentUser is available when including this file
?>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="<?= app_url('admin/expert-dashboard.php') ?>" class="sidebar-brand">
            <i class="fas fa-user-graduate me-2"></i>
            <span>Expert Panel</span>
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
            <small class="text-muted">Mtaalam</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-dashboard.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-dashboard.php') ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Nyumbani</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-questions.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-questions.php') ?>" class="nav-link">
                    <i class="fas fa-question-circle me-2"></i>
                    <span>Maswali</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-view-questions.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-view-questions.php') ?>" class="nav-link">
                    <i class="fas fa-eye me-2"></i>
                    <span>Ona Maswali</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-business.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-business.php') ?>" class="nav-link">
                    <i class="fas fa-building me-2"></i>
                    <span>Biashara Zangu</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-answered.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-answered.php') ?>" class="nav-link">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>Waliojibiwa</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-profile.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-profile.php') ?>" class="nav-link">
                    <i class="fas fa-user-edit me-2"></i>
                    <span>Profaili</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'expert-earnings.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/expert-earnings.php') ?>" class="nav-link">
                    <i class="fas fa-chart-line me-2"></i>
                    <span>Mapato</span>
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
