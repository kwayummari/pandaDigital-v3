<?php
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
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e67e22;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 20px;
        }

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

        .course-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .course-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-footer {
            margin-top: auto;
        }

        .btn-enroll {
            background: var(--success-color);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-enroll:hover {
            background: #229954;
            transform: translateY(-1px);
        }

        .btn-continue {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-continue:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-paid {
            background: var(--warning-color);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-paid:hover {
            background: #d68910;
            transform: translateY(-1px);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #d35400);
            color: white;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #138496);
        }

        .badge-free {
            background: var(--success-color);
            color: white;
        }

        .badge-paid {
            background: var(--warning-color);
            color: white;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/user/dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>
                Panda Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/user/dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
                <a class="nav-link active" href="/user/courses.php">
                    <i class="fas fa-book me-1"></i> Kozi
                </a>
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Toka
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-book text-primary me-2"></i>
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
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo count($courses); ?></h3>
                            <p class="mb-0">Jumla ya Kozi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-user-graduate fa-2x mb-2"></i>
                            <h3 class="mb-1"><?php echo count($enrolledCourses); ?></h3>
                            <p class="mb-0">Kozi Zilizosajiliwa</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-play-circle fa-2x mb-2"></i>
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
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
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
                                <i class="fas fa-th me-2"></i> Kozi Zote (<?php echo count($courses); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="enrolled-tab" data-bs-toggle="pill" data-bs-target="#enrolled" type="button" role="tab">
                                <i class="fas fa-user-graduate me-2"></i> Zilizosajiliwa (<?php echo count($enrolledCourses); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="available-tab" data-bs-toggle="pill" data-bs-target="#available" type="button" role="tab">
                                <i class="fas fa-plus-circle me-2"></i> Zilizopo (<?php echo count($courses) - count($enrolledCourses); ?>)
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
                                        <i class="fas fa-graduation-cap"></i>
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
                                            <a href="/user/course.php?id=<?php echo $course['id']; ?>"
                                                class="btn btn-continue text-white w-100">
                                                <i class="fas fa-play me-2"></i>
                                                Endelea Kusoma
                                            </a>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="enroll_course" class="btn btn-enroll text-white w-100">
                                                    <i class="fas fa-plus me-2"></i>
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
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5>Hujajisajili kwenye kozi yoyote bado!</h5>
                            <p class="text-muted">Jisajili kwenye kozi moja au zaidi ili uanze kujifunza.</p>
                            <a href="#available" class="btn btn-primary" data-bs-toggle="pill">
                                <i class="fas fa-search me-2"></i>
                                Tazama Kozi Zilizopo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($enrolledCourses as $course): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card course-card">
                                        <div class="course-image">
                                            <i class="fas fa-graduation-cap"></i>
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
                                                    <div class="progress-bar bg-success" style="width: 0%"></div>
                                                </div>
                                            </div>

                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="text-primary fw-bold"><?php echo $course['total_videos']; ?></div>
                                                    <small class="text-muted">Masomo</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-success fw-bold"><?php echo $course['total_questions']; ?></div>
                                                    <small class="text-muted">Maswali</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer course-footer bg-transparent border-0">
                                            <a href="/user/course.php?id=<?php echo $course['id']; ?>"
                                                class="btn btn-continue text-white w-100">
                                                <i class="fas fa-play me-2"></i>
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
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Umesajili kwenye kozi zote zilizopo!</h5>
                            <p class="text-muted">Hongera! Umesajili kwenye kozi zote. Endelea kujifunza na uendelee na maendeleo yako.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($availableCourses as $course): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card course-card">
                                        <div class="course-image">
                                            <i class="fas fa-graduation-cap"></i>
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
                                                <div class="col-6">
                                                    <div class="text-success fw-bold"><?php echo $course['total_questions']; ?></div>
                                                    <small class="text-muted">Maswali</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer course-footer bg-transparent border-0">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="enroll_course" class="btn btn-enroll text-white w-100">
                                                    <i class="fas fa-plus me-2"></i>
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
</body>

</html>