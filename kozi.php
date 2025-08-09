<?php
require_once 'config/init.php';

// Get course categories and courses from database
$courseCategories = [
    [
        'id' => 1,
        'name' => 'Ujuzi wa Kidijitali',
        'icon' => 'fas fa-laptop-code',
        'description' => 'Jifunze ujuzi wa kidijitali wa msingi na wa juu',
        'color' => 'primary'
    ],
    [
        'id' => 2,
        'name' => 'Biashara ya Mtandaoni',
        'icon' => 'fas fa-shopping-cart',
        'description' => 'Jifunze jinsi ya kuanza na kuendeleza biashara ya mtandaoni',
        'color' => 'success'
    ],
    [
        'id' => 3,
        'name' => 'Uchapishaji wa Mitandao ya Kijamii',
        'icon' => 'fas fa-share-alt',
        'description' => 'Jifunze jinsi ya kutumia mitandao ya kijamii kwa biashara',
        'color' => 'info'
    ],
    [
        'id' => 4,
        'name' => 'Uchambuzi wa Data',
        'icon' => 'fas fa-chart-line',
        'description' => 'Jifunze jinsi ya kuchambua data na kufanya maamuzi sahihi',
        'color' => 'warning'
    ]
];

$featuredCourses = [
    [
        'id' => 1,
        'title' => 'Ujuzi wa Msingi wa Kompyuta',
        'category' => 'Ujuzi wa Kidijitali',
        'instructor' => 'Mama Sarah Mwambene',
        'duration' => '8 Wiki',
        'level' => 'Mwanzo',
        'price' => 'Tsh 50,000',
        'image' => 'images/courses/basic-computer.jpg',
        'rating' => 4.8,
        'students' => 156,
        'description' => 'Jifunze ujuzi wa msingi wa kompyuta na jinsi ya kutumia programu muhimu za ofisi.'
    ],
    [
        'id' => 2,
        'title' => 'Biashara ya Instagram',
        'category' => 'Biashara ya Mtandaoni',
        'instructor' => 'Mama Fatima Hassan',
        'duration' => '6 Wiki',
        'level' => 'Kati',
        'price' => 'Tsh 75,000',
        'image' => 'images/courses/instagram-business.jpg',
        'rating' => 4.9,
        'students' => 89,
        'description' => 'Jifunze jinsi ya kuanza na kuendeleza biashara kwa kutumia Instagram.'
    ],
    [
        'id' => 3,
        'title' => 'Uchapishaji wa Mitandao ya Kijamii',
        'category' => 'Uchambuzi wa Data',
        'instructor' => 'Mama Grace Mwakatobe',
        'duration' => '10 Wiki',
        'level' => 'Juu',
        'price' => 'Tsh 120,000',
        'image' => 'images/courses/social-media.jpg',
        'rating' => 4.7,
        'students' => 203,
        'description' => 'Jifunze jinsi ya kuchambua data kutoka mitandao ya kijamii na kufanya maamuzi sahihi.'
    ],
    [
        'id' => 4,
        'title' => 'Ujuzi wa Excel',
        'category' => 'Ujuzi wa Kidijitali',
        'instructor' => 'Mama Amina Juma',
        'duration' => '4 Wiki',
        'level' => 'Kati',
        'price' => 'Tsh 60,000',
        'image' => 'images/courses/excel-skills.jpg',
        'rating' => 4.6,
        'students' => 134,
        'description' => 'Jifunze jinsi ya kutumia Excel kwa uchambuzi wa data na biashara.'
    ]
];

