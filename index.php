<?php
require_once 'config/init.php';
require_once 'models/Blog.php';
require_once 'models/Fursa.php';

include 'includes/header.php';

// Initialize models
$blogModel = new Blog();
$fursaModel = new Fursa();

// Fetch data from database
$latestBlogPosts = $blogModel->getLatestPosts(6);
$latestOpportunities = $fursaModel->getLatestOpportunities(6);
?>


<!-- Modern Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center" data-aos="fade-up">
                    <div class="hero-content">
                        <div class="hero-badge">
                            Pata fursa, ujuzi wa kidijitali, anzisha biashara, na jijengee uhuru wa kiuchumi kupitia Panda Digital.
                        </div>
                        <h1 class="hero-title">Kuwa Mjasiriamali wa Kidijitali</h1>
                        <p class="hero-subtitle">Jisajili. Chagua kozi. Jifunze. Pata cheti. Kutana na fursa. <?= htmlspecialchars($appConfig['name']) ?></p>
                        <div class="hero-buttons">
                            <a href="<?= app_url('kozi.php') ?>" class="btn btn-primary">
                                Anza Kozi
                            </a>
                            <a href="<?= app_url('fursa.php') ?>" class="btn btn-outline-primary">
                                Tazama Fursa
                            </a>
                        </div>
                    </div>
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

<!-- Kuhusu Jukwaa la Panda Section -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right">
                <div class="about-content">
                    <h2 class="about-title">Kuhusu Jukwaa la Panda</h2>
                    <p class="about-text">
                        Jukwaa la kwanza la kidigitali kwa lugha ya Kiswahili lenye lengo la kuwasaidia wasichana kupata ujuzi na
                        rasilimali za kuanza na kuendesha biashara zao ili kunufaika uchumi wa kidigitali. Jukwaa hili linafahamika
                        kama Panda Digital likiwa na maana ya kupanda mbegu ya kujitegemea kiuchumi kwa kutumia majukwaa ya kidigitali.
                    </p>
                    <p class="about-text">
                        Hii ni njia inayowakusanya wasichana wote wajasiriamali na kuwapa nafasi ya kuchagua fursa mbalimbali zinazoendana
                        na mahitaji yao kama vile ufadhili, fursa na mafunzo. Jukwaa hili linatumia mfumo mseto wa uwasilishaji ili
                        kunufaisha wasichana wa makundi tofauti mijini na vijijini.
                    </p>
                    <a href="<?= app_url('about.php') ?>" class="btn btn-primary">
                        <i class="fas fa-info-circle me-2"></i>Fahamu Zaidi
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left">
                <div class="about-video">
                    <iframe src="https://www.youtube.com/embed/2D25MKcuE4s" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fursa za Panda Section -->
<section class="features-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Fursa za Panda</h2>
                <p class="section-subtitle">Jifunze, pata fursa, tangaza biashara yako, na uwasiliane na wataalamu</p>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($latestOpportunities)): ?>
                <?php foreach ($latestOpportunities as $index => $opportunity): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <div class="feature-block">
                            <?php if ($opportunity['image']): ?>
                                <div class="feature-image mb-3">
                                    <img src="<?= $fursaModel->getImageUrl($opportunity['image']) ?>"
                                        alt="<?= htmlspecialchars($opportunity['name']) ?>"
                                        class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <h3 class="feature-title"><?= htmlspecialchars($opportunity['name']) ?></h3>
                            <p class="feature-description"><?= $fursaModel->truncateText($opportunity['description']) ?></p>
                            <div class="opportunity-meta mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= htmlspecialchars($opportunity['date']) ?> <?= htmlspecialchars($opportunity['month']) ?>
                                </small>
                            </div>
                            <a href="<?= app_url('fursa-details.php?id=' . $opportunity['id']) ?>" class="btn btn-outline-primary">Soma Zaidi</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no opportunities found -->
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-block">
                        <div class="feature-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="feature-title">Jifunze</h3>
                        <p class="feature-description">Soma kozi mbalimbali kama vile usimamuzi wa biashara, usimamizi wa fedha na ufanyaji masoko zitakazosaidia kukuza ujuzi wako ili kujiajiri au kuajirika</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-block">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="feature-title">Fursa Za Panda</h3>
                        <p class="feature-description">Pata taarifa kuhusu fursa mbalimbali zinazopatikana kutoka kwetu, kwa washirika wetu na sekta nzima ya maendeleo.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-block">
                        <div class="feature-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3 class="feature-title">Tangaza Biashara</h3>
                        <p class="feature-description">Unaweza kutangaza biashara yako kupitia jukwaa la panda digital na kufikia walengwa wako kwa njia sahihi ikiwa inahusana na elimu</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-block">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="feature-title">Panda Chat</h3>
                        <p class="feature-description">Jukwaa maalumu kukuwezesha kuwasiliana na wataalamu na wazoefu kutoka sekta mbalimbali, kuuliza maswali na kupatiwa majibu ya kitaalamu kuhusu changamoto yako</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Habari Section -->
