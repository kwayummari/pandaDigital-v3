<?php
// This file contains the reusable sidebar for admin pages
// Make sure $currentUser is available when including this file
?>
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
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/dashboard.php') ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Nyumbani</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/users.php') ?>" class="nav-link">
                    <i class="fas fa-users me-2"></i>
                    <span>Watumiaji</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/courses.php') ?>" class="nav-link">
                    <i class="fas fa-book me-2"></i>
                    <span>Kozi</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'blogs.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/blogs.php') ?>" class="nav-link">
                    <i class="fas fa-blog me-2"></i>
                    <span>Blogi</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/feedback.php') ?>" class="nav-link">
                    <i class="fas fa-comments me-2"></i>
                    <span>Mrejesho</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'opportunities.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/opportunities.php') ?>" class="nav-link">
                    <i class="fas fa-bullseye me-2"></i>
                    <span>Fursa</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/sales.php') ?>" class="nav-link">
                    <i class="fas fa-shopping-cart me-2"></i>
                    <span>Mauzo</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'rankings.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/rankings.php') ?>" class="nav-link">
                    <i class="fas fa-trophy me-2"></i>
                    <span>Mashindano</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'beneficiaries.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/beneficiaries.php') ?>" class="nav-link">
                    <i class="fas fa-hand-holding-heart me-2"></i>
                    <span>Wanafaidika</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'businesses.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/businesses.php') ?>" class="nav-link">
                    <i class="fas fa-building me-2"></i>
                    <span>Biashara</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'downloads.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/downloads.php') ?>" class="nav-link">
                    <i class="fas fa-download me-2"></i>
                    <span>Pakuli</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'videos.php' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/videos.php') ?>" class="nav-link">
                    <i class="fas fa-video me-2"></i>
                    <span>Video</span>
                </a>
            </li>
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'questions.php' ? 'active' : '' ?>">
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