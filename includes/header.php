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
    </style>