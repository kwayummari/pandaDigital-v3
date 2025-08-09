<?php
require_once 'config/init.php';
require_once 'models/Fursa.php';

// Get opportunity ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Debug: Check if ID is being received
error_log("Fursa Details - ID received: " . $id);

if (!$id) {
    error_log("Fursa Details - No ID provided, redirecting to index");
    redirect('index.php');
}

// Initialize Fursa model
$fursaModel = new Fursa();
$opportunity = $fursaModel->getOpportunityById($id);

// Debug: Check what opportunity data is retrieved
error_log("Fursa Details - Opportunity data: " . print_r($opportunity, true));

if (!$opportunity) {
    error_log("Fursa Details - No opportunity found for ID: " . $id . ", redirecting to index");
    redirect('index.php');
}

// Get related opportunities
$relatedOpportunities = $fursaModel->getLatestOpportunities(3, $id);

// Set page title
$pageTitle = htmlspecialchars($opportunity['name']) . ' - ' . env('APP_NAME');
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
    <section class="page-header-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_url('index.php#fursa') ?>">Fursa</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($opportunity['name']) ?></li>
                        </ol>
                    </nav>
                    <h1 class="page-title"><?= htmlspecialchars($opportunity['name']) ?></h1>
                    <p class="page-subtitle">Fursa ya kujifunza na kuendeleza ujuzi wako wa kidijitali</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Opportunity Details Section -->
    <section class="opportunity-details-section py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="opportunity-main" data-aos="fade-up">
                        <!-- Opportunity Image -->
                        <div class="opportunity-image mb-4">
                            <img src="<?= $fursaModel->getImageUrl($opportunity['image']) ?>"
                                alt="<?= htmlspecialchars($opportunity['name']) ?>"
                                class="img-fluid rounded shadow">
                        </div>

                        <!-- Opportunity Content -->
                        <div class="opportunity-content">
                            <!-- Meta Information -->
                            <div class="opportunity-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <span>Iliundwa: <?= $fursaModel->formatDate($opportunity['date_created']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <span>Muda: <?= htmlspecialchars($opportunity['muda'] ?? 'Haijulikani') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="opportunity-description mb-4">
                                <h3 class="section-subtitle">Maelezo</h3>
                                <div class="description-content">
                                    <?= $opportunity['description'] ?>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="opportunity-requirements mb-4">
                                <h3 class="section-subtitle">Mahitaji</h3>
                                <div class="requirements-content">
                                    <p class="text-muted">Mahitaji ya fursa hii yataonekana hapa baada ya kujazwa na mwenyeji.</p>
                                </div>
                            </div>

                            <!-- Benefits -->
                            <div class="opportunity-benefits mb-4">
                                <h3 class="section-subtitle">Faida</h3>
                                <div class="benefits-content">
                                    <p class="text-muted">Faida za fursa hii zitaonekana hapa baada ya kujazwa na mwenyeji.</p>
                                </div>
                            </div>

                            <!-- Application Buttons -->
                            <div class="opportunity-actions mt-5">
                                <a href="#" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-paper-plane me-2"></i>Omba Sasa
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-share me-2"></i>Shiriki
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="opportunity-sidebar" data-aos="fade-left">
                        <!-- Quick Info Card -->
                        <div class="card sidebar-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Taarifa za Haraka
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <strong>Kategoria:</strong>
                                        <span class="text-muted">Itaonekana baada ya kujazwa</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Mahali:</strong>
                                        <span class="text-muted">Tanzania</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Muda:</strong>
                                        <span class="text-muted">Itaonekana baada ya kujazwa</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Uzoefu:</strong>
                                        <span class="text-muted">Yoyote</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="card sidebar-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    Wasiliana Nasi
                                </h5>
                                <p class="text-muted">Kwa maswali zaidi kuhusu fursa hii:</p>
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

                        <!-- Related Opportunities -->
                        <?php if (!empty($relatedOpportunities)): ?>
                            <div class="card sidebar-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-lightbulb text-primary me-2"></i>
                                        Fursa Zingine
                                    </h5>
                                    <div class="related-opportunities">
                                        <?php foreach ($relatedOpportunities as $related): ?>
                                            <div class="related-item mb-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <img src="<?= $fursaModel->getImageUrl($related['image']) ?>"
                                                            alt="<?= htmlspecialchars($related['name']) ?>"
                                                            class="img-fluid rounded">
                                                    </div>
                                                    <div class="col-8">
                                                        <h6 class="related-title">
                                                            <a href="<?= app_url('fursa-details.php?id=' . $related['id']) ?>">
                                                                <?= htmlspecialchars($related['name']) ?>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <?= $fursaModel->formatDate($related['date_created']) ?>
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

    <!-- Scripts -->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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