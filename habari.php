<?php
require_once 'config/init.php';
require_once 'models/Blog.php';

// Initialize the Blog model
$blogModel = new Blog();

// Get featured blog posts from database
$featuredBlogPosts = $blogModel->getLatestBlogPosts(8);

$pageTitle = 'Habari Zote - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="page-title">Habari Zote</h1>
                <p class="page-subtitle">Tazama habari na makala muhimu kuhusiana na jukwaa la Panda Digital na ujasiriamali wa kidijitali</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Habari</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <i class="fas fa-newspaper fa-4x text-white"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Blog Posts Section -->
<section class="featured-blog-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Habari Mpya</h2>
                <p class="section-subtitle">Makala na habari muhimu kuhusiana na ujasiriamali wa kidijitali</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredBlogPosts)): ?>
                <?php foreach ($featuredBlogPosts as $index => $blogPost): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="blog-card h-100">
                            <div class="blog-image">
                                <?php if (!empty($blogPost['photo'])): ?>
                                    <img src="<?= $blogModel->getImageUrl($blogPost['photo']) ?>" alt="<?= htmlspecialchars($blogPost['name']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="<?= upload_url('Blog/TDA4.jpg') ?>" alt="Default Blog Image" class="img-fluid">
                                <?php endif; ?>
                                <div class="blog-overlay">
                                    <div class="blog-actions">
                                        <a href="habari-details.php?id=<?= $blogPost['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>Soma Zaidi
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="blog-content p-4">
                                <div class="blog-meta mb-2">
                                    <span class="badge bg-primary me-2">Habari</span>
                                    <span class="badge bg-secondary"><?= $blogModel->formatDate($blogPost['date_created']) ?></span>
                                </div>
                                <h5 class="blog-title mb-3">
                                    <a href="habari-details.php?id=<?= $blogPost['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($blogPost['name']) ?></a>
                                </h5>
                                <p class="blog-excerpt text-muted mb-3"><?= $blogModel->truncateText($blogPost['maelezo']) ?></p>

                                <div class="blog-footer d-flex justify-content-between align-items-center">
                                    <div class="blog-date">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?= $blogModel->formatDate($blogPost['date_created']) ?>
                                        </small>
                                    </div>
                                    <div class="blog-read">
                                        <a href="habari-details.php?id=<?= $blogPost['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-book-open me-1"></i>Soma Makala
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Hakuna habari zilizopatikana kwa sasa.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center" data-aos="fade-up">
                <a href="#" class="btn btn-primary btn-lg">
                    <i class="fas fa-th-list me-2"></i>Tazama Habari Zote
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Blog Categories Section -->
<section class="blog-categories-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Aina za Habari</h2>
                <p class="section-subtitle">Chagua aina ya habari unayotaka kusoma</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="category-card text-center p-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-store fa-3x text-primary"></i>
                    </div>
                    <h5 class="category-title mb-3">Biashara</h5>
                    <p class="category-description text-muted">Habari za biashara na ujasiriamali</p>
                    <span class="badge bg-primary">23 Makala</span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="category-card text-center p-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-laptop-code fa-3x text-success"></i>
                    </div>
                    <h5 class="category-title mb-3">Kidijitali</h5>
                    <p class="category-description text-muted">Teknolojia na ujuzi wa kidijitali</p>
                    <span class="badge bg-success">18 Makala</span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="category-card text-center p-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-hand-holding-usd fa-3x text-info"></i>
                    </div>
                    <h5 class="category-title mb-3">Ufadhili</h5>
                    <p class="category-description text-muted">Mikopo, ufadhili na tuzo</p>
                    <span class="badge bg-info">15 Makala</span>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="category-card text-center p-4">
                    <div class="category-icon mb-3">
                        <i class="fas fa-chart-line fa-3x text-warning"></i>
                    </div>
                    <h5 class="category-title mb-3">Maendeleo</h5>
                    <p class="category-description text-muted">Maendeleo ya kiuchumi na kijamii</p>
                    <span class="badge bg-warning">12 Makala</span>
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
                <h2 class="cta-title">Je, Unataka Kujifunza Zaidi?</h2>
                <p class="cta-subtitle">
                    Jisajili sasa na upate habari mpya na makala muhimu kuhusiana na ujasiriamali wa kidijitali.
                </p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Jisajili Sasa
                    </a>
                    <a href="courses.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-graduation-cap me-2"></i>Tazama Kozi
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
                    Una swali au unahitaji msaada? Tunahitaji kusikia kutoka kwako. Wasiliana nasi kupitia njia zifuatazo:
                </p>
                <div class="contact-info">
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone me-3 text-primary"></i>
                        <a href="tel:+255123456789">+255 123 456 789</a>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope me-3 text-primary"></i>
                        <a href="mailto:info@pandadigital.co.tz">info@pandadigital.co.tz</a>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-map-marker-alt me-3 text-primary"></i>
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

<?php include 'includes/footer.php'; ?>

<style>
    /* Blog card styles */
    .blog-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .blog-image {
        position: relative;
        overflow: hidden;
        height: 280px;
    }

    .blog-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        object-position: center top;
    }

    .blog-card:hover .blog-image img {
        transform: scale(1.1);
    }

    .blog-overlay {
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

    .blog-card:hover .blog-overlay {
        opacity: 1;
    }

    .blog-content {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .blog-excerpt {
        flex: 1;
        margin-bottom: 1rem;
        color: #64748b;
    }

    /* Link styling - remove underlines */
    .blog-card a {
        text-decoration: none;
        color: inherit;
        transition: color 0.3s ease;
    }

    .blog-card a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .blog-title a {
        color: #1e293b;
        text-decoration: none;
    }

    .blog-title a:hover {
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

    /* Category card styles */
    .category-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .category-icon {
        margin-bottom: 1rem;
    }

    .category-title {
        color: #1e293b;
        font-weight: 600;
    }

    .category-description {
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

        .blog-image {
            height: 220px;
        }
    }

    @media (max-width: 576px) {
        .blog-image {
            height: 200px;
        }
    }
</style>