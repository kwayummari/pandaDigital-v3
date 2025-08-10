<?php
require_once 'config/init.php';

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

$featuredOpportunities = [
    [
        'id' => 24,
        'title' => 'Nafasi Ya Ajira (Meneja Wa Operesheni Na Miradi)',
        'category' => 'Ajira',
        'company' => 'Panda Digital',
        'location' => 'Dar es Salaam',
        'type' => 'Muda Kamili',
        'deadline' => '2025-05-25',
        'image' => 'images/opportunities/We-Are-Hiring---PI-.png',
        'description' => 'Tunatafuta meneja wa operesheni na miradi mwenye uzoefu wa kusimamia miradi ya kidijitali.'
    ],
    [
        'id' => 23,
        'title' => 'Pata Hadi Dola 15,000 Za Kimarekani Kwa Mjasiriamali Mpya',
        'category' => 'Ufadhili',
        'company' => 'Mastercard Foundation',
        'location' => 'Tanzania',
        'type' => 'Ufadhili',
        'deadline' => '2025-06-17',
        'image' => 'images/opportunities/Screenshot-2025-04-17-at-16.56.31.png',
        'description' => 'Programu ya FAST 2025 inatoa ufadhili hadi dola 15,000 kwa mjasiriamali mpya.'
    ],
    [
        'id' => 22,
        'title' => 'Nestlé Youth Entrepreneurship Platform (YEP)',
        'category' => 'Biashara',
        'company' => 'Nestlé',
        'location' => 'Afrika',
        'type' => 'Ufadhili',
        'deadline' => '2025-07-17',
        'image' => 'images/opportunities/Screenshot-2025-04-17-at-16.46.21.png',
        'description' => 'Jifunze na uendeleze biashara yako kupitia programu ya Nestlé YEP.'
    ],
    [
        'id' => 21,
        'title' => 'Tuzo Ya FINCA Ventures 2025',
        'category' => 'Ufadhili',
        'company' => 'FINCA Ventures',
        'location' => 'Kimataifa',
        'type' => 'Tuzo',
        'deadline' => '2025-08-16',
        'image' => 'images/opportunities/Screenshot-2025-04-16-at-11.15.46.png',
        'description' => 'Shindania na upate tuzo zaidi ya dola 100,000 za kimarekani.'
    ]
];

$pageTitle = 'Fursa Zote - ' . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Tazama fursa zote za ajira, ufadhili, mafunzo na biashara. Panda Digital - Platform bora ya fursa kwa wanawake Tanzania.">

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

    <!-- Opportunity Categories -->
    <section class="categories-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Kategoria za Fursa</h2>
                    <p class="section-subtitle">Chagua kategoria inayokufaa zaidi na uone fursa zote zinazopatikana</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($opportunityCategories as $index => $category): ?>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="category-card text-center p-4 h-100">
                            <div class="category-icon mb-3">
                                <i class="<?= $category['icon'] ?> fa-3x text-<?= $category['color'] ?>"></i>
                            </div>
                            <h4 class="category-title mb-3"><?= $category['name'] ?></h4>
                            <p class="category-description text-muted"><?= $category['description'] ?></p>
                            <div class="category-count mb-3">
                                <span class="badge bg-<?= $category['color'] ?> fs-6"><?= $category['count'] ?> Fursa</span>
                            </div>
                            <a href="#opportunities-<?= $category['id'] ?>" class="btn btn-outline-<?= $category['color'] ?> mt-3">
                                Tazama Fursa <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                <?php foreach ($featuredOpportunities as $index => $opportunity): ?>
                    <div class="col-lg-6 col-xl-3" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="opportunity-card h-100">
                            <div class="opportunity-image">
                                <img src="<?= asset($opportunity['image']) ?>" alt="<?= htmlspecialchars($opportunity['title']) ?>" class="img-fluid">
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
                                    <span class="badge bg-primary me-2"><?= $opportunity['category'] ?></span>
                                    <span class="badge bg-secondary"><?= $opportunity['type'] ?></span>
                                </div>
                                <h5 class="opportunity-title mb-3">
                                    <a href="fursa-details.php?id=<?= $opportunity['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($opportunity['title']) ?></a>
                                </h5>
                                <p class="opportunity-description text-muted mb-3"><?= htmlspecialchars($opportunity['description']) ?></p>

                                <div class="opportunity-company mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-building me-2"></i><?= htmlspecialchars($opportunity['company']) ?>
                                    </small>
                                </div>

                                <div class="opportunity-details d-flex justify-content-between align-items-center mb-3">
                                    <div class="opportunity-location">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i><?= $opportunity['location'] ?>
                                        </small>
                                    </div>
                                    <div class="opportunity-deadline">
                                        <small class="text-danger">
                                            <i class="fas fa-calendar me-1"></i><?= date('d/m/Y', strtotime($opportunity['deadline'])) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="opportunity-footer d-flex justify-content-between align-items-center">
                                    <div class="opportunity-type">
                                        <strong class="text-primary"><?= $opportunity['type'] ?></strong>
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
                    <div class="step-card text-center p-4">
                        <div class="step-number mb-3">
                            <span class="badge bg-primary fs-4">4</span>
                        </div>
                        <h5 class="step-title mb-3">Tuma Maombi</h5>
                        <p class="step-description text-muted">Tuma maombi yako na subiri majibu</p>
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