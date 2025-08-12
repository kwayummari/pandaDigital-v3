<!-- Top Navigation Bar -->
<nav class="top-navbar">
    <div class="nav-left">
        <button class="sidebar-toggle d-lg-none" id="topSidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="page-title mb-0"><?php echo $page_title ?? 'Dashboard'; ?></h4>
    </div>
    <div class="nav-right">
        <div class="user-dropdown">
            <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-2"></i>
                <?php echo htmlspecialchars($currentUser['first_name']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= app_url('user/dashboard.php') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a></li>
                <li><a class="dropdown-item" href="<?= app_url('user/courses.php') ?>">
                        <i class="fas fa-book me-2"></i>Kozi
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