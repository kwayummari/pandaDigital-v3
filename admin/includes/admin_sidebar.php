<?php
require_once __DIR__ . "/../../config/init.php";
require_once __DIR__ . "/../../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$currentUser = $auth->getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="sidebar">
    <div class="sidebar-header">
        <a href="<?= app_url('admin/dashboard.php') ?>" class="sidebar-brand">
            <span>Dashboard</span>
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
            <li class="nav-item">
                <a href="<?= app_url('admin/dashboard.php') ?>" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Nyumbani</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#usersSubmenu">
                    <i class="fas fa-users"></i>
                    <span>Watumiaji</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="usersSubmenu">
                    <li><a href="<?= app_url('admin/users/add-user.php') ?>">Sajili Mtumiaji Mpya</a></li>
                    <li><a href="<?= app_url('admin/users/all-users.php') ?>">Watumiaji Wote</a></li>
                    <li><a href="<?= app_url('admin/users/user-admin.php') ?>">Utawala</a></li>
                    <li><a href="<?= app_url('admin/users/students.php') ?>">Wanafunzi</a></li>
                    <li><a href="<?= app_url('admin/users/experts.php') ?>">Walio omba kuwa wataalamu</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#coursesSubmenu">
                    <i class="fas fa-book"></i>
                    <span>Kozi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="coursesSubmenu">
                    <li><a href="<?= app_url('admin/courses/add-course.php') ?>">Sajili Kozi</a></li>
                    <li><a href="<?= app_url('admin/courses/view-courses.php') ?>">Ona Kozi</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#videosSubmenu">
                    <i class="fas fa-video"></i>
                    <span>Video</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="videosSubmenu">
                    <li><a href="<?= app_url('admin/videos/add-video.php') ?>">Ongeza Video</a></li>
                    <li><a href="<?= app_url('admin/videos/view-videos.php') ?>">Ona Video</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#questionsSubmenu">
                    <i class="fas fa-question-circle"></i>
                    <span>Maswali</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="questionsSubmenu">
                    <li><a href="<?= app_url('admin/questions/add-question.php') ?>">Ongeza Swali</a></li>
                    <li><a href="<?= app_url('admin/questions/view-questions.php') ?>">Ona Maswali</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#answersSubmenu">
                    <i class="fas fa-check-circle"></i>
                    <span>Majibu</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="answersSubmenu">
                    <li><a href="<?= app_url('admin/answers/add-answer.php') ?>">Ongeza Majibu</a></li>
                    <li><a href="<?= app_url('admin/answers/view-answers.php') ?>">Ona Majibu</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#blogSubmenu">
                    <i class="fas fa-blog"></i>
                    <span>Blogi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="blogSubmenu">
                    <li><a href="<?= app_url('admin/blog/write-blog.php') ?>">Andika Blogi</a></li>
                    <li><a href="<?= app_url('admin/blog/view-blogs.php') ?>">Ona Blogi</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#opportunitiesSubmenu">
                    <i class="fas fa-bullseye"></i>
                    <span>Fursa</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="opportunitiesSubmenu">
                    <li><a href="<?= app_url('admin/opportunities/add-opportunity.php') ?>">Andika Fursa</a></li>
                    <li><a href="<?= app_url('admin/opportunities/view-opportunities.php') ?>">Ona Fursa</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#beneficiariesSubmenu">
                    <i class="fas fa-star"></i>
                    <span>Wanufaika</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu collapse" id="beneficiariesSubmenu">
                    <li><a href="<?= app_url('admin/beneficiaries/add-beneficiary.php') ?>">Andika Wanufaika</a></li>
                    <li><a href="<?= app_url('admin/beneficiaries/view-beneficiaries.php') ?>">Ona Wanufaika</a></li>
                </ul>
            </li>

            <?php if ($currentUser['email'] === 'finance@pandadigital.com'): ?>
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-bs-toggle="collapse" data-bs-target="#financeSubmenu">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Mapato</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu collapse" id="financeSubmenu">
                        <li><a href="<?= app_url('admin/finance/sales.php') ?>">Mauzo</a></li>
                        <li><a href="<?= app_url('admin/finance/transactions.php') ?>">Miamala</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a href="<?= app_url('admin/certificates/download-history.php') ?>" class="nav-link <?= $currentPage === 'download-history' ? 'active' : '' ?>">
                    <i class="fas fa-download"></i>
                    <span>Ona Historia Upakuaji Wa Vyeti</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= app_url('admin/feedback/view-feedback.php') ?>" class="nav-link <?= $currentPage === 'view-feedback' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i>
                    <span>Ona Mrejesho</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= app_url('logout.php') ?>" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Toka</span>
        </a>
    </div>
</div>