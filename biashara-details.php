<?php
require_once 'config/init.php';
require_once 'models/Business.php';

// Get business ID from URL
$businessId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$businessId) {
    header('Location: biashara.php');
    exit;
}

// Initialize the Business model
$businessModel = new Business();

// Get business details
$business = $businessModel->getBusinessById($businessId);

if (!$business) {
    header('Location: biashara.php');
    exit;
}

// Get business photos
$businessPhotos = $businessModel->getBusinessPhotos($business['user_id']);

// Get similar businesses
$similarBusinesses = $businessModel->getSimilarBusinesses($business['id'], $business['location'], 4);

$pageTitle = $business['name'] . ' - ' . env('APP_NAME');

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="biashara.php">Tangaza Biashara</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($business['name']) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Business Details Section -->
<section class="business-details-section py-5">
    <div class="container">
        <div class="row">
            <!-- Business Images -->
            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <div class="business-images">
                    <?php if (!empty($businessPhotos)): ?>
                        <div class="main-image mb-3">
                            <img src="<?= $businessModel->getImageUrl($businessPhotos[0]['photo']) ?>"
                                alt="<?= htmlspecialchars($business['name']) ?>"
                                class="img-fluid rounded"
                                style="width: 100%; height: 400px; object-fit: cover;">
                        </div>
                        <?php if (count($businessPhotos) > 1): ?>
                            <div class="thumbnail-images">
                                <div class="row g-2">
                                    <?php foreach (array_slice($businessPhotos, 1, 4) as $photo): ?>
                                        <div class="col-3">
                                            <img src="<?= $businessModel->getImageUrl($photo['photo']) ?>"
                                                alt="Business Photo"
                                                class="img-fluid rounded thumbnail-img"
                                                style="width: 100%; height: 80px; object-fit: cover; cursor: pointer;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="main-image mb-3">
                            <img src="<?= asset('images/business/default-business.jpg') ?>"
                                alt="Default Business Image"
                                class="img-fluid rounded"
                                style="width: 100%; height: 400px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Business Information -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="business-info">
                    <div class="business-header mb-4">
                        <h1 class="business-title"><?= htmlspecialchars($business['name']) ?></h1>
                        <div class="business-meta mb-3">
                            <span class="badge bg-primary me-2">Biashara</span>
                            <span class="badge bg-secondary"><?= $businessModel->formatDate($business['date_created']) ?></span>
                        </div>
                        <p class="business-location">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <?= htmlspecialchars($business['location']) ?>
                        </p>
                    </div>

                    <div class="business-description mb-4">
                        <h5>Maelezo ya Biashara</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($business['maelezo'])) ?></p>
                    </div>

                    <div class="business-owner mb-4">
                        <h5>Mmiliki wa Biashara</h5>
                        <div class="owner-info p-3 bg-light rounded">
                            <p class="mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>Jina:</strong> <?= htmlspecialchars($business['owner_name'] ?? 'Haijulikani') ?>
                            </p>
                            <?php if (!empty($business['owner_phone'])): ?>
                                <p class="mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <strong>Simu:</strong>
                                    <a href="tel:<?= htmlspecialchars($business['owner_phone']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($business['owner_phone']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($business['owner_email'])): ?>
                                <p class="mb-0">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <strong>Barua pepe:</strong>
                                    <a href="mailto:<?= htmlspecialchars($business['owner_email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($business['owner_email']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="business-actions">
                        <a href="tel:<?= htmlspecialchars($business['owner_phone'] ?? '') ?>" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-phone me-2"></i>Piga Simu
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $business['owner_phone'] ?? '') ?>"
                            class="btn btn-success btn-lg me-3"
                            target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                        <button class="btn btn-outline-primary btn-lg" onclick="shareBusiness()">
                            <i class="fas fa-share me-2"></i>Shiriki
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Businesses Section -->
<?php if (!empty($similarBusinesses)): ?>
    <section class="similar-businesses-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4" data-aos="fade-up">
                    <h3 class="section-title">Biashara Zinazofanana</h3>
                    <p class="section-subtitle">Biashara zingine katika eneo hili</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($similarBusinesses as $similarBusiness): ?>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up">
                        <div class="similar-business-card">
                            <div class="similar-business-image">
                                <?php
                                $similarPhotos = $businessModel->getBusinessPhotos($similarBusiness['user_id']);
                                $similarMainPhoto = !empty($similarPhotos) ? $similarPhotos[0]['photo'] : null;
                                ?>
                                <?php if ($similarMainPhoto): ?>
                                    <img src="<?= $businessModel->getImageUrl($similarMainPhoto) ?>"
                                        alt="<?= htmlspecialchars($similarBusiness['name']) ?>"
                                        class="img-fluid">
                                <?php else: ?>
                                    <img src="<?= asset('images/business/default-business.jpg') ?>"
                                        alt="Default Business Image"
                                        class="img-fluid">
                                <?php endif; ?>
                            </div>
                            <div class="similar-business-content p-3">
                                <h6 class="similar-business-title">
                                    <a href="biashara-details.php?id=<?= $similarBusiness['id'] ?>">
                                        <?= htmlspecialchars($similarBusiness['name']) ?>
                                    </a>
                                </h6>
                                <p class="similar-business-location text-muted">
                                    <small><?= htmlspecialchars($similarBusiness['location']) ?></small>
                                </p>
                                <a href="biashara-details.php?id=<?= $similarBusiness['id'] ?>" class="btn btn-outline-primary btn-sm">
                                    Tazama Zaidi
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Contact Form Section -->
<section class="contact-form-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-form-card">
                    <div class="text-center mb-4">
                        <h3>Wasiliana na Mmiliki wa Biashara</h3>
                        <p class="text-muted">Tuma ujumbe au swali kuhusu biashara hii</p>
                    </div>

                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Jina Lako</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Namba ya Simu</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Barua Pepe</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Ujumbe</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Tuma Ujumbe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
    function shareBusiness() {
        if (navigator.share) {
            navigator.share({
                title: '<?= htmlspecialchars($business['name']) ?>',
                text: 'Tazama biashara hii: <?= htmlspecialchars($business['name']) ?>',
                url: window.location.href
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            const url = window.location.href;
            const text = 'Tazama biashara hii: <?= htmlspecialchars($business['name']) ?>';

            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    alert('URL imekopishwa kwenye clipboard!');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('URL imekopishwa kwenye clipboard!');
            }
        }
    }

    // Handle contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Here you would typically send the data to your backend
        // For now, we'll just show a success message
        alert('Asante! Ujumbe wako umetumwa. Mmiliki wa biashara atawasiliana nawe hivi karibuni.');

        // Reset form
        this.reset();
    });

    // Image gallery functionality
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.addEventListener('click', function() {
            const mainImage = document.querySelector('.main-image img');
            mainImage.src = this.src;
        });
    });
