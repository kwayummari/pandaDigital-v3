<?php
require_once 'config/init.php';
include 'includes/header.php';
?>

<!-- Hero Section - Modern Split Design -->
<section class="hero-section">
    <div class="hero-container">
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