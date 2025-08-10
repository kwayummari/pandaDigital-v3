<?php
require_once 'config/init.php';

// Get news categories and articles from database
$newsCategories = [
    [
        'id' => 1,
        'name' => 'Biashara',
        'icon' => 'fas fa-store',
        'description' => 'Habari za biashara na ujasiriamali',
        'color' => 'primary',
        'count' => 23
    ],
    [
        'id' => 2,
        'name' => 'Kidijitali',
        'icon' => 'fas fa-laptop-code',
        'description' => 'Teknolojia na ujuzi wa kidijitali',
        'color' => 'success',
        'count' => 18
    ],
    [
        'id' => 3,
        'name' => 'Ufadhili',
        'icon' => 'fas fa-hand-holding-usd',
        'description' => 'Mikopo, ufadhili na tuzo',
        'color' => 'info',
        'count' => 15
    ],
    [
        'id' => 4,
        'name' => 'Maendeleo',
        'icon' => 'fas fa-chart-line',
        'description' => 'Maendeleo ya kiuchumi na kijamii',
        'color' => 'warning',
        'count' => 12
    ]
];

$featuredArticles = [
    [
        'id' => 1,
        'title' => 'Jinsi Ya Kuanza Biashara Ya Mtandaoni Kwa Wanawake Tanzania',
        'category' => 'Biashara',
        'author' => 'Mama Sarah Mwambene',
        'date' => '2025-04-25',
        'read_time' => '5 Dakika',
        'image' => 'images/blog/online-business.jpg',
        'excerpt' => 'Jifunze jinsi ya kuanza na kuendeleza biashara ya mtandaoni kwa kutumia mitandao ya kijamii na teknolojia ya kidijitali.',
        'views' => 1247,
        'likes' => 89
    ],
    [
        'id' => 2,
        'title' => 'Ujuzi Wa Kidijitali Unaohitajika Kwa Wanawake Leo',
        'category' => 'Kidijitali',
        'author' => 'Mama Fatima Hassan',
        'date' => '2025-04-24',
        'read_time' => '7 Dakika',
        'image' => 'images/blog/digital-skills.jpg',
        'excerpt' => 'Ujuzi gani wa kidijitali unaohitajika zaidi kwa wanawake Tanzania kuendeleza biashara zao na kujipatia ajira?',
        'views' => 2156,
        'likes' => 156
    ],
    [
        'id' => 3,
        'title' => 'Mikopo Ya Biashara Kwa Wanawake: Njia Rahisi Za Kupata',
        'category' => 'Ufadhili',
        'author' => 'Mama Grace Mwakatobe',
        'date' => '2025-04-23',
        'read_time' => '6 Dakika',
        'image' => 'images/blog/business-loans.jpg',
        'excerpt' => 'Jifunze jinsi ya kupata mikopo ya biashara kwa urahisi na masharti mazuri kwa wanawake.',
        'views' => 1893,
        'likes' => 134
    ],
    [
        'id' => 4,
        'title' => 'Wanawake Tanzania Wanaendelea Kuongoza Kwenye Ujasiriamali',
        'category' => 'Maendeleo',
        'author' => 'Mama Amina Juma',
        'date' => '2025-04-22',
        'read_time' => '4 Dakika',
        'image' => 'images/blog/women-entrepreneurs.jpg',
        'excerpt' => 'Takwimu mpya zinaonyesha kuwa wanawake Tanzania wanaendelea kuongoza kwenye ujasiriamali na biashara.',
        'views' => 1678,
        'likes' => 98
    ]
];

