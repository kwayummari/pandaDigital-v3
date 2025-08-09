<?php
require_once 'config/init.php';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content" data-aos="fade-right">
                <h1 class="hero-title">Kuwa Mjasiriamali wa Kidijitali</h1>
                <p class="hero-subtitle">Jifunze ujuzi wa kidijitali, uwe na biashara yako, na uweze kujitegemea kiuchumi kupitia <?= htmlspecialchars($appConfig['name']) ?></p>
                <div class="hero-buttons">
                    <a href="<?= app_url('courses.php') ?>" class="btn btn-primary">
                        <i class="fas fa-play me-2"></i>Anza Kozi
                    </a>
                    <a href="<?= app_url('opportunities.php') ?>" class="btn btn-outline-light">
                        <i class="fas fa-lightbulb me-2"></i>Tazama Fursa
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="<?= asset('images/banner/banner-1.jpg') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" class="img-fluid rounded-3 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">Wanafunzi Waliojifunza</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Kozi za Kidijitali</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-number">1,500+</div>
                    <div class="stat-label">Biashara Zilizoundwa</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Uwezo wa Kujitegemea</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>