<section class="news-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Habari</h2>
                <p class="section-subtitle">Soma habari za hivi karibuni na ujuzi wa kidijitali</p>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($latestBlogPosts)): ?>
                <?php foreach ($latestBlogPosts as $index => $post): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <div class="news-card">
                            <?php if ($post['photo']): ?>
                                <div class="news-image" style="background-image: url('<?= $blogModel->getImageUrl($post['photo']) ?>');"></div>
                            <?php else: ?>
                                <div class="news-image" style="background-image: url('<?= asset('images/blog/post-1.jpg') ?>');"></div>
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i><?= $blogModel->formatDate($post['date_created']) ?></span>
                                    <span><i class="fas fa-user me-1"></i>Panda Digital</span>
                                </div>
                                <h4 class="news-title"><?= htmlspecialchars($post['title']) ?></h4>
                                <p class="news-excerpt"><?= $blogModel->truncateText($post['excerpt']) ?></p>
                                <a href="<?= app_url('habari-details.php?id=' . $post['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>Soma Zaidi
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no blog posts found -->
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="news-card">
                        <div class="news-image" style="background-image: url('<?= asset('images/blog/post-1.jpg') ?>');"></div>
                        <div class="news-content">
                            <div class="news-meta">
                                <span><i class="fas fa-calendar me-1"></i>15 Desemba, 2024</span>
                                <span><i class="fas fa-user me-1"></i>Panda Digital</span>
                            </div>
                            <h4 class="news-title">Jinsi ya Kuanza Biashara ya Kidijitali</h4>
                            <p class="news-excerpt">Jifunze hatua muhimu za kuanza biashara yako ya kidijitali na jinsi ya kufikia walengwa wako...</p>
                            <a href="<?= app_url('habari-details.php?id=1') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>Soma Zaidi
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="news-card">
                        <div class="news-image" style="background-image: url('<?= asset('images/blog/post-2.jpg') ?>');"></div>
                        <div class="news-content">
                            <div class="news-meta">
                                <span><i class="fas fa-calendar me-1"></i>12 Desemba, 2024</span>
                                <span><i class="fas fa-user me-1"></i>Panda Digital</span>
                            </div>
                            <h4 class="news-title">Fursa za Ufadhili kwa Wajasiriamali</h4>
                            <p class="news-excerpt">Tazama fursa mpya za ufadhili zinazopatikana kwa wajasiriamali wa kidijitali...</p>
                            <a href="<?= app_url('habari-details.php?id=2') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>Soma Zaidi
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="news-card">
                        <div class="news-image" style="background-image: url('<?= asset('images/blog/post-3.jpg') ?>');"></div>
                        <div class="news-content">
                            <div class="news-meta">
                                <span><i class="fas fa-calendar me-1"></i>10 Desemba, 2024</span>
                                <span><i class="fas fa-user me-1"></i>Panda Digital</span>
                            </div>
                            <h4 class="news-title">Ujuzi wa Masoko ya Mtandaoni</h4>
                            <p class="news-excerpt">Jifunze jinsi ya kufanya masoko ya mtandaoni na kufikia wateja wako...</p>
                            <a href="<?= app_url('habari-details.php?id=3') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>Soma Zaidi
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= app_url('habari.php') ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-newspaper me-2"></i>Tazama Habari Zote
            </a>
        </div>
    </div>
