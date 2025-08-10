<?php
require_once 'models/Soko.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: soko.php');
    exit;
}

$sokoModel = new Soko();
$business = $sokoModel->getBusinessById($id);

if (!$business) {
    header('Location: soko.php');
    exit;
}

// Get related businesses (excluding current one)
$relatedBusinesses = $sokoModel->getLatestBusinesses(3, $id);

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
                    <li class="list-inline-item">
                        <a class="h3 text-primary font-secondary" href="soko.php">Soko</a>
                    </li>
                    <span class="ti-angle-double-right" style="color: #ffffff;"></span>
                    <li class="list-inline-item text-white h3 font-secondary"><?= htmlspecialchars($business['name']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="section-sm">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="business-details">
                    <!-- Business Image -->
                    <div class="business-image mb-4">
                        <img src="<?= $sokoModel->getImageUrl($business['photo']) ?>"
                            alt="<?= htmlspecialchars($business['name']) ?>"
                            onerror="this.src='<?= asset('images/business/default-business.jpg') ?>'"
                            class="img-fluid">
                    </div>

                    <!-- Business Title -->
                    <h1 class="business-title mb-3"><?= htmlspecialchars($business['name']) ?></h1>

                    <!-- Business Meta -->
                    <div class="business-meta mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p><i class="ti-calendar mr-2"></i><strong>Tarehe:</strong> <?= $sokoModel->formatDate($business['date_created']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="ti-location-pin mr-2"></i><strong>Mahali:</strong> <?= htmlspecialchars($business['location']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Description -->
                    <div class="business-description mb-4">
                        <h3>Maelezo ya Biashara</h3>
                        <p><?= nl2br(htmlspecialchars($business['maelezo'])) ?></p>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info mb-4">
                        <h3>Mawasiliano</h3>
                        <p><i class="ti-info mr-2"></i>Kwa maelezo zaidi, tafadhali wasiliana na biashara hii moja kwa moja.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Related Businesses -->
                <div class="sidebar-card mb-4">
                    <div class="sidebar-card-body">
                        <h4 class="sidebar-card-title">Biashara Zingine</h4>
                        <?php if (!empty($relatedBusinesses)): ?>
                            <?php foreach ($relatedBusinesses as $related): ?>
                                <div class="related-business mb-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <img src="<?= $sokoModel->getImageUrl($related['photo']) ?>"
                                                alt="<?= htmlspecialchars($related['name']) ?>"
                                                onerror="this.src='<?= asset('images/business/default-business.jpg') ?>'"
                                                class="img-fluid rounded">
                                        </div>
                                        <div class="col-8">
                                            <h6 class="mb-1">
                                                <a href="soko-details.php?id=<?= $related['id'] ?>">
                                                    <?= htmlspecialchars($related['name']) ?>
                                                </a>
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="ti-location-pin mr-1"></i>
                                                <?= htmlspecialchars($related['location']) ?>
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <i class="ti-calendar mr-1"></i>
                                                <?= $sokoModel->formatDate($related['date_created']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Hakuna biashara zingine zilizopo.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back to Soko -->
                <div class="sidebar-card">
                    <div class="sidebar-card-body text-center">
                        <a href="soko.php" class="btn btn-outline-primary btn-block">
                            <i class="ti-arrow-left mr-2"></i>Rudi kwenye Soko
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
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

    .business-details {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .business-image img {
        width: 100%;
        height: auto;
        max-height: none;
        object-fit: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none !important;
    }

    .business-title {
        color: #2d3748;
        font-size: 2.5rem;
        font-weight: 700;
        border-bottom: 3px solid #3182ce;
        padding-bottom: 1rem;
    }

    .business-meta p {
        color: #4a5568;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .business-meta i {
        color: #3182ce;
        width: 20px;
    }

    .business-description h3 {
        color: #2d3748;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }

    .business-description p {
        color: #4a5568;
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .contact-info h3 {
        color: #2d3748;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }

    .contact-info p {
        color: #4a5568;
        line-height: 1.6;
    }

    .contact-info i {
        color: #3182ce;
    }

    .sidebar-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .sidebar-card-body {
        padding: 1.5rem;
    }

    .sidebar-card-title {
        color: #2d3748;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.75rem;
    }

    .related-business {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .related-business:hover {
        border-color: #3182ce;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .related-business h6 a {
        color: #2d3748;
        text-decoration: none;
        font-weight: 600;
    }

    .related-business h6 a:hover {
        color: #3182ce;
    }

    .related-business img {
        width: 100%;
        height: 80px;
        object-fit: cover;
    }

    .btn-outline-primary {
        color: #3182ce;
        border-color: #3182ce;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #3182ce;
        border-color: #3182ce;
        color: white;
        transform: translateY(-2px);
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .text-muted {
        color: #718096 !important;
    }

    .small {
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .business-title {
            font-size: 2rem;
        }

        .business-details {
            padding: 1.5rem;
        }

        .sidebar-card-body {
            padding: 1rem;
        }
    }

    /* Ensure no Bootstrap borders interfere */
    .business-details {
        border: none !important;
    }

    .business-details .card {
        border: none !important;
    }

    .sidebar-card {
        border: none !important;
    }

    .sidebar-card .card {
        border: none !important;
    }
</style>

<?php include 'includes/footer.php'; ?>