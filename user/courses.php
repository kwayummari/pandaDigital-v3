<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

// Get all courses with enrollment status
$courses = $courseModel->getAllCourses($currentUser['id']);

// Handle course enrollment
if ($_POST && isset($_POST['enroll_course'])) {
    $courseId = $_POST['course_id'];

    if ($courseModel->enrollUser($currentUser['id'], $courseId)) {
        $success = "Umefanikiwa kujisajili kwenye kozi hii!";
        // Refresh courses list
        $courses = $courseModel->getAllCourses($currentUser['id']);
    } else {
        $error = "Imefeli kujisajili kwenye kozi. Tafadhali jaribu tena.";
    }
}

// Get user's enrolled courses
$enrolledCourses = $courseModel->getUserEnrolledCourses($currentUser['id']);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kozi Zilizopo - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        /* Additional course-specific styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .course-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-footer {
            margin-top: auto;
        }

        .filter-tabs .nav-link {
            border: none;
            color: var(--primary-color);
            font-weight: 600;
            border-radius: 25px;
            margin-right: 10px;
        }

        .filter-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container courses-page">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Kozi Zangu';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Kozi Zilizopo
                        </h1>
                        <p class="text-muted">Jifunze na uendelee na maendeleo yako</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo count($courses); ?></h3>
                                <p class="mb-0">Jumla ya Kozi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card success">
                            <div class="card-body text-center">
                                <h3 class="mb-1"><?php echo count($enrolledCourses); ?></h3>
                                <p class="mb-0">Kozi Zilizosajiliwa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card info">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php
                                    $totalLessons = 0;
                                    foreach ($enrolledCourses as $course) {
                                        $totalLessons += $course['total_videos'];
                                    }
                                    echo $totalLessons;
                                    ?>
                                </h3>
                                <p class="mb-0">Jumla ya Masomo</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills filter-tabs" id="courseTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                                    Kozi Zote (<?php echo count($courses); ?>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="enrolled-tab" data-bs-toggle="pill" data-bs-target="#enrolled" type="button" role="tab">
                                    Zilizosajiliwa (<?php echo count($enrolledCourses); ?>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="available-tab" data-bs-toggle="pill" data-bs-target="#available" type="button" role="tab">
                                    Zilizopo (<?php echo count($courses) - count($enrolledCourses); ?>)
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Course Tabs Content -->
                <div class="tab-content" id="courseTabsContent">
                    <!-- All Courses Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card course-card">
                                        <div class="course-image">
                                            <?php if (!empty($course['photo'])): ?>
                                                <img src="<?= app_url($courseModel->getImageUrl($course['photo'])) ?>"
                                                    alt="<?= htmlspecialchars($course['name']) ?>"
                                                    class="img-fluid w-100 h-100 object-fit-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="course-placeholder d-flex align-items-center justify-content-center" style="display: none;">
                                                    <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="course-placeholder d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body course-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($course['name']); ?></h5>
                                                <?php if ($course['courseIsPaidStatusId'] == 1): ?>
                                                    <span class="badge badge-paid">Kulipwa</span>
                                                <?php else: ?>
                                                    <span class="badge badge-free">Bure</span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="card-text text-muted">
                                                <?php
                                                if (!empty($course['description'])) {
                                                    echo htmlspecialchars(substr($course['description'], 0, 100)) . '...';
                                                } else {
                                                    echo 'Maelezo ya kozi hayajapatikana.';
                                                }
                                                ?>
                                            </p>

                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="text-primary fw-bold"><?php echo $course['total_videos']; ?></div>
                                                    <small class="text-muted">Masomo</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-success fw-bold"><?php echo $course['total_questions']; ?></div>
                                                    <small class="text-muted">Maswali</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-info fw-bold">
                                                        <?php echo $course['total_students'] ?? 0; ?>
                                                    </div>
                                                    <small class="text-muted">Wanafunzi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer course-footer bg-transparent border-0">
                                            <?php if ($course['is_enrolled']): ?>
                                                <a href="<?= app_url('user/course.php?id=' . $course['id']) ?>"
                                                    class="btn btn-continue text-white w-100">
                                                    Endelea Kusoma
                                                </a>
                                            <?php else: ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                    <button type="submit" name="enroll_course" class="btn btn-enroll text-white w-100">
                                                        Jisajili
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Enrolled Courses Tab -->
                    <div class="tab-pane fade" id="enrolled" role="tabpanel">
                        <?php if (empty($enrolledCourses)): ?>
                            <div class="text-center py-5">
                                <h5>Hujajisajili kwenye kozi yoyote bado!</h5>
                                <p class="text-muted">Jisajili kwenye kozi moja au zaidi ili uanze kujifunza.</p>
                                <a href="#available" class="btn btn-primary" data-bs-toggle="pill">
                                    Tazama Kozi Zilizopo
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card course-card">
                                            <div class="course-image">
                                                <?php if (!empty($course['photo'])): ?>
                                                    <img src="<?= app_url($courseModel->getImageUrl($course['photo'])) ?>"
                                                        alt="<?= htmlspecialchars($course['name']) ?>"
                                                        class="img-fluid w-100 h-100 object-fit-cover"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="course-placeholder d-flex align-items-center justify-content-center" style="display: none;">
                                                        <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="course-placeholder d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body course-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($course['name']); ?></h5>
                                                    <span class="badge bg-success">Imejisajili</span>
                                                </div>

                                                <p class="card-text text-muted">
                                                    <?php
                                                    if (!empty($course['description'])) {
                                                        echo htmlspecialchars(substr($course['description'], 0, 100)) . '...';
                                                    } else {
                                                        echo 'Maelezo ya kozi hayajapatikana.';
                                                    }
                                                    ?>
                                                </p>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small class="text-muted">Maendeleo</small>
                                                        <small class="text-muted">
                                                            <?php
                                                            // This would need to be calculated based on completed lessons
                                                            echo "0%"; // Placeholder
                                                            ?>
                                                        </small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 0%; background-color: var(--primary-color);"></div>
                                                    </div>
                                                </div>

                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="fw-bold" style="color: var(--primary-color);"><?php echo $course['total_videos']; ?></div>
                                                        <small class="text-muted">Masomo</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="fw-bold" style="color: var(--secondary-color);"><?php echo $course['total_questions']; ?></div>
                                                        <small class="text-muted">Maswali</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-footer course-footer bg-transparent border-0">
                                                <a href="<?= app_url('user/course.php?id=' . $course['id']) ?>"
                                                    class="btn btn-continue text-white w-100">
                                                    Endelea Kusoma
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Available Courses Tab -->
                    <div class="tab-pane fade" id="available" role="tabpanel">
                        <?php
                        $availableCourses = array_filter($courses, function ($course) {
                            return !$course['is_enrolled'];
                        });
                        ?>

                        <?php if (empty($availableCourses)): ?>
                            <div class="text-center py-5">
                                <h5>Umesajili kwenye kozi zote zilizopo!</h5>
                                <p class="text-muted">Hongera! Umesajili kwenye kozi zote. Endelea kujifunza na uendelee na maendeleo yako.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($availableCourses as $course): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card course-card">
                                            <div class="course-image">
                                                <?php if (!empty($course['photo'])): ?>
                                                    <img src="<?= app_url($courseModel->getImageUrl($course['photo'])) ?>"
                                                        alt="<?= htmlspecialchars($course['name']) ?>"
                                                        class="img-fluid w-100 h-100 object-fit-cover"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="course-placeholder d-flex align-items-center justify-content-center" style="display: none;">
                                                        <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="course-placeholder d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body course-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($course['name']); ?></h5>
                                                    <?php if ($course['courseIsPaidStatusId'] == 1): ?>
                                                        <span class="badge badge-paid">Kulipwa</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-free">Bure</span>
                                                    <?php endif; ?>
                                                </div>

                                                <p class="card-text text-muted">
                                                    <?php
                                                    if (!empty($course['description'])) {
                                                        echo htmlspecialchars(substr($course['description'], 0, 100)) . '...';
                                                    } else {
                                                        echo 'Maelezo ya kozi hayajapatikana.';
                                                    }
                                                    ?>
                                                </p>

                                                <div class="row text-center mb-3">
                                                    <div class="col-4">
                                                        <div class="fw-bold" style="color: var(--primary-color);"><?php echo $course['total_videos']; ?></div>
                                                        <small class="text-muted">Masomo</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="fw-bold" style="color: var(--secondary-color);"><?php echo $course['total_questions']; ?></div>
                                                        <small class="text-muted">Maswali</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-footer course-footer bg-transparent border-0">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                    <button type="submit" name="enroll_course" class="btn btn-enroll text-white w-100">
                                                        Jisajili
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Sidebar Toggle Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
                const sidebar = document.querySelector('.sidebar');
                const dashboardContainer = document.querySelector('.dashboard-container');

                sidebarToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        sidebar.classList.toggle('collapsed');
                        dashboardContainer.classList.toggle('sidebar-collapsed');
                    });
                });

                // Close sidebar on mobile when clicking outside
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
                        if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                            sidebar.classList.remove('collapsed');
                            dashboardContainer.classList.remove('sidebar-collapsed');
                        }
                    }
                });
            });
        </script>
</body>

</html>