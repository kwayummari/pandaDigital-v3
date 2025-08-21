<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/GoogleOAuthService.php';

// Initialize authentication service
$authService = new AuthService();
$isLoggedIn = $authService->isLoggedIn();
$currentUser = $isLoggedIn ? $authService->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appConfig['name']) ?> - Empowering Women in Digital Economy</title>
    <meta name="description" content="Panda Digital - Platform for women entrepreneurs, digital skills, and business opportunities in Tanzania">

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

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Google Analytics -->
    <?php if (env('GOOGLE_ANALYTICS_ID')): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= env('GOOGLE_ANALYTICS_ID') ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '<?= env('GOOGLE_ANALYTICS_ID') ?>');
        </script>
    <?php endif; ?>

    <!-- Facebook Pixel -->
    <?php if (env('FACEBOOK_PIXEL_ID')): ?>
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= env('FACEBOOK_PIXEL_ID') ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=<?= env('FACEBOOK_PIXEL_ID') ?>&ev=PageView&noscript=1" /></noscript>
    <?php endif; ?>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar bg-primary text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center">
                        <a href="tel:<?= htmlspecialchars($contactConfig['phone']) ?>" class="text-white me-3">
                            <i class="fas fa-phone me-1"></i><?= htmlspecialchars($contactConfig['phone']) ?>
                        </a>
                        <div class="social-links">
                            <?php if ($socialConfig['facebook']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['facebook']) ?>" target="_blank" class="text-white me-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($socialConfig['twitter']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['twitter']) ?>" target="_blank" class="text-white me-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($socialConfig['linkedin']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['linkedin']) ?>" target="_blank" class="text-white me-2">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($socialConfig['instagram']): ?>
                                <a href="<?= htmlspecialchars($socialConfig['instagram']) ?>" target="_blank" class="text-white">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-end">
                    <?php if ($isLoggedIn): ?>
                        <!-- User Profile Menu -->
                        <div class="user-profile-menu">
                            <div class="dropdown">
                                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?= htmlspecialchars($currentUser['first_name'] ?? 'User') ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header">
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></div>
                                                    <div class="user-email text-muted small"><?= htmlspecialchars($currentUser['email']) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= app_url('user/dashboard.php') ?>">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= app_url('user/courses.php') ?>">
                                            <i class="fas fa-graduation-cap me-2"></i>Kozi Zangu
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= app_url('user/profile.php') ?>">
                                            <i class="fas fa-user-edit me-2"></i>Badilisha Profaili
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= app_url('logout.php') ?>">
                                            <i class="fas fa-sign-out-alt me-2"></i>Ondoka
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Signup Buttons -->
                        <div class="auth-buttons">
                            <a href="#" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>Ingia
                            </a>
                            <a href="#" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#signupModal">
                                <i class="fas fa-user-plus me-1"></i>Jisajili
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= app_url() ?>">
                <img src="<?= asset('images/logo/logo.png') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" height="50">
                <!-- <span class="ms-2"><?= htmlspecialchars($appConfig['name']) ?></span> -->
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= app_url() ?>">
                            NYUMBANI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('kozi.php') ?>">
                            KOZI ZOTE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('fursa.php') ?>">
                            FURSA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            SOKO
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('soko.php') ?>">SOKO</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('biashara.php') ?>">TANGAZA BIASHARA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('panda-market.php') ?>">PANDA MARKET</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            HABARI
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('wanufaika.php') ?>">WANUFAIKA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('habari.php') ?>">BLOG</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('uliza-swali.php') ?>">
                            ONGEA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            PANDA CHAT
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('uliza-swali.php') ?>">Uliza swali kwa mtaalamu</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('fomu.php') ?>">Fomu ya kuwa mtaalamu</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <style>
        /* User Profile Menu Styles */
        .user-profile-menu .dropdown-menu {
            min-width: 280px;
            padding: 0;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            margin-top: 8px;
        }

        .user-profile-menu .dropdown-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px 12px 0 0;
        }

        .user-profile-menu .user-avatar {
            color: var(--primary-color, #ffbc3b);
        }

        .user-profile-menu .user-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .user-profile-menu .user-email {
            font-size: 0.875rem;
        }

        .user-profile-menu .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: #4a5568;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }

        .user-profile-menu .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--primary-color, #ffbc3b);
            transform: translateX(5px);
        }

        .user-profile-menu .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .user-profile-menu .dropdown-divider {
            margin: 0.5rem 0;
            border-color: #e2e8f0;
        }

        .user-profile-menu .dropdown-toggle {
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .user-profile-menu .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .user-profile-menu .dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-profile-menu .dropdown-menu {
                min-width: 250px;
                margin-top: 5px;
            }

            .user-profile-menu .dropdown-header {
                padding: 1rem;
            }
        }

        /* Enhanced mobile dropdown menu styles */
        @media (max-width: 991.98px) {
            .navbar-nav .dropdown-menu {
                position: static !important;
                float: none;
                width: 100%;
                margin-top: 0;
                border: none;
                box-shadow: none;
                background-color: #f8f9fa;
                border-radius: 8px;
                margin-left: 1rem;
                margin-right: 1rem;
                margin-bottom: 0.5rem;
            }

            .navbar-nav .dropdown-toggle::after {
                float: right;
                margin-top: 8px;
                transition: transform 0.2s ease;
            }

            .navbar-nav .dropdown.show .dropdown-toggle::after {
                transform: rotate(180deg);
            }

            .navbar-nav .dropdown-item {
                padding: 0.75rem 1.5rem;
                border-radius: 6px;
                margin: 0.25rem 0.5rem;
                transition: all 0.2s ease;
            }

            .navbar-nav .dropdown-item:hover {
                background-color: var(--primary-color, #ffbc3b);
                color: white;
                transform: translateX(8px);
            }

            /* Prevent dropdown from closing when clicking inside */
            .navbar-nav .dropdown-menu {
                pointer-events: auto;
            }

            /* Add smooth animation for dropdown */
            .navbar-nav .dropdown-menu {
                transition: all 0.3s ease;
                opacity: 0;
                max-height: 0;
                overflow: hidden;
            }

            .navbar-nav .dropdown.show .dropdown-menu {
                opacity: 1;
                max-height: 300px;
            }
        }

        /* Ensure dropdowns work properly on all devices */
        .dropdown-menu.show {
            display: block !important;
        }

        /* Fix for touch devices */
        @media (hover: none) and (pointer: coarse) {
            .dropdown-toggle {
                min-height: 44px;
                /* Minimum touch target size */
            }

            .dropdown-item {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
        }

        /* Additional mobile dropdown fixes */
        @media (max-width: 991.98px) {

            /* Ensure dropdown menus are visible when open */
            .navbar-nav .dropdown.show .dropdown-menu {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                max-height: 300px !important;
                transform: none !important;
            }

            /* Prevent dropdown from being hidden by overflow */
            .navbar-collapse {
                overflow: visible !important;
            }

            /* Keep navbar open when dropdowns are active */
            .navbar-collapse.show {
                overflow: visible !important;
            }

            /* Ensure proper z-index for dropdowns */
            .navbar-nav .dropdown-menu {
                z-index: 1001;
                position: relative !important;
                transform: none !important;
                left: auto !important;
                right: auto !important;
                top: auto !important;
                bottom: auto !important;
            }

            /* Smooth transitions for dropdown items */
            .navbar-nav .dropdown-item {
                opacity: 0;
                transform: translateX(-10px);
                transition: all 0.3s ease;
            }

            .navbar-nav .dropdown.show .dropdown-item {
                opacity: 1;
                transform: translateX(0);
            }

            /* Stagger animation for dropdown items */
            .navbar-nav .dropdown.show .dropdown-item:nth-child(1) {
                transition-delay: 0.1s;
            }

            .navbar-nav .dropdown.show .dropdown-item:nth-child(2) {
                transition-delay: 0.15s;
            }

            .navbar-nav .dropdown.show .dropdown-item:nth-child(3) {
                transition-delay: 0.2s;
            }

            .navbar-nav .dropdown.show .dropdown-item:nth-child(4) {
                transition-delay: 0.25s;
            }

            .navbar-nav .dropdown.show .dropdown-item:nth-child(5) {
                transition-delay: 0.3s;
            }

            /* Prevent navbar from closing when dropdowns are open */
            .navbar-nav .dropdown.show~.navbar-nav,
            .navbar-nav .dropdown.show+.navbar-nav {
                display: block !important;
            }

            /* Ensure navbar stays visible when dropdowns are active */
            .navbar-collapse.show .navbar-nav .dropdown.show {
                position: relative;
                z-index: 1002;
            }
        }

        /* Global dropdown fixes */
        .dropdown-menu {
            pointer-events: auto !important;
        }

        .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Prevent any CSS from hiding dropdowns */
        .dropdown-menu[style*="display: none"] {
            display: block !important;
        }

        .dropdown-menu[style*="visibility: hidden"] {
            visibility: visible !important;
        }

        /* Prevent navbar from closing when dropdowns are active */
        @media (max-width: 991.98px) {
            .navbar-collapse.show {
                display: block !important;
                overflow: visible !important;
                height: auto !important;
                max-height: none !important;
            }

            /* Ensure navbar stays open when dropdowns are active */
            .navbar-collapse.show .navbar-nav .dropdown.show {
                position: relative;
                z-index: 1002;
            }

            /* Basic dropdown protection without interfering with navbar toggle */

            /* Ensure dropdowns don't cause navbar to collapse */
            .navbar-nav .dropdown.show {
                position: relative;
                z-index: 1002;
            }

            .navbar-nav .dropdown.show .dropdown-menu {
                position: relative !important;
                z-index: 1003;
            }

            /* Ensure navbar toggle button works properly */
            .navbar-toggler {
                pointer-events: auto !important;
                cursor: pointer !important;
            }

            /* Basic navbar functionality - let Bootstrap handle the rest */
        }
    </style>

    <script>
        // Enhanced Bootstrap 5 dropdown handling for mobile
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Header script loaded - initializing dropdowns...');

            // Wait for Bootstrap to be fully loaded
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap detected, initializing dropdowns...');
                // Initialize Bootstrap dropdowns properly
                const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl, {
                        autoClose: true,
                        boundary: 'viewport'
                    });
                });
                console.log('Bootstrap dropdowns initialized:', dropdownList.length);
            } else {
                console.log('Bootstrap not detected, using custom dropdown handling...');
            }

            // Enhanced mobile dropdown behavior
            if (window.innerWidth <= 991.98) {
                console.log('Mobile view detected, setting up custom dropdown handling...');
                const mobileDropdowns = document.querySelectorAll('.navbar-nav .dropdown');
                console.log('Found mobile dropdowns:', mobileDropdowns.length);

                mobileDropdowns.forEach((dropdown, index) => {
                    const toggle = dropdown.querySelector('.dropdown-toggle');
                    const menu = dropdown.querySelector('.dropdown-menu');

                    console.log(`Setting up dropdown ${index + 1}:`, {
                        toggle: !!toggle,
                        menu: !!menu
                    });

                    if (toggle && menu) {
                        // Remove any existing Bootstrap event listeners
                        if (toggle._mobileDropdownHandler) {
                            toggle.removeEventListener('click', toggle._mobileDropdownHandler);
                        }

                        // Create new handler
                        toggle._mobileDropdownHandler = function(e) {
                            console.log('Dropdown toggle clicked:', e.target.textContent);
                            e.preventDefault();
                            e.stopPropagation();

                            // Prevent navbar from closing
                            const navbarCollapse = document.querySelector('.navbar-collapse');
                            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                                // Keep navbar open
                                navbarCollapse.classList.add('show');
                                navbarCollapse.style.display = 'block';
                                navbarCollapse.style.overflow = 'visible';
                            }

                            // Close other open dropdowns
                            mobileDropdowns.forEach(otherDropdown => {
                                if (otherDropdown !== dropdown && otherDropdown.classList.contains('show')) {
                                    console.log('Closing other dropdown');
                                    otherDropdown.classList.remove('show');
                                    const otherMenu = otherDropdown.querySelector('.dropdown-menu');
                                    if (otherMenu) {
                                        otherMenu.classList.remove('show');
                                        otherMenu.style.display = 'none';
                                        otherMenu.style.visibility = 'hidden';
                                        otherMenu.style.opacity = '0';
                                    }
                                }
                            });

                            // Toggle current dropdown
                            const isOpen = dropdown.classList.contains('show');
                            console.log('Dropdown state:', isOpen ? 'open' : 'closed');

                            if (isOpen) {
                                dropdown.classList.remove('show');
                                menu.classList.remove('show');
                                menu.style.display = 'none';
                                menu.style.visibility = 'hidden';
                                menu.style.opacity = '0';
                                console.log('Dropdown closed');
                            } else {
                                dropdown.classList.add('show');
                                menu.classList.add('show');
                                menu.style.display = 'block';
                                menu.style.visibility = 'visible';
                                menu.style.opacity = '1';
                                console.log('Dropdown opened');

                                // Ensure navbar stays open
                                if (navbarCollapse) {
                                    navbarCollapse.classList.add('show');
                                    navbarCollapse.style.display = 'block';
                                    navbarCollapse.style.overflow = 'visible';
                                }
                            }
                        };

                        // Add event listener
                        toggle.addEventListener('click', toggle._mobileDropdownHandler);
                        console.log('Event listener added to dropdown toggle');

                        // Prevent menu from closing when clicking inside
                        menu.addEventListener('click', function(e) {
                            console.log('Menu clicked, preventing propagation');
                            e.stopPropagation();
                        });

                        // Prevent menu from closing when clicking on dropdown items
                        const dropdownItems = menu.querySelectorAll('.dropdown-item');
                        dropdownItems.forEach((item, itemIndex) => {
                            item.addEventListener('click', function(e) {
                                console.log(`Dropdown item ${itemIndex + 1} clicked:`, item.textContent);
                                e.stopPropagation();
                                // Close dropdown after item click
                                setTimeout(() => {
                                    dropdown.classList.remove('show');
                                    menu.classList.remove('show');
                                    menu.style.display = 'none';
                                    menu.style.visibility = 'hidden';
                                    menu.style.opacity = '0';
                                    console.log('Dropdown closed after item selection');
                                }, 100);
                            });
                        });
                    }
                });

                // Close dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.navbar-nav .dropdown')) {
                        console.log('Click outside dropdowns detected, closing all...');
                        mobileDropdowns.forEach(dropdown => {
                            dropdown.classList.remove('show');
                            const menu = dropdown.querySelector('.dropdown-menu');
                            if (menu) {
                                menu.classList.remove('show');
                                menu.style.display = 'none';
                                menu.style.visibility = 'hidden';
                                menu.style.opacity = '0';
                            }
                        });
                    }
                });

                // Close dropdowns when navbar collapses
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse) {
                    // Prevent navbar from closing when dropdowns are open
                    navbarCollapse.addEventListener('hide.bs.collapse', function(e) {
                        const hasOpenDropdowns = Array.from(mobileDropdowns).some(dropdown =>
                            dropdown.classList.contains('show')
                        );

                        if (hasOpenDropdowns) {
                            console.log('Preventing navbar collapse - dropdowns are open');
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    });

                    navbarCollapse.addEventListener('hidden.bs.collapse', function() {
                        console.log('Navbar collapsed, closing dropdowns...');
                        mobileDropdowns.forEach(dropdown => {
                            dropdown.classList.remove('show');
                            const menu = dropdown.querySelector('.dropdown-menu');
                            if (menu) {
                                menu.classList.remove('show');
                                menu.style.display = 'none';
                                menu.style.visibility = 'hidden';
                                menu.style.opacity = '0';
                            }
                        });
                    });
                }

                // Prevent navbar toggle button from closing navbar when dropdowns are open
                const navbarToggler = document.querySelector('.navbar-toggler');
                if (navbarToggler) {
                    console.log('Navbar toggler found:', navbarToggler);

                    navbarToggler.addEventListener('click', function(e) {
                        console.log('Navbar toggler clicked');
                        const hasOpenDropdowns = Array.from(mobileDropdowns).some(dropdown =>
                            dropdown.classList.contains('show')
                        );

                        if (hasOpenDropdowns) {
                            console.log('Closing dropdowns before navbar toggle');
                            // Close all dropdowns before allowing navbar toggle
                            mobileDropdowns.forEach(dropdown => {
                                dropdown.classList.remove('show');
                                const menu = dropdown.querySelector('.dropdown-menu');
                                if (menu) {
                                    menu.classList.remove('show');
                                    menu.style.display = 'none';
                                    menu.style.visibility = 'hidden';
                                    menu.style.opacity = '0';
                                }
                            });
                        }
                    });
                } else {
                    console.log('Navbar toggler not found');
                }

                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (window.innerWidth > 991.98) {
                            console.log('Switched to desktop view, resetting dropdowns...');
                            // Reset to desktop behavior
                            mobileDropdowns.forEach(dropdown => {
                                dropdown.classList.remove('show');
                                const menu = dropdown.querySelector('.dropdown-menu');
                                if (menu) {
                                    menu.classList.remove('show');
                                    menu.style.display = '';
                                    menu.style.visibility = '';
                                    menu.style.opacity = '';
                                }
                            });
                        }
                    }, 250);
                });
            }

            // Prevent any global click handlers from interfering
            document.addEventListener('click', function(e) {
                if (e.target.closest('.dropdown-menu')) {
                    console.log('Preventing global click handler interference');
                    e.stopPropagation();
                }
            }, true);

            // Simple solution: Only prevent dropdown clicks from closing navbar
            if (window.innerWidth <= 991.98) {
                // Add a simple event listener to prevent dropdown clicks from bubbling up
                document.addEventListener('click', function(e) {
                    // If clicking on a dropdown toggle, prevent it from affecting navbar
                    if (e.target.closest('.dropdown-toggle')) {
                        e.stopPropagation();
                    }
                }, true);

                // Add basic debugging for navbar toggle
                const navbarToggler = document.querySelector('.navbar-toggler');
                if (navbarToggler) {
                    console.log('Navbar toggler found and working');
                    navbarToggler.addEventListener('click', function() {
                        console.log('Navbar toggle clicked - should open/close normally');
                    });
                } else {
                    console.log('Navbar toggler not found - check HTML structure');
                }
            }

            console.log('Dropdown initialization complete');
        });
    </script>