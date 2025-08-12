<?php
require_once 'config/init.php';
require_once 'models/Wanufaika.php';

// Initialize the Wanufaika model
$wanufaikaModel = new Wanufaika();

// Get the wanufaika ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('wanufaika.php');
}

// Get wanufaika details
$wanufaika = $wanufaikaModel->getWanufaikaById($id);

if (!$wanufaika) {
    redirect('wanufaika.php');
}

// Get related wanufaika stories (excluding current one)
$relatedWanufaika = $wanufaikaModel->getLatestWanufaika(3, $id);

// Set page title
$pageTitle = htmlspecialchars($wanufaika['title']) . ' - ' . env('APP_NAME');
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header Section -->
    <section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_url('wanufaika.php') ?>">Wanufaika</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($wanufaika['title']) ?></li>
                        </ol>
                    </nav>
                    <h1 class="page-title"><?= htmlspecialchars($wanufaika['title']) ?></h1>
                    <p class="page-subtitle">Hadithi ya mafanikio ya <?= htmlspecialchars($wanufaika['name']) ?> kupitia jukwaa la Panda Digital</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Wanufaika Details Section -->
    <section id="wanufaika-details" class="wanufaika-details-section py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="wanufaika-main" data-aos="fade-up">
                        <!-- Wanufaika Image -->
                        <div class="wanufaika-image mb-4">
                            <?php if (!empty($wanufaika['photo'])): ?>
                                <img src="<?= $wanufaikaModel->getImageUrl($wanufaika['photo']) ?>"
                                    alt="<?= htmlspecialchars($wanufaika['title']) ?>"
                                    class="img-fluid rounded shadow">
                            <?php else: ?>
                                <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                    alt="Default Wanufaika Image"
                                    class="img-fluid rounded shadow">
                            <?php endif; ?>
                        </div>

                        <!-- Wanufaika Content -->
                        <div class="wanufaika-content">
                            <!-- Meta Information -->
                            <div class="wanufaika-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <span>Iliundwa: <?= $wanufaikaModel->formatDate($wanufaika['date_created']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <span>Mwanufaika: <?= htmlspecialchars($wanufaika['name']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="wanufaika-description mb-4">
                                <h3 class="section-subtitle">Maelezo</h3>
                                <div class="description-content">
                                    <?= $wanufaika['description'] ?>
                                </div>
                            </div>

                            <!-- Success Story -->
                            <div class="success-story mb-4">
                                <h3 class="section-subtitle">Hadithi ya Mafanikio</h3>
                                <div class="story-content">
                                    <p class="lead">
                                        <?= htmlspecialchars($wanufaika['name']) ?> alifanikiwa kupitia jukwaa la Panda Digital
                                        na sasa ana biashara yake ya kidijitali inayofanya vizuri.
                                    </p>
                                    <p>
                                        Kupitia kozi na mafunzo aliyopata, <?= htmlspecialchars($wanufaika['name']) ?>
                                        alijifunza ujuzi muhimu wa kidijitali na sasa anaweza kujitegemea kiuchumi.
                                    </p>
                                </div>
                            </div>

                            <!-- Call to Action -->
                            <div class="cta-section text-center py-4 bg-light rounded">
                                <h4 class="mb-3">Tayari Kuanza Safari Yako?</h4>
                                <p class="mb-4">Jiunge na jukwaa letu na uanze safari yako ya mafanikio</p>
                                <a href="<?= app_url('kozi.php') ?>" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-graduation-cap me-2"></i>Anza Kozi
                                </a>
                                <a href="<?= app_url('fursa.php') ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-lightbulb me-2"></i>Tazama Fursa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="wanufaika-sidebar" data-aos="fade-left">
                        <!-- Quick Info Card -->
                        <div class="sidebar-card mb-4">
                            <div class="sidebar-card-body">
                                <h5 class="sidebar-card-title">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Taarifa za Haraka
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <strong>Kategoria:</strong>
                                        <span class="text-muted">Mafanikio</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Mahali:</strong>
                                        <span class="text-muted">Tanzania</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Muda:</strong>
                                        <span class="text-muted"><?= $wanufaikaModel->formatDate($wanufaika['date_created']) ?></span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Uzoefu:</strong>
                                        <span class="text-muted">Yoyote</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="sidebar-card mb-4">
                            <div class="sidebar-card-body">
                                <h5 class="sidebar-card-title">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    Wasiliana Nasi
                                </h5>
                                <p class="text-muted">Kwa maswali zaidi kuhusu hadithi hii:</p>
                                <div class="contact-info">
                                    <div class="contact-item mb-2">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <a href="tel:<?= env('CONTACT_PHONE') ?>"><?= env('CONTACT_PHONE') ?></a>
                                    </div>
                                    <div class="contact-item mb-2">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <a href="mailto:<?= env('CONTACT_EMAIL') ?>"><?= env('CONTACT_EMAIL') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Success Stories -->
                        <?php if (!empty($relatedWanufaika)): ?>
                            <div class="sidebar-card">
                                <div class="sidebar-card-body">
                                    <h5 class="sidebar-card-title">
                                        <i class="fas fa-star text-primary me-2"></i>
                                        Hadithi Zingine za Mafanikio
                                    </h5>
                                    <div class="related-opportunities">
                                        <?php foreach ($relatedWanufaika as $related): ?>
                                            <div class="related-item mb-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <?php if (!empty($related['photo'])): ?>
                                                            <img src="<?= $wanufaikaModel->getImageUrl($related['photo']) ?>"
                                                                alt="<?= htmlspecialchars($related['title']) ?>"
                                                                class="img-fluid rounded">
                                                        <?php else: ?>
                                                            <img src="<?= upload_url('Wanufaika/1.jpeg') ?>"
                                                                alt="Default Image"
                                                                class="img-fluid rounded">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-8">
                                                        <h6 class="related-title">
                                                            <a href="<?= app_url('wanufaika-details.php?id=' . $related['id']) ?>">
                                                                <?= htmlspecialchars($related['title']) ?>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <?= $wanufaikaModel->formatDate($related['date_created']) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom Scripts -->
    <script src="<?= asset('js/script.js') ?>"></script>

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

    .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Wanufaika details styles */
    .wanufaika-image img {
        width: 100%;
        height: auto;
        max-height: none;
        object-fit: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .section-subtitle {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color, #ffbc3b);
    }

    .meta-item i {
        font-size: 1.2rem;
    }

    .description-content {
        line-height: 1.8;
        color: #555;
    }

    .story-content {
        line-height: 1.8;
        color: #555;
    }

    .story-content .lead {
        font-size: 1.3rem;
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 1.5rem;
    }

    /* Sidebar styles */
    .sidebar-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .sidebar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .sidebar-card-body {
        padding: 2rem;
    }

    .sidebar-card-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .contact-item a {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-item a:hover {
        color: #e6a800;
        text-decoration: none;
    }

    .contact-info .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .contact-info .contact-item i {
        margin-right: 0.5rem;
    }

    .contact-info .contact-item a {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-info .contact-item a:hover {
        color: #e6a800;
        text-decoration: none;
    }

    /* Related wanufaika styles */
    .related-item {
        padding: 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .related-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .related-title a {
        color: #1e293b;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .related-title a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .related-item img {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* CTA section styles */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        color: white;
        padding: 80px 0;
    }

    .cta-title {
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }

        .page-subtitle {
            font-size: 1rem;
        }

        .sidebar-card {
            margin-bottom: 2rem;
        }
    }
</style>