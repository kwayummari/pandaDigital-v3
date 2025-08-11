<?php
require_once 'config/init.php';
require_once 'models/Blog.php';

// Get blog post ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('index.php');
}

// Initialize Blog model
$blogModel = new Blog();
$post = $blogModel->getPostById($id);

if (!$post) {
    redirect('index.php');
}

// Get related posts
$relatedPosts = $blogModel->getRelatedPosts($id, 3);

// Set page title
$pageTitle = htmlspecialchars($post['title']) . ' - ' . env('APP_NAME');
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
                    <nav aria-label="breadcrumb text-dark">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item" style="color: #000000;"><a href="<?= app_url() ?>" style="color: #000000;">Nyumbani</a></li>
                            <li class="breadcrumb-item" style="color: #000000;"><a href="<?= app_url('index.php#habari') ?>" style="color: #000000;">Habari</a></li>
                            <li class="breadcrumb-item active" style="color: #000000;" aria-current="page"><?= htmlspecialchars($post['title']) ?></li>
                        </ol>
                    </nav>
                    <h1 class="page-title"><?= htmlspecialchars($post['title']) ?></h1>
                    <p class="page-subtitle">Habari za hivi karibuni na ujuzi wa kidijitali</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Post Details Section -->
    <section class="blog-details-section py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="blog-main" data-aos="fade-up">
                        <!-- Blog Image -->
                        <div class="blog-image mb-4">
                            <img src="<?= $blogModel->getImageUrl($post['photo'] ?? '') ?>"
                                alt="<?= htmlspecialchars($post['title']) ?>"
                                class="img-fluid rounded shadow">
                        </div>

                        <!-- Blog Content -->
                        <div class="blog-content">
                            <!-- Meta Information -->
                            <div class="blog-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <span>Iliundwa: <?= $blogModel->formatDate($post['date_created']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <span>Mwandishi: <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="blog-description mb-4">
                                <h3 class="section-subtitle">Maelezo</h3>
                                <div class="description-content">
                                    <?= $post['excerpt'] ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="blog-content mb-4">
                                <h3 class="section-subtitle">Maandiko</h3>
                                <div class="content-text">
                                    <p class="text-muted">Maandiko kamili ya habari hii yataonekana hapa baada ya kujazwa na mwenyeji.</p>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="blog-tags mb-4">
                                <h3 class="section-subtitle">Tags</h3>
                                <div class="tags-content">
                                    <p class="text-muted">Tags za habari hii zitaonekana hapa baada ya kujazwa na mwenyeji.</p>
                                </div>
                            </div>

                            <!-- Social Share -->
                            <div class="blog-actions mt-5">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="mb-0">Shiriki:</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="social-share">
                                            <a href="#" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fab fa-facebook-f"></i> Facebook
                                            </a>
                                            <a href="#" class="btn btn-outline-info btn-sm me-2">
                                                <i class="fab fa-twitter"></i> Twitter
                                            </a>
                                            <a href="#" class="btn btn-outline-success btn-sm">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog-sidebar" data-aos="fade-left">
                        <!-- Author Info -->
                        <div class="card sidebar-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Kuhusu Mwandishi
                                </h5>
                                <div class="author-info">
                                    <p class="text-muted">Itaonekana baada ya kujazwa na mwenyeji.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Posts -->
                        <?php if (!empty($relatedPosts)): ?>
                            <div class="card sidebar-card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-newspaper text-primary me-2"></i>
                                        Habari za Hivi Karibuni
                                    </h5>
                                    <div class="recent-posts">
                                        <?php foreach ($relatedPosts as $related): ?>
                                            <div class="recent-item mb-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <img src="<?= $blogModel->getImageUrl($related['photo']) ?>"
                                                            alt="<?= htmlspecialchars($related['title']) ?>"
                                                            class="img-fluid rounded">
                                                    </div>
                                                    <div class="col-8">
                                                        <h6 class="recent-title">
                                                            <a href="<?= app_url('habari-details.php?id=' . $related['id']) ?>">
                                                                <?= htmlspecialchars($related['title']) ?>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <?= $blogModel->formatDate($related['date_created']) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Newsletter Signup -->
                        <div class="card sidebar-card">
                            <div class="card-body text-center">
                                <h5 class="card-title">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    Jiunge na Newsletter
                                </h5>
                                <p class="text-muted small">Pata habari mpya na fursa za biashara moja kwa moja kwenye email yako.</p>
                                <form class="newsletter-form">
                                    <div class="input-group mb-3">
                                        <input type="email" class="form-control" placeholder="Email yako" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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