$pageTitle = 'Kozi Zote - ' . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Jifunze ujuzi wa kidijitali na biashara ya mtandaoni. Kozi za ubora wa juu kwa wanawake Tanzania.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo/favicon.png') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="page-title">Kozi Zote</h1>
                    <p class="page-subtitle">Jifunze ujuzi wa kidijitali na biashara ya mtandaoni kutoka kwa wataalamu wenye uzoefu</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li class="breadcrumb-item active">Kozi</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <div class="header-icon">
                        <i class="fas fa-graduation-cap fa-4x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Categories -->
    <section class="categories-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Kategoria za Kozi</h2>
                    <p class="section-subtitle">Chagua kategoria inayokufaa zaidi na uanze safari yako ya kujifunza</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($courseCategories as $index => $category): ?>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="category-card text-center p-4 h-100">
                            <div class="category-icon mb-3">
                                <i class="<?= $category['icon'] ?> fa-3x text-<?= $category['color'] ?>"></i>
                            </div>
                            <h4 class="category-title mb-3"><?= $category['name'] ?></h4>
                            <p class="category-description text-muted"><?= $category['description'] ?></p>
                            <a href="#courses-<?= $category['id'] ?>" class="btn btn-outline-<?= $category['color'] ?> mt-3">
                                Tazama Kozi <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section class="featured-courses-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Kozi Zilizochaguliwa</h2>
                    <p class="section-subtitle">Kozi bora zaidi zilizochaguliwa na wanafunzi wetu</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($featuredCourses as $index => $course): ?>
                    <div class="col-lg-6 col-xl-3" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="course-card h-100">
                            <div class="course-image">
                                <img src="<?= asset($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="img-fluid">
                                <div class="course-overlay">
                                    <div class="course-actions">
                                        <a href="#" class="btn btn-primary btn-sm">
                                            <i class="fas fa-play me-2"></i>Anza Kozi
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="course-content p-4">
                                <div class="course-meta mb-2">
                                    <span class="badge bg-primary me-2"><?= $course['category'] ?></span>
                                    <span class="badge bg-secondary"><?= $course['level'] ?></span>
                                </div>
                                <h5 class="course-title mb-3">
                                    <a href="#" class="text-decoration-none"><?= htmlspecialchars($course['title']) ?></a>
                                </h5>
                                <p class="course-description text-muted mb-3"><?= htmlspecialchars($course['description']) ?></p>

                                <div class="course-instructor mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-2"></i><?= htmlspecialchars($course['instructor']) ?>
                                    </small>
                                </div>

                                <div class="course-details d-flex justify-content-between align-items-center mb-3">
                                    <div class="course-duration">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?= $course['duration'] ?>
                                        </small>
                                    </div>
                                    <div class="course-rating">
                                        <small class="text-warning">
                                            <i class="fas fa-star"></i><?= $course['rating'] ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="course-footer d-flex justify-content-between align-items-center">
                                    <div class="course-price">
                                        <strong class="text-primary"><?= $course['price'] ?></strong>
                                    </div>
                                    <div class="course-students">
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i><?= $course['students'] ?> wanafunzi
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-center" data-aos="fade-up">
                    <a href="#" class="btn btn-primary btn-lg">
                        <i class="fas fa-th-list me-2"></i>Tazama Kozi Zote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Benefits -->
    <section class="benefits-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Kwa Nini Kuchagua Kozi Zetu?</h2>
                    <p class="section-subtitle">Tunaweka wanawake Tanzania mbele kwa kutoa huduma bora zaidi</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="benefit-card text-center p-4">
                        <div class="benefit-icon mb-3">
                            <i class="fas fa-certificate fa-3x text-primary"></i>
                        </div>
                        <h5 class="benefit-title mb-3">Cheti cha Uhalali</h5>
                        <p class="benefit-description text-muted">Pata cheti cha uhalali baada ya kumaliza kozi yoyote</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="benefit-card text-center p-4">
                        <div class="benefit-icon mb-3">
                            <i class="fas fa-users fa-3x text-success"></i>
                        </div>
                        <h5 class="benefit-title mb-3">Wataalamu Wenye Uzoefu</h5>
                        <p class="benefit-description text-muted">Jifunze kutoka kwa wataalamu wenye uzoefu wa miaka mingi</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="benefit-card text-center p-4">
                        <div class="benefit-icon mb-3">
                            <i class="fas fa-clock fa-3x text-info"></i>
                        </div>
                        <h5 class="benefit-title mb-3">Muda wa Kujifunza</h5>
                        <p class="benefit-description text-muted">Jifunze kwa muda wako mwenyewe na kasi yako</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="benefit-card text-center p-4">
                        <div class="benefit-icon mb-3">
                            <i class="fas fa-headset fa-3x text-warning"></i>
                        </div>
                        <h5 class="benefit-title mb-3">Msaada wa 24/7</h5>
                        <p class="benefit-description text-muted">Pata msaada wakati wowote unapohitaji</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h2 class="cta-title">Tayari Kuanza Safari Yako ya Kujifunza?</h2>
                    <p class="cta-subtitle">Jiunge na wanafunzi zaidi ya 1,000 ambao tayari wameanza safari yao ya kujifunza ujuzi wa kidijitali</p>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <a href="#" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i>Anza Sasa
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

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