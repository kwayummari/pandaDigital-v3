<?php
require_once __DIR__ . '/../config/init.php';
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
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= env('GOOGLE_ANALYTICS_ID') ?>');
    </script>
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if (env('FACEBOOK_PIXEL_ID')): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= env('FACEBOOK_PIXEL_ID') ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?= env('FACEBOOK_PIXEL_ID') ?>&ev=PageView&noscript=1"
    /></noscript>
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
                    <div class="auth-buttons">
                        <a href="#" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt me-1"></i>Ingia
                        </a>
                        <a href="#" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#signupModal">
                            <i class="fas fa-user-plus me-1"></i>Jisajili
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= app_url() ?>">
                <img src="<?= asset('images/logo/logo.png') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" height="50">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url() ?>">
                            <i class="fas fa-home me-1"></i>NYUMBANI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('courses.php') ?>">
                            <i class="fas fa-graduation-cap me-1"></i>KOZI ZOTE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('opportunities.php') ?>">
                            <i class="fas fa-lightbulb me-1"></i>FURSA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart me-1"></i>SOKO
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('marketplace.php') ?>">SOKO</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('business.php') ?>">TANGAZA BIASHARA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('panda-market.php') ?>">PANDA MARKET</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-newspaper me-1"></i>HABARI
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('success-stories.php') ?>">WANUFAIKA</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('blog.php') ?>">BLOG</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('chat.php') ?>">
                            <i class="fas fa-comments me-1"></i>ONGEA HUB
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-robot me-1"></i>Panda Chat
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= app_url('expert-chat.php') ?>">Uliza swali kwa mtaalamu</a></li>
                            <li><a class="dropdown-item" href="<?= app_url('become-expert.php') ?>">Fomu ya kuwa mtaalamu</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav> 