$pageTitle = 'Habari Zote - ' . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Soma habari mpya za biashara, kidijitali, ufadhili na maendeleo. Panda Digital - Chanzo cha habari kwa wanawake Tanzania.">

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
                    <h1 class="page-title">Habari Zote</h1>
                    <p class="page-subtitle">Soma habari mpya za biashara, kidijitali, ufadhili na maendeleo zinazokufaa</p>
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

    <!-- News Categories -->
    <section class="categories-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Kategoria za Habari</h2>
                    <p class="section-subtitle">Chagua kategoria inayokufaa zaidi na uone habari zote zinazopatikana</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($newsCategories as $index => $category): ?>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="category-card text-center p-4 h-100">
                            <div class="category-icon mb-3">
                                <i class="<?= $category['icon'] ?> fa-3x text-<?= $category['color'] ?>"></i>
                            </div>
                            <h4 class="category-title mb-3"><?= $category['name'] ?></h4>
                            <p class="category-description text-muted"><?= $category['description'] ?></p>
                            <div class="category-count mb-3">
                                <span class="badge bg-<?= $category['color'] ?> fs-6"><?= $category['count'] ?> Makala</span>
                            </div>
                            <a href="#news-<?= $category['id'] ?>" class="btn btn-outline-<?= $category['color'] ?> mt-3">
                                Tazama Habari <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Articles -->
    <section class="featured-articles-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Makala Zilizochaguliwa</h2>
                    <p class="section-subtitle">Makala bora zaidi zilizochaguliwa na wasomaji wetu</p>
                </div>
            </div>

            <div class="row g-4">
                <?php foreach ($featuredArticles as $index => $article): ?>
                    <div class="col-lg-6 col-xl-3" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="article-card h-100">
                            <div class="article-image">
                                <img src="<?= asset($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="img-fluid">
                                <div class="article-overlay">
                                    <div class="article-actions">
                                        <a href="habari-details.php?id=<?= $article['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i>Soma Zaidi
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="article-content p-4">
                                <div class="article-meta mb-2">
                                    <span class="badge bg-primary me-2"><?= $article['category'] ?></span>
                                    <span class="badge bg-secondary"><?= $article['read_time'] ?></span>
                                </div>
                                <h5 class="article-title mb-3">
                                    <a href="habari-details.php?id=<?= $article['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($article['title']) ?></a>
                                </h5>
                                <p class="article-excerpt text-muted mb-3"><?= htmlspecialchars($article['excerpt']) ?></p>

                                <div class="article-author mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-2"></i><?= htmlspecialchars($article['author']) ?>
                                    </small>
                                </div>

                                <div class="article-details d-flex justify-content-between align-items-center mb-3">
                                    <div class="article-date">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?= date('d/m/Y', strtotime($article['date'])) ?>
                                        </small>
                                    </div>
                                    <div class="article-views">
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i><?= $article['views'] ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="article-footer d-flex justify-content-between align-items-center">
                                    <div class="article-likes">
                                        <small class="text-danger">
                                            <i class="fas fa-heart me-1"></i><?= $article['likes'] ?>
                                        </small>
                                    </div>
                                    <div class="article-read">
                                        <a href="habari-details.php?id=<?= $article['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-book-open me-1"></i>Soma Sasa
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
                        <i class="fas fa-th-list me-2"></i>Tazama Makala Zote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Subscription -->
    <section class="newsletter-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <div class="newsletter-card p-5">
                        <h3 class="newsletter-title mb-3">Jiunge na Jarida Letu</h3>
                        <p class="newsletter-subtitle mb-4">Pata habari mpya za biashara na fursa moja kwa moja kwenye barua pepe yako</p>

                        <form class="newsletter-form">
                            <div class="row g-3 justify-content-center">
                                <div class="col-md-8">
                                    <input type="email" class="form-control form-control-lg" placeholder="Weka barua pepe yako" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Jiunga
                                    </button>
                                </div>
                            </div>
                        </form>

                        <p class="newsletter-note mt-3">
                            <small class="text-muted">Hakuna spam. Unaweza kujiondoa wakati wowote.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News Ticker -->
    <section class="news-ticker-section py-4 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h6 class="mb-0">
                        <i class="fas fa-broadcast-tower me-2"></i>Habari Mpya
                    </h6>
                </div>
                <div class="col-md-9">
                    <div class="news-ticker">
                        <div class="ticker-content">
                            <span>🔥 Nafasi mpya za ajira zinazopatikana kwa wanawake Tanzania</span>
                            <span>💡 Mafunzo ya bure ya ujuzi wa kidijitali yanaanza wiki ijayo</span>
                            <span>🚀 Ufadhili mpya wa biashara kwa mjasiriamali mpya</span>
                            <span>📱 Teknolojia mpya ya biashara ya mtandaoni</span>
                        </div>
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
                    <h2 class="cta-title">Je, Una Habari Unayotaka Kushiriki?</h2>
                    <p class="cta-subtitle">Tunaweza kukusaidia kushiriki habari yako na wanawake wengine wanaohitaji</p>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <a href="#" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Ongeza Habari
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

        // News ticker animation
        document.addEventListener('DOMContentLoaded', function() {
            const ticker = document.querySelector('.ticker-content');
            if (ticker) {
                ticker.style.animation = 'ticker 20s linear infinite';
            }
        });
    </script>

    <style>
        @keyframes ticker {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .news-ticker {
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-content {
            display: inline-block;
            white-space: nowrap;
        }

        .ticker-content span {
            display: inline-block;
            margin-right: 50px;
        }
    </style>
</body>

</html>