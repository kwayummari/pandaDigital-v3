<?php
require_once 'models/Soko.php';

$sokoModel = new Soko();
$businesses = $sokoModel->getAllBusinesses();
$businessCount = $sokoModel->getBusinessCount('approved');

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: 1;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row">
            <div class="col-md-8">
                <ul class="list-inline custom-breadcrumb mb-2">
                    <li class="list-inline-item">
                        <a class="h2 text-primary font-secondary" href="index.php">Nyumbani</a>
                    </li>
                    <span class="ti-angle-double-right" style="color: #ffffff;"></span>
                    <li class="list-inline-item text-white h3 font-secondary">Soko</li>
                </ul>
                <p class="text-lighten mb-0">Kwa kushirikiana na wataalamu, tumekuandalia mbalimbali kama vile usimamuzi wa biashara, usimamizi wa fedha na ufanyaji masoko zitakazosaidia kukuza ujuzi wako ili kujiajiri au kuajirika</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="section-sm">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center section-title justify-content-between">
                    <h2 class="mb-0 text-nowrap mr-3">Masoko Yote (<?= $businessCount ?>)</h2>
                    <div class="border-top w-100 border-primary d-none d-sm-block"></div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php if (!empty($businesses)): ?>
                <?php foreach ($businesses as $business): ?>
                    <div class="col-lg-4 col-md-6 mb-5">
                        <div class="business-card">
                            <img class="business-card-image"
                                src="<?= $sokoModel->getImageUrl($business['photo']) ?>"
                                alt="<?= htmlspecialchars($business['name']) ?>"
                                onerror="this.src='<?= asset('images/business/default-business.jpg') ?>'">
                            <div class="business-card-body">
                                <ul class="list-inline mb-2">
                                    <li class="list-inline-item">
                                        <i class="ti-calendar mr-1 text-color"></i>
                                        <?= $sokoModel->formatDate($business['date_created']) ?>
                                    </li>
                                    <li class="list-inline-item">
                                        <a class="text-color" href="index.php">Panda Digital</a>
                                    </li>
                                </ul>
                                <a href="#">
                                    <h4 class="business-card-title">Fahamu Kuhusu <?= htmlspecialchars($business['name']) ?></h4>
                                </a>
                                <p class="business-card-text mb-4">
                                    <?= $sokoModel->truncateText($business['maelezo'], 150) ?>
                                </p>
                                <a href="soko-details.php?id=<?= $business['id'] ?>" class="btn btn-primary btn-sm">Ona Biashara</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <h3 style="color: #E2C124;"><b>Hamna Biashara Yoyote</b></h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    /* CSS Reset for business cards to override Bootstrap */
    .business-card,
    .business-card * {
        border: none !important;
        outline: none !important;
    }

    .page-header {
        padding: 100px 0;
        color: white;
        text-align: left;
    }

    .page-header h2,
    .page-header h3 {
        color: white;
    }

    .page-header .text-primary {
        color: #ffc107 !important;
    }

    .business-card {
        border: none !important;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background: white;
    }

    .business-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .business-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .business-card-body {
        padding: 1.5rem;
    }

    .business-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #2d3748;
    }

    .business-card-title a {
        text-decoration: none;
        color: inherit;
    }

    .business-card-title a:hover {
        color: #3182ce;
    }

    .business-card-text {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .business-card a {
        text-decoration: none;
    }

    .list-inline-item a {
        text-decoration: none;
    }

    .text-color {
        color: #3182ce;
    }

    .section-title {
        margin-bottom: 3rem;
    }

    /* Removed conflicting border-primary rule */

    .btn-primary {
        background-color: #3182ce;
        border-color: #3182ce;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2c5aa0;
        border-color: #2c5aa0;
        transform: translateY(-2px);
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    /* Override Bootstrap card styles */
    .business-card.card {
        border: none !important;
        border-radius: 16px !important;
    }

    .business-card .card-img-top {
        border-radius: 0 !important;
    }

    /* Ensure no blue borders from Bootstrap */
    .business-card.border-primary {
        border: none !important;
    }

    .business-card.border {
        border: none !important;
    }

    /* Additional Bootstrap overrides */
    .business-card[class*="border-"] {
        border: none !important;
    }

    .business-card .border-primary,
    .business-card .border-secondary,
    .business-card .border-success,
    .business-card .border-danger,
    .business-card .border-warning,
    .business-card .border-info,
    .business-card .border-light,
    .business-card .border-dark {
        border: none !important;
    }

    /* Force remove any Bootstrap border utilities */
    .business-card.border-0,
    .business-card.border-top,
    .business-card.border-end,
    .business-card.border-bottom,
    .business-card.border-start {
        border: none !important;
    }
</style>

<?php include 'includes/footer.php'; ?>