<?php
require_once 'config/init.php';
include 'includes/header.php';
?>

<!-- Hero Section with Full Background -->
<section class="hero-section">
    <div class="hero-background">
        <div class="hero-overlay"></div>
    </div>
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 hero-content" data-aos="fade-up">
                <h1 class="hero-title">Kuwa Mjasiriamali wa Kidijitali</h1>
                <p class="hero-subtitle">Jifunze ujuzi wa kidijitali, uwe na biashara yako, na uweze kujitegemea kiuchumi kupitia <?= htmlspecialchars($appConfig['name']) ?></p>
                <div class="hero-buttons">
                    <a href="<?= app_url('courses.php') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-play me-2"></i>Anza Kozi
                    </a>
                    <a href="<?= app_url('opportunities.php') ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-lightbulb me-2"></i>Tazama Fursa
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="hero-image-container">
                    <img src="<?= asset('images/banner/banner-1.jpg') ?>" alt="<?= htmlspecialchars($appConfig['name']) ?>" class="hero-image">
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <a href="#stats" class="scroll-down">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<!-- Stats Section -->
<section id="stats" class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">Wanafunzi Waliojifunza</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Kozi za Kidijitali</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-number">1,500+</div>
                    <div class="stat-label">Biashara Zilizoundwa</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Uwezo wa Kujitegemea</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Kwa nini <?= htmlspecialchars($appConfig['name']) ?>?</h2>
                <p class="section-subtitle">Tunakupa fursa za kujifunza na kuendeleza biashara yako ya kidijitali</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">Kozi za Ubora</h3>
                    <p class="feature-description">Jifunze kutoka kwa wataalamu wenye uzoefu katika nyanja mbalimbali za kidijitali</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Jumuiya ya Wajasiriamali</h3>
                    <p class="feature-description">Unganisha na wajasiriamali wengine na uweze kujifunza kutoka kwao</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="feature-title">Fursa za Biashara</h3>
                    <p class="feature-description">Pata fursa za kufanya biashara na kuuza bidhaa zako mtandaoni</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Ufikivu wa Simu</h3>
                    <p class="feature-description">Jifunze kutoka popote na wakati wowote kupitia simu yako</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="feature-title">Cheti za Uhalali</h3>
                    <p class="feature-description">Pata cheti za kuhalalisha ujuzi wako na kuongeza uwezo wa kazi</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">Msaada wa 24/7</h3>
                    <p class="feature-description">Pata msaada wakati wowote kutoka kwa timu yetu ya wataalamu</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Courses Section -->
<section class="courses-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Kozi Maarufu</h2>
                <p class="section-subtitle">Chagua kutoka kwa kozi zetu za ubora za kidijitali</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="course-card">
                    <div class="course-image" style="background-image: url('<?= asset('images/counter/biashara_wanawake.png') ?>');">
                        <div class="course-overlay">
                            <a href="<?= app_url('course-details.php?id=1') ?>" class="btn btn-light">Tazama Kozi</a>
                        </div>
                    </div>
                    <div class="course-content">
                        <h4 class="course-title">Ujasiriamali wa Kidijitali</h4>
                        <p class="course-instructor">Na Sarah Mwangi</p>
                        <div class="course-meta">
                            <span class="course-price">TSh 50,000</span>
                            <span class="course-rating">
                                <i class="fas fa-star"></i> 4.8
                            </span>
                        </div>
                        <a href="<?= app_url('course-details.php?id=1') ?>" class="btn btn-primary w-100">Jisajili Sasa</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="course-card">
                    <div class="course-image" style="background-image: url('<?= asset('images/counter/kampeni.png') ?>');">
                        <div class="course-overlay">
                            <a href="<?= app_url('course-details.php?id=2') ?>" class="btn btn-light">Tazama Kozi</a>
                        </div>
                    </div>
                    <div class="course-content">
                        <h4 class="course-title">Masoko ya Mtandaoni</h4>
                        <p class="course-instructor">Na Fatima Hassan</p>
                        <div class="course-meta">
                            <span class="course-price">TSh 75,000</span>
                            <span class="course-rating">
                                <i class="fas fa-star"></i> 4.9
                            </span>
                        </div>
                        <a href="<?= app_url('course-details.php?id=2') ?>" class="btn btn-primary w-100">Jisajili Sasa</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="course-card">
                    <div class="course-image" style="background-image: url('<?= asset('images/counter/wanawake_panda_sms.png') ?>');">
                        <div class="course-overlay">
                            <a href="<?= app_url('course-details.php?id=3') ?>" class="btn btn-light">Tazama Kozi</a>
                        </div>
                    </div>
                    <div class="course-content">
                        <h4 class="course-title">Uandishi wa Maudhui</h4>
                        <p class="course-instructor">Na Amina Juma</p>
                        <div class="course-meta">
                            <span class="course-price">TSh 40,000</span>
                            <span class="course-rating">
                                <i class="fas fa-star"></i> 4.7
                            </span>
                        </div>
                        <a href="<?= app_url('course-details.php?id=3') ?>" class="btn btn-primary w-100">Jisajili Sasa</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= app_url('courses.php') ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>Tazama Kozi Zote
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Wanafunzi Wetu Wanazungumza</h2>
                <p class="section-subtitle">Sikia kutoka kwa wanafunzi wetu waliofanikiwa</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">"<?= htmlspecialchars($appConfig['name']) ?> ilinisaidia kujifunza ujasiriamali wa kidijitali na sasa nina biashara yangu ya mtandaoni inayofanya faida nzuri."</p>
                    <div class="testimonial-author">
                        <img src="<?= asset('images/testimonials/user1.jpg') ?>" alt="Mariam Ali" class="testimonial-avatar">
                        <div>
                            <h5 class="testimonial-name">Mariam Ali</h5>
                            <p class="testimonial-position">Mjasiriamali wa Kidijitali</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">"Kozi za <?= htmlspecialchars($appConfig['name']) ?> zilikuwa na ubora wa juu na walinisaidia kupata kazi nzuri katika kampuni ya teknolojia."</p>
                    <div class="testimonial-author">
                        <img src="<?= asset('images/testimonials/user2.jpg') ?>" alt="Grace Mwambene" class="testimonial-avatar">
                        <div>
                            <h5 class="testimonial-name">Grace Mwambene</h5>
                            <p class="testimonial-position">Mtaalamu wa Masoko</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">"Kupitia <?= htmlspecialchars($appConfig['name']) ?>, niliweza kujifunza ujuzi wa kidijitali na sasa ninaweza kujitegemea kiuchumi."</p>
                    <div class="testimonial-author">
                        <img src="<?= asset('images/testimonials/user3.jpg') ?>" alt="Halima Omar" class="testimonial-avatar">
                        <div>
                            <h5 class="testimonial-name">Halima Omar</h5>
                            <p class="testimonial-position">Mjasiriamali wa Biashara</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="cta-title">Tayari Kuanza Safari Yako?</h2>
                <p class="cta-subtitle">Jisajili sasa na uanze kujifunza ujuzi wa kidijitali na kuendeleza biashara yako</p>
            </div>
            <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                <a href="<?= app_url('signup.php') ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-rocket me-2"></i>Anza Sasa
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>