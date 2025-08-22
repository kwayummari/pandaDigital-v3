<?php
require_once 'config/init.php';
require_once 'services/AuthService.php';
require_once 'services/GoogleOAuthService.php';

// Initialize authentication service
$authService = new AuthService();
$isLoggedIn = $authService->isLoggedIn();
$currentUser = $isLoggedIn ? $authService->getCurrentUser() : null;

// Set page title
$pageTitle = "Kuhusu Sisi - " . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Jifunze zaidi kuhusu Panda Digital - Jukwaa la kwanza la kidigitali kwa lugha ya Kiswahili lenye lengo la kuwasaidia wasichana kupata ujuzi na rasilimali za kuanza na kuendesha biashara zao.">

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
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-3">Kuhusu Sisi</h1>
                    <p class="lead mb-0">Jifunze zaidi kuhusu Panda Digital na jinsi tunavyowasaidia wasichana kunufaika uchumi wa kidigitali</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="about-content-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right">
                    <div class="about-content">
                        <h2 class="about-title h3 mb-4">Kuhusu Jukwaa la Panda</h2>
                        <p class="about-text mb-4">
                            Jukwaa la kwanza la kidigitali kwa lugha ya Kiswahili lenye lengo la kuwasaidia wasichana kupata ujuzi na
                            rasilimali za kuanza na kuendesha biashara zao ili kunufaika uchumi wa kidigitali. Jukwaa hili linafahamika
                            kama Panda Digital likiwa na maana ya kupanda mbegu ya kujitegemea kiuchumi kwa kutumia majukwaa ya kidigitali.
                        </p>
                        <p class="about-text mb-4">
                            Hii ni njia inayowakusanya wasichana wote wajasiriamali na kuwapa nafasi ya kuchagua fursa mbalimbali zinazoendana
                            na mahitaji yao kama vile ufadhili, fursa na mafunzo. Jukwaa hili linatumia mfumo mseto wa uwasilishaji ili
                            kunufaisha wasichana wa makundi tofauti mijini na vijijini.
                        </p>

                        <div class="about-features mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-3 fa-lg"></i>
                                        <span>Masomo ya kidigitali kwa Kiswahili</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-3 fa-lg"></i>
                                        <span>Kozi za bure na za kulipia</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-3 fa-lg"></i>
                                        <span>Msaada kupitia Panda Chat</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-3 fa-lg"></i>
                                        <span>Jifunze kwa SMS bila intaneti</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left">
                    <div class="about-video">
                        <div class="video-container position-relative rounded overflow-hidden shadow-lg">
                            <iframe src="https://www.youtube.com/embed/2D25MKcuE4s"
                                frameborder="0"
                                allowfullscreen
                                class="w-100"
                                style="height: 350px;">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="mission-vision-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mission-icon mb-3">
                                <i class="fas fa-bullseye fa-3x text-primary"></i>
                            </div>
                            <h3 class="card-title h4 mb-3">Lengo Letu</h3>
                            <p class="card-text">
                                Kuwa jukwaa la kwanza la kidigitali kwa lugha ya Kiswahili lenye lengo la kuwasaidia wasichana
                                kupata ujuzi na rasilimali za kuanza na kuendesha biashara zao ili kunufaika uchumi wa kidigitali.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="vision-icon mb-3">
                                <i class="fas fa-eye fa-3x text-primary"></i>
                            </div>
                            <h3 class="card-title h4 mb-3">Mtazamo Wetu</h3>
                            <p class="card-text">
                                Kuwa na jamii ya wasichana wajasiriamali wenye ujuzi wa kidigitali na uwezo wa kujitegemea kiuchumi
                                kupitia teknolojia na majukwaa ya kidigitali.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">Thamani Zetu</h2>
                    <p class="section-subtitle">Thamani zinazotufanya kuwa tofauti na kuwa na imani yetu</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <h4 class="value-title h5">Umoja</h4>
                        <p class="value-description">Tunaamini katika umoja na ushirikiano wa wasichana wote</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                        <h4 class="value-title h5">Mafunzo</h4>
                        <p class="value-description">Tunaamini katika nguvu ya elimu na ujuzi wa kidigitali</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-handshake fa-2x text-primary"></i>
                        </div>
                        <h4 class="value-title h5">Uaminifu</h4>
                        <p class="value-description">Tunaamini katika uaminifu na uwazi katika kila kitu tunachofanya</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card text-center">
                        <div class="value-icon mb-3">
                            <i class="fas fa-rocket fa-2x text-primary"></i>
                        </div>
                        <h4 class="value-title h5">Uwezo</h4>
                        <p class="value-description">Tunaamini katika kuwawezesha wasichana kuwa na uwezo wa kujitegemea</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-8 mx-auto">
                    <h2 class="cta-title h3 mb-4">Jiunge Nasi Leo</h2>
                    <p class="cta-description mb-4">
                        Unakaribishwa kujiunga na jukwaa letu na kuanza safari yako ya kujitegemea kiuchumi kupitia teknolojia ya kidigitali.
                    </p>
                    <div class="cta-buttons">
                        <a href="#" class="btn btn-light btn-lg me-3" data-bs-toggle="modal" data-bs-target="#signupModal">
                            <i class="fas fa-user-plus me-2"></i>Jisajili Sasa
                        </a>
                        <a href="<?= app_url('kozi.php') ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-graduation-cap me-2"></i>Tazama Kozi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JS -->
    <script src="<?= asset('js/script.js') ?>"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>

    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        }

        .about-title {
            color: var(--primary-color, #ffbc3b);
            font-weight: 600;
        }

        .about-text {
            color: #6c757d;
            line-height: 1.8;
        }

        .about-features .fas {
            color: var(--primary-color, #ffbc3b);
        }

        .video-container {
            border-radius: 15px;
            overflow: hidden;
        }

        .mission-vision-section .card {
            transition: transform 0.3s ease;
            border-radius: 15px;
        }

        .mission-vision-section .card:hover {
            transform: translateY(-5px);
        }

        .value-card {
            padding: 2rem 1rem;
            transition: transform 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }

        .value-icon {
            color: var(--primary-color, #ffbc3b);
        }

        .value-title {
            color: var(--primary-color, #ffbc3b);
            font-weight: 600;
        }

        .value-description {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        }

        .cta-buttons .btn {
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .cta-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</body>

</html>