</script>

<style>
    .business-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .business-location {
        font-size: 1.1rem;
        color: #64748b;
        margin-bottom: 1rem;
    }

    .business-description h5,
    .business-owner h5 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .owner-info {
        border-left: 4px solid var(--primary-color, #ffbc3b);
    }

    .owner-info a {
        color: var(--primary-color, #ffbc3b);
    }

    .owner-info a:hover {
        text-decoration: underline;
    }

    .business-actions {
        margin-top: 2rem;
    }

    .thumbnail-img {
        transition: transform 0.3s ease;
        border: 2px solid transparent;
    }

    .thumbnail-img:hover {
        transform: scale(1.05);
        border-color: var(--primary-color, #ffbc3b);
    }

    .similar-business-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .similar-business-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .similar-business-image {
        height: 150px;
        overflow: hidden;
    }

    .similar-business-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .similar-business-card:hover .similar-business-image img {
        transform: scale(1.05);
    }

    .similar-business-title a {
        color: #1e293b;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .similar-business-title a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .contact-form-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .section-title {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        color: #64748b;
    }

    .page-header {
        padding: 80px 0 40px;
        color: white;
    }

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

    @media (max-width: 768px) {
        .business-title {
            font-size: 2rem;
        }

        .business-actions .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .business-actions .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>