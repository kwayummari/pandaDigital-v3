<?php
require_once 'config/init.php';
require_once 'models/Expert.php';

// Initialize the Expert model
$expertModel = new Expert();

// Get all experts
$experts = $expertModel->getAllExperts();

// Get expert statistics
$expertStats = $expertModel->getExpertStats();

// Handle search
$searchQuery = $_GET['search'] ?? '';
$filteredExperts = $experts;

if (!empty($searchQuery)) {
    $filteredExperts = $expertModel->searchExperts($searchQuery);
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uliza Swali kwa Mtaalamu - Panda Digital</title>
    <meta name="description" content="Uliza maswali kwa wataalamu wetu wa ujasiriamali. Pata msaada na mwongozo kuhusu biashara yako.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="page-title">Uliza Swali kwa Mtaalamu</h1>
                    <p class="page-subtitle">Pata msaada na mwongozo kutoka kwa wataalamu wetu wa ujasiriamali</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li class="breadcrumb-item active">Wataalamu</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <div class="header-icon">
                        <div class="star-icon text-white" style="font-size: 4rem; font-weight: bold;">★</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="search-card" data-aos="fade-up">
                        <h3 class="text-center mb-4">Tafuta Mtaalamu</h3>
                        <form method="GET" action="" class="search-form">
                            <div class="input-group">
                                <input type="text"
                                    class="form-control form-control-lg"
                                    name="search"
                                    placeholder="Tafuta kwa jina, biashara, au mkoa..."
                                    value="<?= htmlspecialchars($searchQuery) ?>">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <span class="me-2"></span>Tafuta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <h3 class="stat-number text-primary"><?= $expertStats['total'] ?></h3>
                        <p class="stat-label">Wataalamu Wote</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <h3 class="stat-number text-success"><?= count(array_filter($experts, function ($expert) use ($expertModel) {
                                                                    return $expertModel->getExpertStatus($expert['id']) === 'free';
                                                                })) ?></h3>
                        <p class="stat-label">Wataalamu wa Bure</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <h3 class="stat-number text-warning"><?= count(array_filter($experts, function ($expert) use ($expertModel) {
                                                                    return $expertModel->getExpertStatus($expert['id']) === 'premium';
                                                                })) ?></h3>
                        <p class="stat-label">Wataalamu wa Premium</p>
                    </div>
                </div>
            </div>

            <?php if (!empty($expertStats['byRegion'])): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h4 class="text-center mb-4">Wataalamu kwa Mkoa</h4>
                        <div class="row">
                            <?php foreach (array_slice($expertStats['byRegion'], 0, 3) as $region): ?>
                                <div class="col-md-4 mb-3" data-aos="fade-up">
                                    <div class="region-stat">
                                        <h5 class="text-primary"><?= htmlspecialchars($region['region']) ?></h5>
                                        <p class="text-muted"><?= $region['count'] ?> Wataalamu</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Experts Section -->
    <section class="experts-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center section-title justify-content-between mb-5">
                        <h2 class="mb-0">Wataalamu Wetu</h2>
                        <div class="border-top flex-grow-1 mx-3 d-none d-md-block"></div>
                        <div class="text-end">
                            <span class="badge bg-primary fs-6"><?= count($filteredExperts) ?> Wataalamu</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($filteredExperts)): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="no-results" data-aos="fade-up">
                            <h4 class="text-muted mb-3">Hakuna Wataalamu Walioonekana</h4>
                            <p class="text-muted">Hakuna wataalamu walioendana na utafutaji wako. Jaribu kutafuta kwa njia nyingine.</p>
                            <a href="<?= app_url('uliza-swali.php') ?>" class="btn btn-outline-primary">Ona Wataalamu Wote</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($filteredExperts as $expert): ?>
                        <div class="col-lg-4 col-md-6 mb-5" data-aos="fade-up">
                            <div class="expert-card">
                                <div class="expert-image-container">
                                    <img src="<?= $expertModel->getExpertImageUrl($expert['profile_photo']) ?>"
                                        alt="<?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?>"
                                        class="expert-image">
                                    <div class="expert-status <?= $expertModel->getExpertStatus($expert['id']) ?>">
                                        <?= ucfirst($expertModel->getExpertStatus($expert['id'])) ?>
                                    </div>
                                </div>
                                <div class="expert-info">
                                    <h4 class="expert-name"><?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?></h4>
                                    <?php if (!empty($expert['business'])): ?>
                                        <p class="expert-business"><?= htmlspecialchars($expert['business']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($expert['region'])): ?>
                                        <p class="expert-region"><?= htmlspecialchars($expert['region']) ?></p>
                                    <?php endif; ?>
                                    <div class="expert-actions">
                                        <a href="<?= app_url('expert-details.php?id=' . $expert['id']) ?>" class="btn btn-primary btn-sm">
                                            <span class="me-2"></span>Ongea na <?= htmlspecialchars($expert['first_name']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Information Section -->
    <section class="info-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8" data-aos="fade-right">
                    <h3 class="mb-4">Kwa Nini Uliza Swali kwa Mtaalamu?</h3>
                    <p class="mb-4">
                        Panda Chat ni kipengele kipya kilichopo kwenye jukwaa la Panda Digital, kinachowakutanisha wajasiriamali wasichana na wataalamu mbalimbali ili kukabiliana na changamoto wanazokabiliana nazo katika biashara zao.
                    </p>
                    <p class="mb-4">
                        Wazo la kuanzisha lilitokana na ukweli kwamba kuanzisha biashara ni jambo moja, lakini kuendesha biashara inaweza kuwa changamoto tofauti kabisa. Kipengele hiki kipya kwenye jukwaa la Panda Digital kinawaleta pamoja wajasiriamali vijana wanawake na wataalamu mbalimbali ili kutatua changamoto wanazokabiliana nazo katika uendeshaji wa biashara zao.
                    </p>
                    <div class="features-list">
                        <div class="feature-item">
                            <span class="text-success me-2"></span>
                            <span>Usiri kamili wa maswali yako</span>
                        </div>
                        <div class="feature-item">
                            <span class="text-info me-2"></span>
                            <span>Majibu ya haraka na sahihi</span>
                        </div>
                        <div class="feature-item">
                            <span class="text-warning me-2"></span>
                            <span>Msaada wa bure na wa kulipwa</span>
                        </div>
                        <div class="feature-item">
                            <span class="text-danger me-2"></span>
                            <span>Ushirikiano na wataalamu wenye uzoefu</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="contact-card">
                        <h4 class="mb-3">Wasiliana Nasi</h4>
                        <div class="contact-item">
                            <span class="me-2 text-primary"></span>
                            <a href="tel:+25573428334" class="text-decoration-none">+255 734 283 34</a>
                        </div>
                        <div class="contact-item">
                            <span class="me-2 text-primary"></span>
                            <a href="mailto:info@pandadigital.co.tz" class="text-decoration-none">info@pandadigital.co.tz</a>
                        </div>
                        <div class="contact-item">
                            <span class="me-2 text-primary"></span>
                            <span>Dar Es Salaam, Tanzania</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section py-5 text-center text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="mb-4">Jiunge na Panda Chat Leo</h3>
                    <p class="mb-4">Ufungue fursa nyingi za kuongeza ujuzi wako wa biashara na kupata suluhisho kwa changamoto zako za ujasiriamali.</p>
                    <div class="cta-buttons">
                        <a href="<?= app_url('register.php') ?>" class="btn btn-light btn-lg me-3">
                            <span class="me-2"></span>Jisajili Sasa
                        </a>
                        <a href="<?= app_url('login.php') ?>" class="btn btn-outline-light btn-lg">
                            <span class="me-2"></span>Ingia
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Additional Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            }

            console.log('Uliza Swali page JavaScript loaded successfully');
        });
    </script>

    <style>
        /* Page header styles */
        .page-header {
            padding: 120px 0 80px;
            color: white;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .header-icon {
            opacity: 0.8;
        }

        /* Breadcrumb styles */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Search section styles */
        .search-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .search-form .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px 0 0 10px;
        }

        .search-form .btn {
            border-radius: 0 10px 10px 0;
            padding: 0.75rem 1.5rem;
        }

        /* Statistics styles */
        .stat-card {
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #6c757d;
            margin: 0;
        }

        /* Expert card styles */
        .expert-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .expert-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .expert-image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .expert-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .expert-card:hover .expert-image {
            transform: scale(1.1);
        }

        .expert-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .expert-status.free {
            background: #28a745;
            color: white;
        }

        .expert-status.premium {
            background: #ffc107;
            color: #212529;
        }

        .expert-info {
            padding: 1.5rem;
        }

        .expert-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .expert-business {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .expert-region {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .expert-actions {
            text-align: center;
        }

        /* Section title styles */
        .section-title {
            position: relative;
        }

        .section-title h2 {
            color: #333;
            font-weight: 700;
        }

        /* Information section styles */
        .info-section {
            background: #f8f9fa;
        }

        .features-list {
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .contact-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .contact-item a {
            color: #333;
            text-decoration: none;
        }

        .contact-item a:hover {
            color: #007bff;
        }

        /* CTA section styles */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .cta-buttons .btn {
            padding: 0.75rem 2rem;
            font-weight: 600;
        }

        /* No results styles */
        .no-results {
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Statistics styles */
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0;
        }

        .region-stat {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .region-stat h5 {
            margin-bottom: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .search-card {
                padding: 1.5rem;
            }

            .expert-card {
                margin-bottom: 2rem;
            }

            .cta-buttons .btn {
                display: block;
                width: 100%;
                margin-bottom: 1rem;
            }
        }
    </style>