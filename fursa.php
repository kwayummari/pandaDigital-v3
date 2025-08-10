<?php
require_once 'config/init.php';
require_once 'models/Fursa.php';

// Initialize the Fursa model
$fursaModel = new Fursa();

// Get opportunity categories and opportunities from database
$opportunityCategories = [
    [
        'id' => 1,
        'name' => 'Ajira',
        'icon' => 'fas fa-briefcase',
        'description' => 'Fursa za ajira na kazi za muda mfupi',
        'color' => 'primary',
        'count' => 45
    ],
    [
        'id' => 2,
        'name' => 'Ufadhili',
        'icon' => 'fas fa-hand-holding-usd',
        'description' => 'Mikopo, ufadhili na tuzo za biashara',
        'color' => 'success',
        'count' => 32
    ],
    [
        'id' => 3,
        'name' => 'Mafunzo',
        'icon' => 'fas fa-graduation-cap',
        'description' => 'Mafunzo ya bure na ya bei nafuu',
        'color' => 'info',
        'count' => 28
    ],
    [
        'id' => 4,
        'name' => 'Biashara',
        'icon' => 'fas fa-store',
        'description' => 'Fursa za biashara na ujasiriamali',
        'color' => 'warning',
        'count' => 19
    ]
];

// Get featured opportunities from database
$featuredOpportunities = $fursaModel->getLatestOpportunities(8);

$pageTitle = 'Fursa Zote - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="page-title">Fursa Zote</h1>
                <p class="page-subtitle">Tazama fursa bora za ajira, ufadhili, mafunzo na biashara zinazokufaa</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Fursa</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <i class="fas fa-lightbulb fa-4x text-white"></i>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Featured Opportunities -->
<section class="featured-opportunities-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Fursa Zilizochaguliwa</h2>
                <p class="section-subtitle">Fursa bora zaidi zilizochaguliwa na wataalamu wetu</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredOpportunities)): ?>
                <?php foreach ($featuredOpportunities as $index => $opportunity): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="opportunity-card h-100">
                            <div class="opportunity-image">
                                <?php if (!empty($opportunity['image'])): ?>
                                    <img src="<?= $fursaModel->getImageUrl($opportunity['image']) ?>" alt="<?= htmlspecialchars($opportunity['name']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="<?= asset('images/opportunities/default-opportunity.jpg') ?>" alt="Default Opportunity Image" class="img-fluid">
                                <?php endif; ?>
                                <div class="opportunity-overlay">
                                    <div class="opportunity-actions">
                                        <a href="fursa-details.php?id=<?= $opportunity['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>Tazama Zaidi
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="opportunity-content p-4">
                                <div class="opportunity-meta mb-2">
                                    <span class="badge bg-primary me-2">Fursa</span>
                                    <span class="badge bg-secondary"><?= $fursaModel->formatDate($opportunity['date_created']) ?></span>
                                </div>
                                <h5 class="opportunity-title mb-3">
                                    <a href="fursa-details.php?id=<?= $opportunity['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($opportunity['name']) ?></a>
                                </h5>
                                <p class="opportunity-description text-muted mb-3"><?= $fursaModel->truncateText($opportunity['description']) ?></p>

                                <div class="opportunity-footer d-flex justify-content-between align-items-center">
                                    <div class="opportunity-date">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?= $fursaModel->formatDate($opportunity['date_created']) ?>
                                        </small>
                                    </div>
                                    <div class="opportunity-apply">
                                        <a href="fursa-details.php?id=<?= $opportunity['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-paper-plane me-1"></i>Omba Sasa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Hakuna fursa zilizopatikana kwa sasa.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center" data-aos="fade-up">
                <a href="#" class="btn btn-primary btn-lg">
                    <i class="fas fa-th-list me-2"></i>Tazama Fursa Zote
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How to Apply Section -->
<section class="how-to-apply-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Jinsi Ya Kuomba Fursa</h2>
                <p class="section-subtitle">Fuata hatua hizi rahisi kuomba fursa yoyote</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">1</span>
                    </div>
                    <h5 class="step-title mb-3">Tafuta Fursa</h5>
                    <p class="step-description text-muted">Tafuta fursa inayokufaa zaidi kutoka kwenye orodha yetu</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">2</span>
                    </div>
                    <h5 class="step-title mb-3">Soma Maelezo</h5>
                    <p class="step-description text-muted">Soma maelezo kamili ya fursa na mahitaji yote</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">3</span>
                    </div>
                    <h5 class="step-title mb-3">Andaa Maombi</h5>
                    <p class="step-description text-muted">Andaa maombi yako kwa makini na usahihi</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="step-number mb-3">
                    <span class="badge bg-primary fs-4">4</span>
                </div>
                <h5 class="step-title mb-3">Tuma Maombi</h5>
                <p class="step-description text-muted">Tuma maombi yako na subiri majibu</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="cta-title">Je, Una Fursa Unayotaka Kushiriki?</h2>
                <p class="cta-subtitle">Tunaweza kukusaidia kushiriki fursa yako na wanawake wengine wanaohitaji</p>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <a href="#" class="btn btn-light btn-lg">
                    <i class="fas fa-plus me-2"></i>Ongeza Fursa
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    /* Opportunity card specific styles */
    .opportunity-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e2e8f0;
    }

    .opportunity-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .opportunity-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
    }

    .opportunity-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .opportunity-card:hover .opportunity-image img {
        transform: scale(1.05);
    }

    .opportunity-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: var(--transition, all 0.3s ease);
    }

    .opportunity-card:hover .opportunity-overlay {
        opacity: 1;
    }

    .opportunity-content {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .opportunity-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .opportunity-description {
        flex: 1;
        margin-bottom: 1rem;
        color: #64748b;
    }

    /* Link styling - remove underlines */
    .opportunity-card a {
        text-decoration: none;
        color: inherit;
        transition: color 0.3s ease;
    }

    .opportunity-card a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .opportunity-title a {
        color: #1e293b;
        text-decoration: none;
    }

    .opportunity-title a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

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

    /* Section title styles */
    .section-title {
        margin-bottom: 3rem;
    }

    .section-title h2 {
        color: #333;
        font-weight: 600;
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
</style>