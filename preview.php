<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - New Hero & Navbar Design</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar bg-primary text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center">
                        <a href="tel:+25573428334" class="text-white me-3">
                            <i class="fas fa-phone me-1"></i>+25573428334
                        </a>
                        <div class="social-links">
                            <a href="#" target="_blank" class="text-white me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" target="_blank" class="text-white me-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" target="_blank" class="text-white me-2">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" target="_blank" class="text-white">
                                <i class="fab fa-instagram"></i>
                            </a>
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

    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/images/logo/logo.png" alt="Panda Digital" height="50">
                <span class="ms-2">Panda Digital</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-home me-1"></i>NYUMBANI
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-graduation-cap me-1"></i>KOZI ZOTE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-lightbulb me-1"></i>FURSA
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart me-1"></i>SOKO
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">SOKO</a></li>
                            <li><a class="dropdown-item" href="#">TANGAZA BIASHARA</a></li>
                            <li><a class="dropdown-item" href="#">PANDA MARKET</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-newspaper me-1"></i>HABARI
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">BLOG</a></li>
                            <li><a class="dropdown-item" href="#">HABARI</a></li>
                            <li><a class="dropdown-item" href="#">MATUKIO</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-info-circle me-1"></i>KUHUSU
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modern Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 text-center" data-aos="fade-up">
                        <div class="hero-content">
                            <div class="hero-badge">
                                <i class="fas fa-star me-2"></i>Jukwaa la Kwanza la Kidijitali kwa Wasichana
                            </div>
                            <h1 class="hero-title">Kuwa Mjasiriamali wa Kidijitali</h1>
                            <p class="hero-subtitle">Jifunze ujuzi wa kidijitali, uwe na biashara yako, na uweze kujitegemea kiuchumi kupitia Panda Digital</p>
                            <div class="hero-buttons">
                                <a href="#" class="btn btn-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>Anza Kozi
                                </a>
                                <a href="#" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-lightbulb me-2"></i>Tazama Fursa
                                </a>
                            </div>
                            <div class="hero-stats">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">5,000+</span>
                                    <span class="hero-stat-label">Wanafunzi</span>
                                </div>
                                <div class="hero-stat">
                                    <span class="hero-stat-number">200+</span>
                                    <span class="hero-stat-label">Kozi</span>
                                </div>
                                <div class="hero-stat">
                                    <span class="hero-stat-number">1,500+</span>
                                    <span class="hero-stat-label">Biashara</span>
                                </div>
                                <div class="hero-stat">
                                    <span class="hero-stat-number">95%</span>
                                    <span class="hero-stat-label">Mafanikio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <a href="#content" class="scroll-down">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Content Section -->
    <section id="content" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="mb-4">🎉 New Design Features</h2>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-palette fa-3x text-primary mb-3"></i>
                                    <h5>Modern Hero</h5>
                                    <p>Gradient background with animated stats and modern typography</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bars fa-3x text-secondary mb-3"></i>
                                    <h5>Enhanced Navbar</h5>
                                    <p>Glassmorphism effect with smooth scroll transitions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                                    <h5>Responsive Design</h5>
                                    <p>Perfect on all devices with smooth animations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>