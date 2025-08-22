<?php
require_once 'config/init.php';
require_once 'models/Wanufaika.php';

// Initialize the Wanufaika model
$wanufaikaModel = new Wanufaika();

// Get featured wanufaika from database
$featuredWanufaika = $wanufaikaModel->getLatestWanufaika(8);

// Debug: Check what we're getting from database
error_log("Debug: Featured wanufaika count: " . count($featuredWanufaika));
if (!empty($featuredWanufaika)) {
    error_log("Debug: First wanufaika data: " . print_r($featuredWanufaika[0], true));
}

$pageTitle = 'Wanufaika - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Wanufaika</h1>
                <p class="page-subtitle">Tazama hadithi za mafanikio ya watu waliofanikiwa kupitia jukwaa letu la Panda Digital</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Wanufaika</li>
                    </ol>
                </nav>
            </div>
            <!-- <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <div class="star-icon text-white" style="font-size: 4rem; font-weight: bold;">★</div>
                </div>
            </div> -->
        </div>
    </div>
</section>

<!-- Featured Wanufaika Section -->
<section class="featured-wanufaika-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Hadithi za Mafanikio</h2>
                <p class="section-subtitle">Wanufaika wetu waliofanikiwa kupitia jukwaa letu la Panda Digital</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredWanufaika)): ?>
                <?php foreach ($featuredWanufaika as $index => $wanufaika): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="wanufaika-card h-100">
                            <div class="wanufaika-image">
                                <?php
                                // Debug: Log photo information
                                error_log("Debug: Wanufaika photo: " . ($wanufaika['photo'] ?? 'NULL'));
                                error_log("Debug: Photo empty check: " . (empty($wanufaika['photo']) ? 'TRUE' : 'FALSE'));

                                if (!empty($wanufaika['photo'])):
                                    $imageUrl = $wanufaikaModel->getImageUrl($wanufaika['photo']);
                                    error_log("Debug: Generated image URL: " . $imageUrl);
                                ?>
                                    <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($wanufaika['name']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="<?= upload_url('Wanufaika/1.jpeg') ?>" alt="Default Wanufaika Image" class="img-fluid">
                                <?php endif; ?>
                                <div class="wanufaika-overlay">
                                    <div class="wanufaika-actions">
                                        <a href="wanufaika-details.php?id=<?= $wanufaika['id'] ?>" class="btn btn-primary btn-sm">
                                            <span class="me-2">👁️</span>Soma Zaidi
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="wanufaika-content p-4">
                                <div class="wanufaika-meta mb-2">
                                    <span class="badge bg-primary me-2">Mafanikio</span>
                                    <span class="badge bg-secondary"><?= $wanufaikaModel->formatDate($wanufaika['date_created']) ?></span>
                                </div>
                                <h5 class="wanufaika-title mb-3">
                                    <span class="text-decoration-none" style="cursor: pointer;" onclick="showWanufaikaDetails(<?= $wanufaika['id'] ?>)"><?= htmlspecialchars($wanufaika['title']) ?></span>
                                </h5>
                                <p class="wanufaika-author text-muted mb-2">
                                    <span class="me-1">👤</span><?= htmlspecialchars($wanufaika['name']) ?>
                                </p>
                                <p class="wanufaika-description text-muted mb-3"><?= $wanufaikaModel->truncateText($wanufaika['description']) ?></p>

                                <div class="wanufaika-footer d-flex justify-content-between align-items-center">
                                    <div class="wanufaika-date">
                                        <small class="text-muted">
                                            <span class="me-1">📅</span><?= $wanufaikaModel->formatDate($wanufaika['date_created']) ?>
                                        </small>
                                    </div>
                                    <div class="wanufaika-read">
                                        <a href="wanufaika-details.php?id=<?= $wanufaika['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <span class="me-1">📖</span>Soma Hadithi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Hakuna hadithi za wanufaika zilizopatikana kwa sasa.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center" data-aos="fade-up">
                <a href="#" class="btn btn-primary btn-lg">
                    <span class="me-2">📋</span>Tazama Wanufaika Wote
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How to Become a Success Story Section -->
<section class="how-to-become-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Jinsi Ya Kuwa Mwanufaika</h2>
                <p class="section-subtitle">Fuata hatua hizi rahisi kuwa mwanufaika wa Panda Digital</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">1</span>
                    </div>
                    <h5 class="step-title mb-3">Jisajili</h5>
                    <p class="step-description text-muted">Jisajili kwenye jukwaa letu la Panda Digital</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">2</span>
                    </div>
                    <h5 class="step-title mb-3">Jifunze</h5>
                    <p class="step-description text-muted">Jifunze kozi na mafunzo yanayokufaa</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">3</span>
                    </div>
                    <h5 class="step-title mb-3">Tekeleza</h5>
                    <p class="step-description text-muted">Tekeleza ulichojifunza katika maisha yako</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">4</span>
                    </div>
                    <h5 class="step-title mb-3">Fanikiwa</h5>
                    <p class="step-description text-muted">Ona mafanikio na ukuze biashara yako</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h2 class="cta-title">Je, Unataka Kuwa Mwanufaika Wetu?</h2>
                <p class="cta-subtitle">
                    Jisajili sasa na uanze safari yako ya mafanikio. Panda Digital inakupa zana na maarifa yote unayohitaji kufanikiwa.
                </p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg me-3">
                        <span class="me-2">➕</span>Jisajili Sasa
                    </a>
                    <a href="courses.php" class="btn btn-outline-light btn-lg">
                        <span class="me-2">🎓</span>Tazama Kozi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-right">
                <h3 class="contact-title mb-4">Wasiliana Nasi</h3>
                <p class="contact-text mb-4">
                    Una swali? Tunahitaji kusikia kutoka kwako. Wasiliana nasi kupitia njia zifuatazo:
                </p>
                <div class="contact-info">
                    <div class="contact-item mb-3">
                        <span class="me-3 text-primary" style="font-size: 1.2rem;">📞</span>
                        <a href="tel:+255123456789">+255 123 456 789</a>
                    </div>
                    <div class="contact-item mb-3">
                        <span class="me-3 text-primary" style="font-size: 1.2rem;">✉️</span>
                        <a href="mailto:info@pandadigital.co.tz">info@pandadigital.co.tz</a>
                    </div>
                    <div class="contact-item mb-3">
                        <span class="me-3 text-primary" style="font-size: 1.2rem;">📍</span>
                        <span>Dar es Salaam, Tanzania</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-left">
                <div class="contact-form-wrapper">
                    <h4 class="mb-3">Tuma Ujumbe</h4>
                    <form class="contact-form">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Jina lako" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Barua pepe" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Ujumbe wako" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Tuma Ujumbe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Wanufaika card styles */
    .wanufaika-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .wanufaika-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .wanufaika-image {
        position: relative;
        overflow: hidden;
        height: 280px;
    }

    .wanufaika-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        object-position: center top;
    }

    .wanufaika-card:hover .wanufaika-image img {
        transform: scale(1.1);
    }

    .wanufaika-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .wanufaika-card:hover .wanufaika-overlay {
        opacity: 1;
    }

    .wanufaika-content {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .wanufaika-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .wanufaika-author {
        font-size: 0.9rem;
        color: #64748b;
    }

    .wanufaika-description {
        flex: 1;
        margin-bottom: 1rem;
        color: #64748b;
    }

    /* Link styling - remove underlines */
    .wanufaika-card a {
        text-decoration: none;
        color: inherit;
        transition: color 0.3s ease;
    }

    .wanufaika-card a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .wanufaika-title a {
        color: #1e293b;
        text-decoration: none;
    }

    .wanufaika-title a:hover {
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

    /* Step card styles */
    .step-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .step-number {
        margin-bottom: 1rem;
    }

    .step-title {
        color: #1e293b;
        font-weight: 600;
    }

    .step-description {
        color: #64748b;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .cta-buttons {
        margin-top: 2rem;
    }

    /* Contact Section */
    .contact-title {
        color: #1e293b;
        font-weight: 600;
    }

    .contact-text {
        color: #64748b;
    }

    .contact-item {
        display: flex;
        align-items: center;
    }

    .contact-item a {
        color: #1e293b;
    }

    .contact-item a:hover {
        color: var(--primary-color, #ffbc3b);
    }

    .contact-form-wrapper {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .contact-form .form-control {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .contact-form .form-control:focus {
        border-color: var(--primary-color, #ffbc3b);
        box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
    }

    /* General Styles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        margin-bottom: 0;
    }

    .btn {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 188, 59, 0.3);
    }

    .btn-outline-light {
        border: 2px solid white;
        color: white;
    }

    .btn-outline-light:hover {
        background: white;
        color: var(--primary-color, #ffbc3b);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2.5rem;
        }

        .cta-title {
            font-size: 2rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .wanufaika-image {
            height: 220px;
        }
    }

    @media (max-width: 576px) {
        .wanufaika-image {
            height: 200px;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>