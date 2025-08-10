<?php
require_once 'config/init.php';
require_once 'models/Course.php';

// Initialize Course model
$courseModel = new Course();

// Fetch data from database using the model
$courseCategories = $courseModel->getCourseCategories();
$featuredCourses = $courseModel->getFeaturedCourses(8);

// If no courses found, use fallback data
if (empty($featuredCourses)) {
    $featuredCourses = [
        [
            'id' => 1,
            'name' => 'Ujuzi wa Msingi wa Kompyuta',
            'category_name' => 'Teknolojia',
            'difficulty_level' => 'beginner',
            'estimated_duration' => '8 Wiki',
            'price' => 0,
            'photo' => 'images/courses/basic-computer.jpg',
            'average_rating' => 4.8,
            'total_enrollments' => 156,
            'description' => 'Jifunze ujuzi wa msingi wa kompyuta na jinsi ya kutumia programu muhimu za ofisi.'
        ],
        [
            'id' => 2,
            'name' => 'Biashara ya Instagram',
            'category_name' => 'Masoko',
            'difficulty_level' => 'intermediate',
            'estimated_duration' => '6 Wiki',
            'price' => 75000,
            'photo' => 'images/courses/instagram-business.jpg',
            'average_rating' => 4.9,
            'total_enrollments' => 89,
            'description' => 'Jifunze jinsi ya kuanza na kuendeleza biashara kwa kutumia Instagram.'
        ],
        [
            'id' => 3,
            'name' => 'Ujuzi wa Msingi wa Kompyuta',
            'category_name' => 'Teknolojia',
            'difficulty_level' => 'beginner',
            'estimated_duration' => '8 Wiki',
            'price' => 0,
            'photo' => 'images/courses/basic-computer.jpg',
            'average_rating' => 4.8,
            'total_enrollments' => 156,
            'description' => 'Jifunze ujuzi wa msingi wa kompyuta na jinsi ya kutumia programu muhimu za ofisi.'
        ]
    ];
}

$pageTitle = 'Kozi Zote - ' . $appConfig['name'];

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);"></div>
    <div class="container" style="position: relative; z-index: 2;">
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
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="featured-courses-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center section-title justify-content-between">
                    <h2 class="mb-0 text-nowrap mr-3">Kozi Zote</h2>
                    <div class="border-top w-100 border-primary d-none d-sm-block"></div>
                    <div>
                        <a href="" class="btn btn-sm btn-outline-primary ml-sm-3 d-none d-sm-block">Ona Zote</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php if (!empty($featuredCourses)): ?>
                <?php foreach ($featuredCourses as $index => $course): ?>
                    <div class="col-lg-4 col-sm-6 mb-5" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <div class="course-card hover-shadow h-100">
                            <div class="course-image-container">
                                <?php if (!empty($course['photo'])): ?>
                                    <img class="course-image"
                                        src="<?= $courseModel->getImageUrl($course['photo']) ?>"
                                        alt="<?= htmlspecialchars($course['name']) ?>">
                                <?php else: ?>
                                    <img class="course-image"
                                        src="<?= asset('images/courses/default-course.jpg') ?>"
                                        alt="Default Course Image">
                                <?php endif; ?>
                            </div>
                            <div class="course-content">
                                <ul class="list-inline mb-2">
                                    <li class="list-inline-item">
                                        <i class="fas fa-calendar mr-1 text-color"></i>
                                        <?= $courseModel->formatDate($course['date_created']) ?>
                                    </li>
                                    <li class="list-inline-item">
                                        <a class="text-color" href="<?= app_url() ?>">Panda Digital</a>
                                    </li>
                                </ul>
                                <a href="https://pandadigital.co.tz/admin/spo/routes/AboutCourse/?id=<?= $course['id'] ?>">
                                    <h4 class="course-title">Fahamu Kuhusu <?= htmlspecialchars($course['name']) ?></h4>
                                </a>
                                <p class="course-description mb-4">
                                    <?= $courseModel->truncateText($course['description']) ?>
                                </p>
                                <a href="https://pandadigital.co.tz/admin/spo/routes/AboutCourse/?id=<?= $course['id'] ?>"
                                    class="btn btn-primary btn-sm">Jiunge na kozi</a>
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

        <div class="row">
            <div class="col-12 text-center">
                <a href="" class="btn btn-sm btn-outline-primary d-sm-none d-inline-block">Ona Zote</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5" style="background-image: url('<?= asset('images/backgrounds/success-story.jpg') ?>'); background-size: cover; background-position: center; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="text-white mb-4">Tayari kujifunza?</h2>
                <p class="text-white mb-4">Jiunge na kozi zetu na uende mbele katika kazi yako. Panda Digital inakupa fursa ya kujifunza kutoka kwa wataalamu wenye uzoefu.</p>
                <a href="#featured-courses" class="btn btn-primary btn-lg">Anza Sasa</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    /* Course image sizing for consistent appearance */
    .course-image-container {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
    }

    .course-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        transition: transform 0.3s ease;
    }

    .course-image:hover {
        transform: scale(1.05);
    }

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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .course-image-container {
            height: 200px;
        }
    }

    @media (max-width: 576px) {
        .course-image-container {
            height: 180px;
        }
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

    /* Card hover effects */
    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    /* CTA section styles */
    .cta-section {
        color: white;
    }

    .cta-section h2 {
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
</style>