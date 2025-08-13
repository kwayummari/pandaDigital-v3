<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

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

// Check if user is enrolled
$isEnrolled = $courseModel->isUserEnrolled($currentUser['id'], $courseId);
if (!$isEnrolled) {
    header('Location: ' . app_url('user/courses.php') . '?error=not_enrolled');
    exit();
}

// Check if user has completed the course
if (!$courseModel->hasCompletedCourse($currentUser['id'], $courseId)) {
    header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&error=course_not_completed');
    exit();
}

// Generate or get existing certificate
$certificateNumber = $courseModel->generateCertificate($currentUser['id'], $courseId);
if (!$certificateNumber) {
    header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&error=certificate_generation_failed');
    exit();
}

// Get course completion details
$userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);
$completionDate = date('Y-m-d');
$completionPercentage = round($userProgress['completion_percentage']);

// Set page title for navigation
$page_title = 'Pakua Vyeti';
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakua Vyeti - <?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

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
                                <h2 class="card-title">Hongera! Umeimaliza Kozi</h2>
                                <p class="text-muted">Umehitimu kozi hii na unaweza kupakua vyeti vyako</p>
                            </div>

                            <!-- Certificate Preview -->
                            <div class="certificate-preview mb-4">
                                <div class="certificate-container">
                                    <!-- Certificate Background Pattern -->
                                    <div class="certificate-bg-pattern"></div>

                                    <!-- Logo Watermark -->
                                    <div class="logo-watermark">
                                        <div class="logo-circle">
                                            <img src="<?php echo app_url('assets/images/logo.png'); ?>" alt="Panda Digital" class="watermark-logo">
                                        </div>
                                    </div>

                                    <!-- Certificate Header -->
                                    <div class="certificate-header">
                                        <div class="certificate-title">
                                            <h2>CERTIFICATE OF COMPLETION</h2>
                                            <p class="subtitle">Vyeti vya Hitimu</p>
                                        </div>
                                    </div>

                                    <!-- Certificate Body -->
                                    <div class="certificate-body">
                                        <div class="award-text">
                                            <p>This is to certify that</p>
                                            <h3 class="student-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h3>
                                            <p>has successfully completed the course:</p>
                                        </div>

                                        <div class="course-section">
                                            <h4 class="course-name"><?php echo htmlspecialchars($course['name']); ?></h4>
                                        </div>

                                        <div class="completion-details">
                                            <div class="detail-row">
                                                <div class="detail-item">
                                                    <span class="detail-label">Completion Rate:</span>
                                                    <span class="detail-value"><?php echo $completionPercentage; ?>%</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Date Completed:</span>
                                                    <span class="detail-value"><?php echo date('F j, Y', strtotime($completionDate)); ?></span>
                                                </div>
                                            </div>
                                            <div class="detail-row">
                                                <div class="detail-item">
                                                    <span class="detail-label">Certificate Number:</span>
                                                    <span class="detail-value certificate-number"><?php echo $certificateNumber; ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Learning Duration:</span>
                                                    <span class="detail-value"><?php echo $userProgress['total_questions']; ?> questions</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Certificate Footer -->
                                    <div class="certificate-footer">
                                        <div class="signature-section">
                                            <div class="signature-line"></div>
                                            <p class="signature-title">Training Manager</p>
                                            <p class="signature-name">Panda Digital Team</p>
                                        </div>

                                        <div class="certificate-validity">
                                            <p>This certificate is valid and can be used as proof of acquired skills and knowledge.</p>
                                            <p class="validity-note">This certificate is authentic and can be verified through our platform.</p>
                                        </div>
                                    </div>

                                    <!-- Certificate Border -->
                                    <div class="certificate-border">
                                        <div class="border-corner top-left"></div>
                                        <div class="border-corner top-right"></div>
                                        <div class="border-corner bottom-left"></div>
                                        <div class="border-corner bottom-right"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Summary -->
                            <div class="course-summary mb-4">
                                <h5 class="mb-3">Muhtasari wa Kozi</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="summary-item">
                                            <div class="summary-number"><?php echo $userProgress['total_questions']; ?></div>
                                            <div class="summary-label">Maswali Yote</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="summary-item">
                                            <div class="summary-number"><?php echo $userProgress['answered_questions']; ?></div>
                                            <div class="summary-label">Umejibu</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="summary-item">
                                            <div class="summary-number"><?php echo $completionPercentage; ?>%</div>
                                            <div class="summary-label">Umehitimu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Actions -->
                            <div class="download-actions">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-lg" onclick="downloadPDF()">
                                        Pakua Vyeti (PDF)
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="printCertificate()">
                                        Chapisha Vyeti
                                    </button>
                                    <a href="<?php echo app_url('user/course-overview.php'); ?>?id=<?php echo $courseId; ?>"
                                        class="btn btn-outline-secondary">
                                        Rudi kwenye Kozi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // Hide elements that shouldn't be in PDF
            const sidebar = document.querySelector('.sidebar');
            const topNavbar = document.querySelector('.top-navbar');
            const downloadActions = document.querySelector('.download-actions');
            const courseSummary = document.querySelector('.course-summary');

            if (sidebar) sidebar.style.display = 'none';
            if (topNavbar) topNavbar.style.display = 'none';
            if (downloadActions) downloadActions.style.display = 'none';
            if (courseSummary) courseSummary.style.display = 'none';

            // Get the certificate container
            const certificateContainer = document.querySelector('.certificate-container');

            if (!certificateContainer) {
                alert('Haikuweza kupata vyeti. Tafadhali jaribu tena.');
                return;
            }

            // PDF options
            const opt = {
                margin: 0,
                filename: 'vyeti_<?php echo htmlspecialchars($course['name']); ?>_<?php echo htmlspecialchars($currentUser['first_name']); ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // Generate PDF
            html2pdf().set(opt).from(certificateContainer).save().then(() => {
                // Show elements back after PDF generation
                if (sidebar) sidebar.style.display = 'block';
                if (topNavbar) topNavbar.style.display = 'block';
                if (downloadActions) downloadActions.style.display = 'block';
                if (courseSummary) courseSummary.style.display = 'block';
            }).catch(err => {
                console.error('Error generating PDF:', err);
                alert('Kulikuwa na tatizo kujenga PDF. Tafadhali jaribu tena au tumia "Chapisha Vyeti" badala yake.');

                // Show elements back on error
                if (sidebar) sidebar.style.display = 'block';
                if (topNavbar) topNavbar.style.display = 'block';
                if (downloadActions) downloadActions.style.display = 'block';
                if (courseSummary) courseSummary.style.display = 'block';
            });
        }

        function printCertificate() {
            // Hide elements for printing
            const sidebar = document.querySelector('.sidebar');
            const topNavbar = document.querySelector('.top-navbar');
            const downloadActions = document.querySelector('.download-actions');
            const courseSummary = document.querySelector('.course-summary');

            if (sidebar) sidebar.style.display = 'none';
            if (topNavbar) topNavbar.style.display = 'none';
            if (downloadActions) downloadActions.style.display = 'none';
            if (courseSummary) courseSummary.style.display = 'none';

            // Print
            window.print();

            // Show elements back after printing
            setTimeout(() => {
                if (sidebar) sidebar.style.display = 'block';
                if (topNavbar) topNavbar.style.display = 'block';
                if (downloadActions) downloadActions.style.display = 'block';
                if (courseSummary) courseSummary.style.display = 'block';
            }, 1000);
        }

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