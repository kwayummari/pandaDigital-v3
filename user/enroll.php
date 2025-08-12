<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

// Set page title for navigation
$page_title = 'Jisajili Kwenye Kozi';

// Get course ID from URL
$courseId = $_GET['course_id'] ?? null;

if (!$courseId) {
    header('Location: ' . app_url('user/courses.php') . '?error=invalid_course');
    exit();
}

// Get course information
$course = $courseModel->getCourseById($courseId, $currentUser['id']);
if (!$course) {
    header('Location: ' . app_url('user/courses.php') . '?error=course_not_found');
    exit();
}

// Check if already enrolled
$isEnrolled = $courseModel->isUserEnrolled($currentUser['id'], $courseId);
if ($isEnrolled) {
    header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&message=already_enrolled');
    exit();
}

// Handle enrollment
if ($_POST && isset($_POST['confirm_enrollment'])) {
    $enrollmentResult = $courseModel->enrollUser($currentUser['id'], $courseId);

    if ($enrollmentResult) {
        header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&message=enrollment_success');
        exit();
    } else {
        $error = "Imefeli kujisajili kwenye kozi. Tafadhali jaribu tena.";
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jisajili Kwenye Kozi - <?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/style.css'); ?>?v=8">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="content-card">
                            <div class="text-center mb-4">
                                <h2 class="card-title">Jisajili Kwenye Kozi</h2>
                                <p class="text-muted">Thibitisha usajili wako kwenye kozi hii</p>
                            </div>

                            <!-- Messages -->
                            <?php if (isset($_GET['message'])): ?>
                                <?php if ($_GET['message'] === 'enrollment_success'): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Hongera!</strong> Umesajili kwenye kozi hii kwa mafanikio.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Course Info -->
                            <div class="course-preview mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="course-image-small">
                                            <?php if (!empty($course['photo'])): ?>
                                                <img src="<?php echo app_url($courseModel->getImageUrl($course['photo'])); ?>"
                                                    alt="<?php echo htmlspecialchars($course['name']); ?>"
                                                    class="img-fluid rounded"
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="course-placeholder-small d-flex align-items-center justify-content-center bg-light rounded"
                                                    style="width: 100px; height: 100px;">
                                                    <i class="fas fa-book text-muted" style="font-size: 24px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <h4 class="mb-2"><?php echo htmlspecialchars($course['name']); ?></h4>
                                        <p class="text-muted mb-2">
                                            <?php echo htmlspecialchars(substr($course['description'] ?? 'Maelezo ya kozi hayajapatikana.', 0, 150)) . '...'; ?>
                                        </p>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold text-primary"><?php echo $course['total_videos']; ?></div>
                                                <small class="text-muted">Masomo</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success"><?php echo $course['total_questions']; ?></div>
                                                <small class="text-muted">Maswali</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-info"><?php echo $course['total_students'] ?? 0; ?></div>
                                                <small class="text-muted">Wanafunzi</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Enrollment Form -->
                            <form method="POST">
                                <div class="enrollment-details mb-4">
                                    <h5 class="mb-3">Maelezo ya Usajili</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Mwanafunzi:</strong> <?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['email']); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Tarehe:</strong> <?php echo date('d/m/Y'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Bei:</strong> Bure
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong>Muda:</strong> Maisha
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="enrollment-benefits mb-4">
                                    <h5 class="mb-3">Faida za Usajili</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Ufikiaji wa video zote za mafunzo
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Maswali ya majaribio na mazoezi
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Ufikiaji wa maisha kwenye kozi
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>
                                            Uwezo wa kufuatilia maendeleo yako
                                        </li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="confirm_enrollment" class="btn btn-primary btn-lg">
                                        Thibitisha Usajili
                                    </button>
                                    <a href="<?php echo app_url('user/course-overview.php'); ?>?id=<?php echo $courseId; ?>"
                                        class="btn btn-outline-secondary">
                                        Rudi
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }
        });
    </script>
</body>

</html>