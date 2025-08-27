<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/GoogleOAuthService.php';

// Initialize authentication service
$authService = new AuthService();
$isLoggedIn = $authService->isLoggedIn();
$currentUser = $isLoggedIn ? $authService->getCurrentUser() : null;

// Function to determine current page and set active navigation
function isCurrentPage($pagePath)
{
    $currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

    // Check if current path matches the page path
    if ($pagePath === '/' || $pagePath === '') {
        return $currentPath === '/' || $currentPath === '/index.php' || $currentPath === '/panda/index.php' || $currentPath === '/panda/pandadigitalV3/index.php';
    }

    return strpos($currentPath, $pagePath) !== false;
}
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

            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- Custom Mobile Nav Toggle -->
            <button class="mobile-nav-toggle d-lg-none" type="button" id="mobileNavToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('/') ? 'active' : '' ?>" href="<?= app_url() ?>">
                            NYUMBANI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('about.php') ? 'active' : '' ?>" href="<?= app_url('about.php') ?>">
                            KUHUSU SISI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('kozi.php') ? 'active' : '' ?>" href="<?= app_url('kozi.php') ?>">
                            KOZI ZOTE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('fursa.php') ? 'active' : '' ?>" href="<?= app_url('fursa.php') ?>">
                            FURSA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= isCurrentPage('soko.php') || isCurrentPage('biashara.php') || isCurrentPage('panda-market.php') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                            SOKO
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('soko.php') ?>">SOKO</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('biashara.php') ?>">TANGAZA BIASHARA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('panda-market.php') ?>">PANDA MARKET</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= isCurrentPage('wanufaika.php') || isCurrentPage('habari.php') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                            HABARI
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('wanufaika.php') ?>">WANUFAIKA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('habari.php') ?>">BLOG</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('uliza-swali.php') ? 'active' : '' ?>" href="<?= app_url('uliza-swali.php') ?>">
                            ONGEA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= isCurrentPage('uliza-swali.php') || isCurrentPage('fomu.php') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
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

    <!-- Custom Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-content">
            <div class="mobile-nav-header">
                <div class="mobile-nav-brand"><?= htmlspecialchars($appConfig['name']) ?></div>
                <button class="mobile-nav-close" id="mobileNavClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <ul class="mobile-nav-menu">
                <li class="mobile-nav-item">
                    <a href="<?= app_url() ?>" class="mobile-nav-link <?= isCurrentPage('/') ? 'active' : '' ?>">
                        <i class="fas fa-home me-2"></i>NYUMBANI
                    </a>
                </li>

                <li class="mobile-nav-item">
                    <a href="<?= app_url('about.php') ?>" class="mobile-nav-link <?= isCurrentPage('about.php') ? 'active' : '' ?>">
                        <i class="fas fa-info-circle me-2"></i>KUHUSU SISI
                    </a>
                </li>

                <li class="mobile-nav-item">
                    <a href="<?= app_url('kozi.php') ?>" class="mobile-nav-link <?= isCurrentPage('kozi.php') ? 'active' : '' ?>">
                        <i class="fas fa-graduation-cap me-2"></i>KOZI ZOTE
                    </a>
                </li>

                <li class="mobile-nav-item">
                    <a href="<?= app_url('fursa.php') ?>" class="mobile-nav-link <?= isCurrentPage('fursa.php') ? 'active' : '' ?>">
                        <i class="fas fa-briefcase me-2"></i>FURSA
                    </a>
                </li>

                <li class="mobile-nav-item mobile-dropdown" id="sokoDropdown">
                    <button class="mobile-dropdown-toggle <?= isCurrentPage('soko.php') || isCurrentPage('biashara.php') || isCurrentPage('panda-market.php') ? 'active' : '' ?>" type="button">
                        <span><i class="fas fa-store me-2"></i>SOKO</span>
                    </button>
                    <div class="mobile-dropdown-menu">
                        <a href="<?= app_url('soko.php') ?>" class="mobile-dropdown-item">SOKO</a>
                        <a href="<?= app_url('biashara.php') ?>" class="mobile-dropdown-item">TANGAZA BIASHARA</a>
                        <a href="<?= app_url('panda-market.php') ?>" class="mobile-dropdown-item">PANDA MARKET</a>
                    </div>
                </li>

                <li class="mobile-nav-item mobile-dropdown" id="habariDropdown">
                    <button class="mobile-dropdown-toggle <?= isCurrentPage('wanufaika.php') || isCurrentPage('habari.php') ? 'active' : '' ?>" type="button">
                        <span><i class="fas fa-newspaper me-2"></i>HABARI</span>
                    </button>
                    <div class="mobile-dropdown-menu">
                        <a href="<?= app_url('wanufaika.php') ?>" class="mobile-dropdown-item">WANUFAIKA</a>
                        <a href="<?= app_url('habari.php') ?>" class="mobile-dropdown-item">BLOG</a>
                    </div>
                </li>

                <li class="mobile-nav-item">
                    <a href="<?= app_url('uliza-swali.php') ?>" class="mobile-nav-link <?= isCurrentPage('uliza-swali.php') ? 'active' : '' ?>">
                        <i class="fas fa-comments me-2"></i>ONGEA
                    </a>
                </li>

                <li class="mobile-nav-item mobile-dropdown" id="pandaChatDropdown">
                    <button class="mobile-dropdown-toggle <?= isCurrentPage('uliza-swali.php') || isCurrentPage('fomu.php') ? 'active' : '' ?>" type="button">
                        <span><i class="fas fa-headset me-2"></i>PANDA CHAT</span>
                    </button>
                    <div class="mobile-dropdown-menu">
                        <a href="<?= app_url('uliza-swali.php') ?>" class="mobile-dropdown-item">Uliza swali kwa mtaalamu</a>
                        <a href="<?= app_url('fomu.php') ?>" class="mobile-dropdown-item">Fomu ya kuwa mtaalamu</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <style>
        /* Force desktop navbar active state styling */
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active:hover,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active:focus,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .dropdown-toggle.active,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .dropdown-toggle.active:hover,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .dropdown-toggle.active:focus {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
            border: none !important;
            outline: none !important;
            box-shadow: 0 2px 8px rgba(95, 69, 148, 0.3) !important;
        }

        /* Additional desktop navbar overrides */
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active:hover,
        .navbar.navbar-expand-lg .navbar-nav .nav-item .nav-link.active:focus {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Target the specific navbar structure */
        .navbar.navbar-expand-lg.navbar-light.sticky-top .navbar-nav .nav-item .nav-link.active {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Target navbar-collapse specifically */
        .navbar-collapse .navbar-nav .nav-item .nav-link.active {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Force override for any remaining conflicts */
        .navbar-nav .nav-item .nav-link.active,
        .navbar-nav .nav-item .nav-link.active:hover,
        .navbar-nav .nav-item .nav-link.active:focus {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Ensure active state persists */
        .navbar-nav .nav-item .nav-link[class*="active"] {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Additional specificity for dropdown toggles */
        .navbar-nav .nav-item .dropdown-toggle.active,
        .navbar-nav .nav-item .dropdown-toggle.active:hover,
        .navbar-nav .nav-item .dropdown-toggle.active:focus {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Maximum specificity override */
        html body .navbar.navbar-expand-lg.navbar-light.sticky-top .navbar-collapse .navbar-nav .nav-item .nav-link.active {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

        /* Force override for any remaining conflicts */
        .navbar-nav .nav-item .nav-link.active,
        .navbar-nav .nav-item .nav-link.active:hover,
        .navbar-nav .nav-item .nav-link.active:focus,
        .navbar-nav .nav-item .nav-link.active:visited,
        .navbar-nav .nav-item .nav-link.active:active {
            background: #5f4594 !important;
            background-color: #5f4594 !important;
            color: #ffbc3b !important;
        }

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

        /* Custom Mobile Navigation - Completely Remade */
        @media (max-width: 991.98px) {

            /* Hide Bootstrap navbar on mobile */
            .navbar-collapse {
                display: none !important;
            }

            /* Custom mobile navigation */
            .mobile-nav {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: rgba(0, 0, 0, 0.8);
                z-index: 9999;
                overflow-y: auto;
            }

            .mobile-nav.show {
                display: block;
            }

            .mobile-nav-content {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                max-width: 350px;
                height: 100vh;
                background: white;
                padding: 2rem 1rem;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .mobile-nav.show .mobile-nav-content {
                transform: translateX(0);
            }

            .mobile-nav-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid var(--primary-color, #ffbc3b);
            }

            .mobile-nav-brand {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--primary-color, #ffbc3b);
            }

            .mobile-nav-close {
                background: none;
                border: none;
                font-size: 2rem;
                color: #666;
                cursor: pointer;
                padding: 0;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
            }

            .mobile-nav-close:hover {
                background: #f0f0f0;
                color: #333;
            }

            .mobile-nav-menu {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .mobile-nav-item {
                margin-bottom: 0.5rem;
            }

            .mobile-nav-link {
                display: block;
                padding: 1rem;
                color: #333;
                text-decoration: none;
                border-radius: 8px;
                transition: all 0.2s ease;
                font-weight: 500;
            }

            .mobile-nav-link:hover {
                background: var(--primary-color, #ffbc3b);
                color: white;
                transform: translateX(8px);
            }

            .mobile-nav-link.active {
                background: #5f4594 !important;
                color: #ffbc3b !important;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(95, 69, 148, 0.3);
            }

            /* Mobile dropdown styles */
            .mobile-dropdown {
                margin-bottom: 0.5rem;
            }

            .mobile-dropdown-toggle {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                padding: 1rem;
                background: none;
                border: none;
                color: #333;
                text-align: left;
                border-radius: 8px;
                transition: all 0.2s ease;
                font-weight: 500;
                cursor: pointer;
            }

            .mobile-dropdown-toggle:hover {
                background: #f8f9fa;
                color: var(--primary-color, #ffbc3b);
            }

            .mobile-dropdown-toggle.active {
                background: #5f4594 !important;
                color: #ffbc3b !important;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(95, 69, 148, 0.3);
            }

            .mobile-dropdown-toggle::after {
                content: '▼';
                font-size: 0.8rem;
                transition: transform 0.2s ease;
            }

            .mobile-dropdown-toggle.active::after {
                transform: rotate(180deg);
            }

            .mobile-dropdown-menu {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                background: #f8f9fa;
                border-radius: 8px;
                margin: 0.5rem 0;
            }

            .mobile-dropdown.show .mobile-dropdown-menu {
                max-height: 300px;
            }

            .mobile-dropdown-item {
                display: block;
                padding: 0.75rem 1.5rem;
                color: #555;
                text-decoration: none;
                border-radius: 6px;
                margin: 0.25rem 0.5rem;
                transition: all 0.2s ease;
                font-weight: 400;
            }

            .mobile-dropdown-item:hover {
                background: var(--primary-color, #ffbc3b);
                color: white;
                transform: translateX(8px);
            }

            /* Mobile nav toggle button */
            .mobile-nav-toggle {
                display: block !important;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #333;
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .mobile-nav-toggle:hover {
                background: #f0f0f0;
                color: var(--primary-color, #ffbc3b);
            }
        }

        /* Hide mobile nav toggle on desktop */
        @media (min-width: 992px) {
            .mobile-nav-toggle {
                display: none !important;
            }

            /* Hide entire mobile navigation on desktop */
            .mobile-nav {
                display: none !important;
            }

            /* Ensure Bootstrap navbar is visible on desktop */
            .navbar-collapse {
                display: flex !important;
            }

            /* Show Bootstrap navbar toggler on desktop for any remaining mobile functionality */
            .navbar-toggler {
                display: none !important;
            }
        }

        /* Ensure mobile navigation is only visible on mobile */
        @media (max-width: 991.98px) {

            /* Hide Bootstrap navbar on mobile */
            .navbar-collapse {
                display: none !important;
            }

            /* Show mobile nav toggle on mobile */
            .mobile-nav-toggle {
                display: block !important;
            }

            /* Hide Bootstrap navbar toggler on mobile */
            .navbar-toggler {
                display: none !important;
            }
        }
    </style>

    <script>
        // Simple Mobile Navigation System - Completely Remade
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Mobile navigation system initializing...');

            // Get mobile navigation elements
            const mobileNav = document.getElementById('mobileNav');
            const mobileNavToggle = document.getElementById('mobileNavToggle');
            const mobileNavClose = document.getElementById('mobileNavClose');
            const mobileDropdowns = document.querySelectorAll('.mobile-dropdown');

            // Mobile navigation toggle
            if (mobileNavToggle) {
                mobileNavToggle.addEventListener('click', function() {
                    console.log('Mobile nav toggle clicked');
                    mobileNav.classList.add('show');
                    document.body.style.overflow = 'hidden'; // Prevent background scrolling
                });
            }

            // Mobile navigation close
            if (mobileNavClose) {
                mobileNavClose.addEventListener('click', function() {
                    console.log('Mobile nav close clicked');
                    mobileNav.classList.remove('show');
                    document.body.style.overflow = ''; // Restore scrolling

                    // Close all dropdowns when closing nav
                    mobileDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                });
            }

            // Close mobile nav when clicking outside
            mobileNav.addEventListener('click', function(e) {
                if (e.target === mobileNav) {
                    console.log('Clicked outside mobile nav content');
                    mobileNav.classList.remove('show');
                    document.body.style.overflow = '';

                    // Close all dropdowns
                    mobileDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                }
            });

            // Handle mobile dropdowns
            mobileDropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.mobile-dropdown-toggle');
                const menu = dropdown.querySelector('.mobile-dropdown-menu');

                if (toggle && menu) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        console.log('Mobile dropdown toggle clicked:', toggle.textContent.trim());

                        // Close other dropdowns
                        mobileDropdowns.forEach(otherDropdown => {
                            if (otherDropdown !== dropdown) {
                                otherDropdown.classList.remove('show');
                            }
                        });

                        // Toggle current dropdown
                        dropdown.classList.toggle('show');

                        if (dropdown.classList.contains('show')) {
                            console.log('Mobile dropdown opened');
                        } else {
                            console.log('Mobile dropdown closed');
                        }
                    });
                }
            });

            // Handle dropdown item clicks
            const mobileDropdownItems = document.querySelectorAll('.mobile-dropdown-item');
            mobileDropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    console.log('Mobile dropdown item clicked:', item.textContent.trim());

                    // Close mobile nav after item click
                    setTimeout(() => {
                        mobileNav.classList.remove('show');
                        document.body.style.overflow = '';

                        // Close all dropdowns
                        mobileDropdowns.forEach(dropdown => {
                            dropdown.classList.remove('show');
                        });
                    }, 100);
                });
            });

            // Close mobile nav on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileNav.classList.contains('show')) {
                    console.log('Escape key pressed, closing mobile nav');
                    mobileNav.classList.remove('show');
                    document.body.style.overflow = '';

                    // Close all dropdowns
                    mobileDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                }
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 991.98) {
                        console.log('Switched to desktop view, closing mobile nav');
                        mobileNav.classList.remove('show');
                        document.body.style.overflow = '';

                        // Close all dropdowns
                        mobileDropdowns.forEach(dropdown => {
                            dropdown.classList.remove('show');
                        });
                    }
                }, 250);
            });

            console.log('Mobile navigation system initialized successfully');
        });
    </script>