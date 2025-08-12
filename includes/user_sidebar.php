<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="<?= app_url('user/dashboard.php') ?>" class="sidebar-brand">
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
            <li class="nav-item">
                <a href="<?= app_url('user/dashboard.php') ?>" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <span>Nyumbani</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/courses.php') ?>" class="nav-link <?= $current_page == 'courses.php' ? 'active' : '' ?>">
                    <span>Kozi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/quiz-results.php') ?>" class="nav-link <?= $current_page == 'quiz-results.php' ? 'active' : '' ?>">
                    <span>Majibu Yangu</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/certificates.php') ?>" class="nav-link <?= $current_page == 'certificates.php' ? 'active' : '' ?>">
                    <span>Vyeti Vyagu</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/business.php') ?>" class="nav-link <?= $current_page == 'business.php' ? 'active' : '' ?>">
                    <span>Biashara</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/ask-questions.php') ?>" class="nav-link <?= $current_page == 'ask-questions.php' ? 'active' : '' ?>">
                    <span>Uliza Maswali</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/certificate-history.php') ?>" class="nav-link <?= $current_page == 'certificate-history.php' ? 'active' : '' ?>">
                    <span>Historia ya Vyeti</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/skill-level.php') ?>" class="nav-link <?= $current_page == 'skill-level.php' ? 'active' : '' ?>">
                    <span>Daraja la Uwezo</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= app_url('user/feedback.php') ?>" class="nav-link <?= $current_page == 'feedback.php' ? 'active' : '' ?>">
                    <span>Toa Mrejesho</span>
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