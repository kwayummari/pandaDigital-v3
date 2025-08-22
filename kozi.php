<?php
require_once 'config/init.php';
require_once 'models/Course.php';

// Initialize Course model
$courseModel = new Course();

// Fetch data from database using the model
$featuredCourses = $courseModel->getFeaturedCourses(8);

// Debug: Check what we got
echo "<!-- Debug: Featured courses count: " . count($featuredCourses) . " -->";
if (!empty($featuredCourses)) {
    echo "<!-- Debug: First course data: " . json_encode($featuredCourses[0]) . " -->";
} else {
    echo "<!-- Debug: No courses returned from database -->";
}

// If no courses found, use fallback data to show the design
if (empty($featuredCourses)) {
    echo "<!-- Debug: Using fallback data because database returned empty -->";
    $featuredCourses = [
        [
            'id' => 1,
            'name' => 'Ujuzi wa Msingi wa Kompyuta',
            'description' => 'Jifunze ujuzi wa msingi wa kompyuta na jinsi ya kutumia programu muhimu za ofisi.',
            'photo' => 'images/courses/basic-computer.jpg',
            'date_created' => '2024-01-15'
        ],
        [
            'id' => 2,
            'name' => 'Biashara ya Instagram',
            'description' => 'Jifunze jinsi ya kuanza na kuendeleza biashara kwa kutumia Instagram.',
            'photo' => 'images/courses/instagram-business.jpg',
            'date_created' => '2024-01-20'
        ],
        [
            'id' => 3,
            'name' => 'Ufanyaji wa Video',
            'description' => 'Jifunze jinsi ya kutengeneza video za kujifunza na za biashara.',
            'photo' => 'images/courses/video-making.jpg',
            'date_created' => '2024-01-25'
        ]
    ];
}

$pageTitle = 'Kozi Zote - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Kozi Zote</h1>
                <p class="page-subtitle">Jifunze ujuzi wa kidijitali na biashara ya mtandaoni kutoka kwa wataalamu wenye uzoefu</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Kozi</li>
                    </ol>
                </nav>
            </div>
            <!-- <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <i class="fas fa-graduation-cap fa-4x text-white"></i>
                </div>
            </div> -->
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="featured-courses-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Kozi Zilizochaguliwa</h2>
                <p class="section-subtitle">Kozi bora zaidi zilizochaguliwa na wataalamu wetu</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredCourses)): ?>
                <?php
                echo "<!-- Debug: Displaying " . count($featuredCourses) . " courses -->";
                foreach ($featuredCourses as $index => $course):
                    echo "<!-- Debug Course $index: " . htmlspecialchars($course['name']) . " -->";
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; border: 1px solid #e2e8f0;">
                            <div style="height: 300px; overflow: hidden; position: relative;">
                                <?php if (!empty($course['photo'])): ?>
                                    <img src="<?= $courseModel->getImageUrl($course['photo']) ?>" alt="<?= htmlspecialchars($course['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= asset('images/courses/default-course.jpg') ?>" alt="Default Course Image" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-2">
                                    <span class="badge bg-primary me-2">Kozi</span>
                                    <span class="badge bg-secondary"><?= $courseModel->formatDate($course['date_created']) ?></span>
                                </div>
                                <h5 class="card-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: #1e293b;">
                                    <?= htmlspecialchars($course['name']) ?>
                                </h5>
                                <p class="card-text text-muted mb-3" style="color: #64748b;">
                                    <?= $courseModel->truncateText($course['description'] ?? 'Maelezo ya kozi hayajapatikana.') ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i><?= $courseModel->formatDate($course['date_created']) ?>
                                    </small>
                                    <a href="course-details.php?id=<?= $course['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-graduation-cap me-1"></i>Jisajili Sasa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Hakuna kozi zilizopatikana kwa sasa.</p>
                </div>
            <?php endif; ?>
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

<!-- How to Learn Section -->
<section class="how-to-learn-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Jinsi Ya Kujifunza</h2>
                <p class="section-subtitle">Fuata hatua hizi rahisi kujifunza na kuendelea na maendeleo yako</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">1</span>
                    </div>
                    <h5 class="step-title mb-3">Chagua Kozi</h5>
                    <p class="step-description text-muted">Chagua kozi inayokufaa zaidi kutoka kwenye orodha yetu</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">2</span>
                    </div>
                    <h5 class="step-title mb-3">Jisajili</h5>
                    <p class="step-description text-muted">Jisajili kwenye kozi na uanze kujifunza</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">3</span>
                    </div>
                    <h5 class="step-title mb-3">Jifunza</h5>
                    <p class="step-description text-muted">Jifunza kwa makini na ufanye mazoezi</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">
                        <span class="badge bg-primary fs-4">4</span>
                    </div>
                    <h5 class="step-title mb-3">Pata Vyeti</h5>
                    <p class="step-description text-muted">Maliza kozi na upate vyeti vyako</p>
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
                <h2 class="cta-title">Je, Una Kozi Unayotaka Kufundisha?</h2>
                <p class="cta-subtitle">Tunaweza kukusaidia kufundisha kozi yako na kusaidia wanawake wengine</p>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <a href="#" class="btn btn-light btn-lg">
                    <i class="fas fa-plus me-2"></i>Ongeza Kozi
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    /* Course card specific styles */
    .course-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e2e8f0;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.05);
    }

    .course-overlay {
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
        transition: all 0.3s ease;
    }

    .course-card:hover .course-overlay {
        opacity: 1;
    }

    .course-content {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .course-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .course-description {
        flex: 1;
        margin-bottom: 1rem;
        color: #64748b;
    }

    /* Link styling - remove underlines */
    .course-card a {
        text-decoration: none;
        color: inherit;
        transition: color 0.3s ease;
    }

    .course-card a:hover {
        color: var(--primary-color, #ffbc3b);
        text-decoration: none;
    }

    .course-title a {
        color: #1e293b;
        text-decoration: none;
    }

    .course-title a:hover {
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
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .step-number .badge {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step-title {
        color: #333;
        font-weight: 600;
    }

    .step-description {
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* CTA section styles */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, var(--secondary-color, #5f4594) 100%);
        color: white;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }
</style>