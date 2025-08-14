<?php
require_once __DIR__ . "/../../config/init.php";
require_once __DIR__ . "/../../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="<?= app_url('admin/dashboard.php') ?>" class="sidebar-brand">
            <i class="fas fa-user-shield me-2"></i>
            <span>Admin Panel</span>
        </a>
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fas fa-user-shield fa-2x"></i>
        </div>
        <div class="user-info">
            <h6 class="mb-0"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></h6>
            <small class="text-muted">Administrator</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/dashboard.php') ?>" class="nav-link">
                    <span>Nyumbani</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'users' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/users.php') ?>" class="nav-link">
                    <span>Watumiaji</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'courses' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/courses.php') ?>" class="nav-link">
                    <span>Kozi</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'videos' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/videos.php') ?>" class="nav-link">
                    <span>Video</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'questions' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/questions.php') ?>" class="nav-link">
                    <span>Maswali</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'blogs' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/blogs.php') ?>" class="nav-link">
                    <span>Blogi</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'opportunities' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/opportunities.php') ?>" class="nav-link">
                    <span>Fursa</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'beneficiaries' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/beneficiaries.php') ?>" class="nav-link">
                    <span>Wanufaika</span>
                </a>
            </li>
            <?php if ($currentUser['email'] === 'finance@pandadigital.com'): ?>
                <li class="nav-item <?= $currentPage === 'sales' ? 'active' : '' ?>">
                    <a href="<?= app_url('admin/sales.php') ?>" class="nav-link">
                        <span>Mapato</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item <?= $currentPage === 'downloads' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/downloads.php') ?>" class="nav-link">
                    <span>Historia ya Vyeti</span>
                </a>
            </li>
            <li class="nav-item <?= $currentPage === 'feedback' ? 'active' : '' ?>">
                <a href="<?= app_url('admin/feedback.php') ?>" class="nav-link">
                    <span>Mrejesho</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= app_url('logout.php') ?>" class="nav-link text-danger">
            <span>Toka</span>
        </a>
    </div>
</div>