</section>

<!-- Washirika Wetu Section -->
<section class="partners-section">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h2 class="section-title">Washirika Wetu</h2>
                <p class="section-subtitle">Tunafanya kazi na washirika wa kusadikika katika sekta ya maendeleo</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="partner-logo">
                    <img src="<?= asset('images/Logo EKN high resolution (1) (1).jpg') ?>" alt="EKN Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="partner-logo">
                    <img src="<?= asset('images/roddenberry (1).png') ?>" alt="Roddenberry Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="partner-logo">
                    <img src="<?= asset('images/SFFlogolong.jpeg') ?>" alt="SFF Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="partner-logo">
                    <img src="<?= asset('images/Serengeti-Bytes-logo-1-ai-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.png') ?>" alt="Serengeti Bytes Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="partner-logo">
                    <img src="<?= asset('images/WFT-Trust-Logo-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.jpg') ?>" alt="WFT Trust Partner">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="partner-logo">
                    <img src="<?= asset('images/women-first-q46m1eri9z6ztzpd7mnkhscwf4hfmgddieelg3l534.png') ?>" alt="Women First Partner">
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

<style>
    /* Enhanced Hero Section Styles */
    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, var(--secondary-color, #5f4594) 0%, var(--secondary-dark, #4a3675) 100%);
        overflow: hidden;
        padding: 4rem 0;
    }

    .hero-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("<?= asset('images/backgrounds/hero-pattern.png') ?>") center/cover;
        opacity: 0.05;
        z-index: 1;
    }

    .hero-container {
        position: relative;
        z-index: 3;
        width: 100%;
    }

    .hero-content {
        color: white;
        padding: 2rem 0;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 188, 59, 0.2);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 2rem;
        border: 2px solid var(--primary-color, #ffbc3b);
        color: var(--primary-color, #ffbc3b);
        text-transform: uppercase;
        letter-spacing: 1px;
        backdrop-filter: blur(10px);
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: white;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 500;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
    }

    .hero-buttons {
        margin-bottom: 2rem;
    }

    .hero-buttons .btn {
        padding: 1rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .hero-buttons .btn-primary {
        background: var(--primary-color, #ffbc3b);
        border-color: var(--primary-color, #ffbc3b);
        color: #333;
    }

    .hero-buttons .btn-primary:hover {
        background: #e6a800;
        border-color: #e6a800;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .hero-buttons .btn-outline-primary {
        border: 2px solid var(--primary-color, #ffbc3b);
        color: var(--primary-color, #ffbc3b);
        background: transparent;
    }

    .hero-buttons .btn-outline-primary:hover {
        background: var(--primary-color, #ffbc3b);
        color: #333;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 188, 59, 0.3);
    }

    .hero-features {
        margin-top: 2rem;
    }

    .hero-feature {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .hero-feature i {
        font-size: 1.2rem;
        margin-right: 0.75rem;
        color: #28a745;
    }

    .hero-image {
        position: relative;
        text-align: center;
    }

    .hero-image img {
        max-width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .hero-image-overlay {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.95);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    .overlay-content h4 {
        color: var(--secondary-color, #5f4594);
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .overlay-content p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }

    .hero-scroll-indicator {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
    }

    .scroll-down {
        display: inline-block;
        color: white;
        font-size: 1.5rem;
        animation: bounce 2s infinite;
        text-decoration: none;
    }

    .scroll-down:hover {
        color: var(--primary-color, #ffbc3b);
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .hero-image {
            margin-top: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1.5rem;
        }

        .hero-feature {
            font-size: 1rem;
        }
    }

    /* Feature Cards Styles */
    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #ffbc3b), var(--secondary-color, #5f4594));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b), #e6a800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1);
        box-shadow: 0 10px 25px rgba(255, 188, 59, 0.4);
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .feature-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    /* Section Titles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--secondary-color, #5f4594);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #666;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
</style>