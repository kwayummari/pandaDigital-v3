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

include 'includes/header.php';
?>

<!-- Page Header Section -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('fursa.php') ?>">Fursa</a></li>
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
                        <?php if (!empty($opportunity['image'])): ?>
                            <img src="<?= $fursaModel->getImageUrl($opportunity['image']) ?>"
                                alt="<?= htmlspecialchars($opportunity['name']) ?>"
                                class="img-fluid rounded shadow">
                        <?php else: ?>
                            <img src="<?= asset('images/opportunities/default-opportunity.jpg') ?>"
                                alt="Default Opportunity Image"
                                class="img-fluid rounded shadow">
                        <?php endif; ?>
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
                    <div class="sidebar-card mb-4">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title">
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
                    <div class="sidebar-card mb-4">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title">
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
                        <div class="sidebar-card">
                            <div class="sidebar-card-body">
                                <h5 class="sidebar-card-title">
                                    <i class="fas fa-lightbulb text-primary me-2"></i>
                                    Fursa Zingine
                                </h5>
                                <div class="related-opportunities">
                                    <?php foreach ($relatedOpportunities as $related): ?>
                                        <div class="related-item mb-3">
                                            <div class="row">
                                                <div class="col-4">
                                                    <?php if (!empty($related['image'])): ?>
                                                        <img src="<?= $fursaModel->getImageUrl($related['image']) ?>"
                                                            alt="<?= htmlspecialchars($related['name']) ?>"
                                                            class="img-fluid rounded">
                                                    <?php else: ?>
                                                        <img src="<?= asset('images/opportunities/default-opportunity.jpg') ?>"
                                                            alt="Default Opportunity Image"
                                                            class="img-fluid rounded">
                                                    <?php endif; ?>
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

<?php include 'includes/footer.php'; ?>

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

    /* Opportunity details styles */
    .opportunity-image img {
        width: 100%;
        height: auto;
        max-height: none;
        object-fit: contain;
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

    .requirements-content,
    .benefits-content {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color, #ffbc3b);
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

    /* Related opportunities styles */
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