<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();
$quizModel = new Quiz();

// Get user's enrolled courses with progress
$enrolledCourses = $courseModel->getUserEnrolledCourses($currentUser['id']);

// Process each course to determine certificate eligibility
$certificates = [];
$totalCertificates = 0;
$issuedCertificates = 0;
$pendingCertificates = 0;

foreach ($enrolledCourses as $course) {
    $courseId = $course['id'];

    // Get course progress and quiz scores
    $courseProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);
    $quizStats = $quizModel->getUserQuizStatsForCourse($currentUser['id'], $courseId);

    // Determine certificate eligibility
    $isCompleted = $courseProgress['completion_percentage'] >= 100;
    $hasPassingScore = $quizStats['average_score'] >= 70;
    $certificateEligible = $isCompleted && $hasPassingScore;

    // Calculate accuracy percentage
    $accuracyPercentage = $quizStats['average_score'] ?? 0;

    // Determine status
    if ($certificateEligible) {
        $status = 'issued';
        $issuedCertificates++;
        $totalCertificates++;
    } elseif ($isCompleted && !$hasPassingScore) {
        $status = 'pending_score';
        $pendingCertificates++;
        $totalCertificates++;
    } elseif (!$isCompleted) {
        $status = 'in_progress';
        $pendingCertificates++;
        $totalCertificates++;
    } else {
        $status = 'not_eligible';
        continue;
    }

    // Generate certificate number if eligible
    $certificateNumber = $certificateEligible ? 'CERT-' . str_pad($courseId, 3, '0', STR_PAD_LEFT) . '-' . date('Y') : null;

    $certificates[] = [
        'id' => $courseId,
        'course_name' => $course['name'],
        'completion_date' => $courseProgress['last_activity'] ?? null,
        'issue_date' => $certificateEligible ? date('Y-m-d') : null,
        'grade' => calculateGrade($accuracyPercentage),
        'certificate_number' => $certificateNumber,
        'status' => $status,
        'completion_percentage' => $courseProgress['completion_percentage'],
        'accuracy_percentage' => $accuracyPercentage,
        'total_questions' => $quizStats['total_questions'] ?? 0,
        'answered_questions' => $quizStats['questions_answered'] ?? 0
    ];
}

// Sort certificates by completion date (most recent first)
usort($certificates, function ($a, $b) {
    if ($a['completion_date'] && $b['completion_date']) {
        return strtotime($b['completion_date']) - strtotime($a['completion_date']);
    }
    return 0;
});

// Helper function to calculate grade
function calculateGrade($score)
{
    if ($score >= 90) return 'A+';
    if ($score >= 80) return 'A';
    if ($score >= 70) return 'B+';
    if ($score >= 60) return 'B';
    if ($score >= 50) return 'C+';
    if ($score >= 40) return 'C';
    if ($score >= 30) return 'D';
    return 'F';
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyeti Vyagu - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        /* Additional styles for certificates page */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .stats-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stats-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .certificate-card {
            border: 1px solid var(--border-color);
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .certificate-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .certificate-header {
            background: #f8f9fa;
            border-bottom: 1px solid var(--border-color);
            border-radius: 14px 14px 0 0;
            padding: 20px;
            text-align: center;
        }

        .certificate-body {
            padding: 25px;
        }

        .certificate-number {
            background: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .grade-badge {
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.2rem;
            display: inline-block;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-issued {
            background: var(--success-color);
            color: white;
        }

        .status-pending_score {
            background: var(--warning-color);
            color: white;
        }

        .status-in_progress {
            background: var(--info-color);
            color: white;
        }

        .download-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .download-btn:disabled {
            background: var(--gray-color);
            cursor: not-allowed;
        }

        .progress-bar {
            height: 8px;
            border-radius: 10px;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .progress-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .progress-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.3;
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            margin-top: 1rem;
            font-weight: 600;
            color: var(--gray-color);
        }

        .empty-state p {
            color: var(--gray-color);
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Vyeti Vyagu';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Vyeti Vyagu
                        </h1>
                        <p class="text-muted">Tazama vyeti vyote ulivyopata kwa kukamilisha kozi</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo $totalCertificates; ?></h3>
                                <p class="mb-0">Jumla ya Kozi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo $issuedCertificates; ?></h3>
                                <p class="mb-0">Vyeti Vilivyotolewa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h3 class="mb-1"><?php echo $pendingCertificates; ?></h3>
                                <p class="mb-0">Vyeti Vinasubiri</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($certificates)): ?>
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h5>Huna kozi zilizosajiliwa bado</h5>
                        <p>Jisajili kwenye kozi na ukamilishe ili upate vyeti vyako</p>
                        <a href="<?= app_url('user/courses.php') ?>" class="btn btn-primary">
                            Tazama Kozi
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Certificates Grid -->
                    <div class="row">
                        <?php foreach ($certificates as $certificate): ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="certificate-card">
                                    <div class="certificate-header">
                                        <h5 class="mb-2"><?php echo htmlspecialchars($certificate['course_name']); ?></h5>
                                        <?php if ($certificate['certificate_number']): ?>
                                            <div class="certificate-number">
                                                <?php echo htmlspecialchars($certificate['certificate_number']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="certificate-body">
                                        <!-- Progress Information -->
                                        <div class="mb-3">
                                            <div class="progress-info">
                                                <span class="progress-label">Maendeleo ya Kozi</span>
                                                <span class="progress-value"><?php echo round($certificate['completion_percentage'], 1); ?>%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: <?php echo $certificate['completion_percentage']; ?>%"
                                                    aria-valuenow="<?php echo $certificate['completion_percentage']; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Maswali Yaliyojibiwa</small>
                                                <div class="fw-bold"><?php echo $certificate['answered_questions']; ?>/<?php echo $certificate['total_questions']; ?></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Alama</small>
                                                <div class="grade-badge"><?php echo htmlspecialchars($certificate['grade']); ?></div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Usahihi</small>
                                                <div class="fw-bold"><?php echo round($certificate['accuracy_percentage'], 1); ?>%</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Hali</small>
                                                <div>
                                                    <span class="status-badge status-<?php echo $certificate['status']; ?>">
                                                        <?php
                                                        if ($certificate['status'] == 'issued') {
                                                            echo 'Imetolewa';
                                                        } elseif ($certificate['status'] == 'pending_score') {
                                                            echo 'Inahitaji Alama';
                                                        } elseif ($certificate['status'] == 'in_progress') {
                                                            echo 'Inaendelea';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <?php if ($certificate['status'] == 'issued'): ?>
                                                <a href="<?= app_url('user/download-certificate.php?course_id=' . $certificate['id']) ?>" class="btn download-btn">
                                                    Pakua Vyeti
                                                </a>
                                            <?php elseif ($certificate['status'] == 'pending_score'): ?>
                                                <button class="btn download-btn" disabled>
                                                    Unahitaji 70%+
                                                </button>
                                            <?php else: ?>
                                                <button class="btn download-btn" disabled>
                                                    Inaendelea
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


</body>

</html>