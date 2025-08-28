<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../models/Quiz.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../includes/profile-check.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Check profile completion for course access
$userModel = new User($pdo);
$profileCompletionStatus = getProfileCompletionStatus($userModel, $currentUser['id']);

// If profile is not complete, require completion before accessing course
if (!$hasMinimumProfile($userModel, $currentUser['id'])) {
    requireProfileCompletion($userModel, $currentUser['id'], 'course-overview');
}
$courseModel = new Course();
$quizModel = new Quiz();

// Set page title for navigation
$page_title = 'Maelezo ya Kozi';

// Get course ID from URL
$courseId = $_GET['id'] ?? null;

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

// Get course statistics
$courseStats = $courseModel->getCourseStats($courseId);
$totalStudents = $courseStats['total_students'];
$totalLessons = $courseStats['total_lessons'];
$totalQuestions = $courseStats['total_questions'];

// Get course lessons (videos)
$courseLessons = $courseModel->getCourseVideos($courseId);

// Track course view
$courseModel->trackCourseView($currentUser['id'], $courseId);

// Get user progress if enrolled
$userProgress = null;
if ($isEnrolled) {
    $userProgress = $courseModel->calculateCourseProgress($currentUser['id'], $courseId);

    // Check completion status for each lesson
    foreach ($courseLessons as &$lesson) {
        $lesson['completed'] = false;
        if (isset($lesson['id'])) {
            // Check if user has completed this video's quiz
            $lesson['completed'] = $quizModel->hasCompletedQuiz($currentUser['id'], $lesson['id']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/style.css'); ?>?v=8">

    <style>
        /* Lesson items styling */
        .lesson-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            background: white;
        }

        .lesson-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .lesson-number {
            background: var(--primary-color);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .lesson-content {
            flex-grow: 1;
        }

        .lesson-title {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 16px;
        }

        .lesson-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .lesson-type {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #6c757d;
        }

        .lesson-status.completed {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .lesson-action {
            margin-left: 15px;
            color: var(--primary-color);
            font-size: 20px;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .lesson-item {
                padding: 12px;
                flex-direction: column;
                align-items: flex-start;
            }

            .lesson-number {
                margin-bottom: 10px;
                margin-right: 0;
            }

            .lesson-content {
                width: 100%;
                margin-bottom: 10px;
            }

            .lesson-meta {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .lesson-action {
                margin-left: 0;
                margin-top: 10px;
                align-self: center;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container course-overview-page">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <!-- Messages -->
            <?php if (isset($_GET['message'])): ?>
                <?php if ($_GET['message'] === 'enrollment_success'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Hongera!</strong> Umesajili kwenye kozi hii kwa mafanikio. Sasa unaweza kuanza kujifunza.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['message'] === 'already_enrolled'): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Umesajili!</strong> Tayari umejisajili kwenye kozi hii. Unaweza kuendelea kujifunza.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Course Header -->
            <div class="course-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="course-title mb-2"><?php echo htmlspecialchars($course['name']); ?></h1>
                        <p class="course-description text-muted mb-0">
                            <?php echo htmlspecialchars($course['description'] ?? 'Kozi ya kujifunza na kujenga ujuzi wa kidigitali.'); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php if ($isEnrolled): ?>
                            <?php if ($courseModel->hasCompletedCourse($currentUser['id'], $courseId)): ?>
                                <button class="btn btn-success me-2" onclick="downloadCertificate(<?php echo $courseId; ?>)">
                                    Pakua Vyeti
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-primary" onclick="scrollToLessons()">
                                Endelea Kusoma
                            </button>
                        <?php else: ?>
                            <?php if ($course['courseIsPaidStatusId'] == 1): ?>
                                <button type="button" class="btn btn-primary text-white" style="color: white !important;" onclick="openPaymentModal(<?php echo $courseId; ?>, '<?php echo htmlspecialchars($course['name']); ?>', <?php echo $course['price']; ?>)">
                                    Jiunge kwa TSh <?php echo number_format($course['price'] ?? 0); ?>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary" onclick="enrollCourse(<?php echo $courseId; ?>)">
                                    Jiunge Bure
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="<?php echo app_url('user/courses.php'); ?>" class="btn btn-outline-secondary ms-2">
                            Rudi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Image and Preview -->
            <div class="course-preview-section mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <?php if (!empty($course['photo'])): ?>
                            <div class="course-main-image">
                                <img src="<?php echo app_url($courseModel->getImageUrl($course['photo'])); ?>"
                                    alt="<?php echo htmlspecialchars($course['name']); ?>"
                                    class="img-fluid w-100 rounded"
                                    style="height: 400px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="course-main-image-placeholder d-flex align-items-center justify-content-center bg-light rounded"
                                style="height: 400px;">
                                <span class="text-muted">Picha ya kozi haijapatikana</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <?php if (!empty($courseLessons) && isset($courseLessons[0]['name']) && !empty($courseLessons[0]['name'])): ?>
                            <div class="course-preview-video">
                                <h5 class="mb-3">Onyesho la Kozi</h5>
                                <div class="video-preview-container">
                                    <iframe src="<?php echo htmlspecialchars($courseLessons[0]['name']); ?>"
                                        frameborder="0"
                                        allowfullscreen
                                        class="w-100 rounded"
                                        style="height: 200px;">
                                    </iframe>
                                </div>
                                <p class="text-muted mt-2 small"><?php echo htmlspecialchars($courseLessons[0]['description']); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="course-preview-video">
                                <h5 class="mb-3">Onyesho la Kozi</h5>
                                <div class="video-preview-container d-flex align-items-center justify-content-center bg-light"
                                    style="height: 200px;">
                                    <span class="text-muted">Video ya onyesho haijapatikana</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Course Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalStudents; ?></div>
                        <div class="stat-label">Wanafunzi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalLessons; ?></div>
                        <div class="stat-label">Masomo</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalQuestions; ?></div>
                        <div class="stat-label">Maswali</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php if ($course['courseIsPaidStatusId'] == 1): ?>
                                TSh <?php echo number_format($course['price'] ?? 0); ?>
                            <?php else: ?>
                                Bure
                            <?php endif; ?>
                        </div>
                        <div class="stat-label">Bei</div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="row">
                <!-- Course Description -->
                <div class="col-md-8">
                    <div class="content-card mb-4">
                        <h3 class="card-title">Kuhusu Kozi Hii</h3>
                        <div class="card-content">
                            <?php if (!empty($course['long_description'])): ?>
                                <?php echo nl2br(htmlspecialchars($course['long_description'])); ?>
                            <?php else: ?>
                                <p>Kozi hii inakupa ufahamu wa kina juu ya mada muhimu za kidigitali. Kupitia masomo na mazoezi, utajifunza jinsi ya kutumia teknolojia kwa ufanisi na kuunda suluhisho za kidigitali.</p>
                                <p>Kozi inajumuisha video za mafunzo, maswali ya majaribio, na mazoezi ya vitendo ambayo yatakusaidia kujenga ujuzi wa kweli na uwezo wa kutumia maarifa yako katika hali halisi.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Course Lessons -->
                    <div class="content-card" id="lessons-section">
                        <h3 class="card-title">Mipango ya Masomo</h3>
                        <div class="lessons-list">
                            <?php if (!empty($courseLessons)): ?>
                                <?php foreach ($courseLessons as $index => $lesson): ?>
                                    <div class="lesson-item" onclick="viewLesson(<?php echo $courseId; ?>, <?php echo $lesson['id']; ?>)" style="cursor: pointer;">
                                        <div class="lesson-number"><?php echo $index + 1; ?></div>
                                        <div class="lesson-content">
                                            <h5 class="lesson-title"><?php echo htmlspecialchars($lesson['description']); ?></h5>
                                            <div class="lesson-meta">
                                                <span class="lesson-type">Video na Maswali</span>
                                                <?php if ($isEnrolled && isset($lesson['completed']) && $lesson['completed']): ?>
                                                    <span class="lesson-status completed">✓ Imekamilika</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="lesson-action">
                                            <i class="fas fa-play-circle text-primary"></i>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-lessons">
                                    <p class="text-muted">Masomo yataongezwa hivi karibuni.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="col-md-4">
                    <div class="content-card mb-4">
                        <h3 class="card-title">Onyesho la Kozi</h3>
                        <div class="course-features">
                            <div class="feature-item">
                                <span class="feature-text"><?php echo $totalLessons; ?> Video za Mafunzo</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-text"><?php echo $totalQuestions; ?> Maswali ya Majaribio</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-text">Inafikiwa kwenye Simu na Kompyuta</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-text">Ufikiaji wa Maisha</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-text">Kozi ya Bure Kabisa</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-text">Lugha: Kiswahili</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="content-card">
                        <h3 class="card-title">Vipengele vya Kozi</h3>
                        <div class="course-info">
                            <div class="info-item">
                                <strong>Mwalimu:</strong> Panda Digital Team
                            </div>
                            <div class="info-item">
                                <strong>Muda:</strong> <?php echo $totalLessons; ?> Masomo
                            </div>
                            <div class="info-item">
                                <strong>Ngazi:</strong> Mwanzo
                            </div>
                            <div class="info-item">
                                <strong>Lugha:</strong> Kiswahili
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Payment Header -->
                <div class="modal-header" style="background: var(--primary-color); color: white; border: none;">
                    <h5 class="modal-title" id="paymentModalLabel">Malipo ya Kozi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Payment Body -->
                <div class="modal-body p-4">
                    <!-- Course Info -->
                    <div class="course-info mb-4 p-3" style="background: #f8f9fa; border-left: 4px solid var(--primary-color);">
                        <h6 class="mb-2">Kozi: <span id="courseName"></span></h6>
                        <p class="text-muted mb-0">Jiunge kwenye kozi hii</p>
                    </div>

                    <!-- Price Display -->
                    <div class="text-center mb-4">
                        <div class="price-display" style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);">
                            <span id="coursePrice"></span>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div id="paymentStatus" class="payment-status mb-3" style="display: none;"></div>

                    <!-- Countdown Timer -->
                    <div id="countdown" class="countdown mb-3" style="display: none; text-align: center; font-size: 1.2rem; color: var(--secondary-color);"></div>

                    <!-- Payment Form -->
                    <form id="paymentForm">
                        <input type="hidden" id="courseId">
                        <input type="hidden" id="amount">

                        <!-- Mobile Provider Selection -->
                        <div class="mb-3">
                            <label class="form-label">Chagua Mtoa Huduma wa Simu</label>
                            <div class="mobile-providers d-flex gap-3">
                                <div class="mobile-provider flex-fill text-center p-3 border rounded cursor-pointer" data-provider="halopesa" style="cursor: pointer; transition: all 0.3s ease; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo app_url('assets/images/halopesa.png'); ?>" alt="HaloPesa" style="width: 80px; height: 80px; object-fit: contain;">
                                </div>
                                <div class="mobile-provider flex-fill text-center p-3 border rounded cursor-pointer" data-provider="tigo" style="cursor: pointer; transition: all 0.3s ease; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo app_url('assets/images/tigo.png'); ?>" alt="Mix by YAS" style="width: 80px; height: 80px; object-fit: contain;">
                                </div>
                                <div class="mobile-provider flex-fill text-center p-3 border rounded cursor-pointer" data-provider="airtel" style="cursor: pointer; transition: all 0.3s ease; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <img src="<?php echo app_url('assets/images/airtel.png'); ?>" alt="Airtel Money" style="width: 80px; height: 80px; object-fit: contain;">
                                </div>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Namba ya Simu</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Mfano: 0712345678" required>
                        </div>

                        <!-- Payment Button -->
                        <button type="submit" id="payButton" class="btn w-100" style="background: var(--primary-color); color: white; padding: 12px; font-size: 1.1rem; border: none; border-radius: 8px; transition: all 0.3s ease;">
                            Lipa Sasa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function enrollCourse(courseId) {
            if (confirm('Je, unahitaji kujisajili kwenye kozi hii?')) {
                // Here you would typically make an AJAX call to enroll the user
                // For now, we'll redirect to a simple enrollment process
                window.location.href = '<?php echo app_url("user/enroll.php"); ?>?course_id=' + courseId;
            }
        }

        function scrollToLessons() {
            const lessonsSection = document.getElementById('lessons-section');
            if (lessonsSection) {
                lessonsSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        function viewLesson(courseId, lessonId) {
            // Redirect to lesson viewing page similar to old pandadigitalV2 structure
            window.location.href = '<?php echo app_url("user/learn.php"); ?>?course_id=' + courseId + '&video_id=' + lessonId;
        }

        function downloadCertificate(courseId) {
            if (confirm('Je, unahitaji kupakua vyeti vya kozi hii?')) {
                window.location.href = '<?php echo app_url("user/download-certificate.php"); ?>?course_id=' + courseId;
            }
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

        // Payment Modal Functions
        function openPaymentModal(courseId, courseName, coursePrice) {
            // Set modal content
            document.getElementById('courseName').textContent = courseName;
            document.getElementById('coursePrice').textContent = 'TSh ' + coursePrice.toLocaleString();
            document.getElementById('courseId').value = courseId;
            document.getElementById('amount').value = coursePrice;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        let selectedProvider = '';
        let paymentInterval;
        let attemptCount = 0;
        const maxAttempts = 4;
        const checkInterval = 30000; // 30 seconds

        // Mobile provider selection
        document.querySelectorAll('.mobile-provider').forEach(provider => {
            provider.addEventListener('click', function() {
                // Remove selection from all providers
                document.querySelectorAll('.mobile-provider').forEach(p => p.classList.remove('selected'));
                // Add selection to clicked provider
                this.classList.add('selected');
                selectedProvider = this.dataset.provider;
            });
        });

        // Payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!selectedProvider) {
                showStatus('Tafadhali chagua mtoa huduma wa simu', 'error');
                return;
            }

            const phone = document.getElementById('phone').value;
            if (!phone) {
                showStatus('Tafadhali weka namba ya simu', 'error');
                return;
            }

            // Start payment process
            initiatePayment();
        });

        function initiatePayment() {
            const courseId = document.getElementById('courseId').value;
            const userId = <?php echo $currentUser['id']; ?>;
            const amount = document.getElementById('amount').value;
            const phone = document.getElementById('phone').value;

            // Disable form
            document.getElementById('payButton').disabled = true;
            document.getElementById('payButton').textContent = 'Inatumia Malipo...';

            // Show processing status
            showStatus('Inatumia malipo, tafadhali subiri...', 'processing');

            // Make payment request
            fetch('<?php echo app_url('user/process-payment.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        courseId: courseId,
                        userId: userId,
                        amount: amount,
                        phone: phone,
                        provider: selectedProvider
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('Malipo yamepokelewa! Inakaguliwa...', 'success');
                        startPaymentCheck();
                    } else {
                        showStatus(data.message || 'Kulikuwa na tatizo, jaribu tena', 'error');
                        resetForm();
                    }
                })
                .catch(error => {
                    console.error('Payment error:', error);
                    showStatus('Kulikuwa na tatizo, jaribu tena', 'error');
                    resetForm();
                });
        }

        function startPaymentCheck() {
            attemptCount = 0;
            showCountdown();

            paymentInterval = setInterval(() => {
                attemptCount++;
                checkPaymentStatus();

                if (attemptCount >= maxAttempts) {
                    clearInterval(paymentInterval);
                    hideCountdown();
                    showStatus('Muda wa kukagua malipo umekwisha. Tafadhali jaribu tena au wasiliana na msaada.', 'error');
                    resetForm();
                }
            }, checkInterval);
        }

        function checkPaymentStatus() {
            const courseId = document.getElementById('courseId').value;
            const userId = <?php echo $currentUser['id']; ?>;

            fetch('<?php echo app_url('user/check-payment-status.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        courseId: courseId,
                        userId: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.paid) {
                        clearInterval(paymentInterval);
                        showStatus('Hongera! Malipo yamekamilika. Unaweza kuanza kujifunza.', 'success');
                        setTimeout(() => {
                            window.location.reload(); // Reload page to show enrolled status
                        }, 2000);
                    } else {
                        updateCountdown();
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                });
        }

        function showStatus(message, type) {
            const statusDiv = document.getElementById('paymentStatus');
            statusDiv.textContent = message;
            statusDiv.className = 'payment-status ' + type;
            statusDiv.style.display = 'block';
        }

        function showCountdown() {
            document.getElementById('countdown').style.display = 'block';
            updateCountdown();
        }

        function hideCountdown() {
            document.getElementById('countdown').style.display = 'none';
        }

        function updateCountdown() {
            const remainingAttempts = maxAttempts - attemptCount;
            const remainingTime = Math.ceil((checkInterval * remainingAttempts) / 1000);
            document.getElementById('countdown').textContent =
                `Inakagua malipo... (Mabaki: ${remainingAttempts} mara, Muda: ${remainingTime}s)`;
        }

        function resetForm() {
            document.getElementById('payButton').disabled = false;
            document.getElementById('payButton').textContent = 'Lipa Sasa';
            document.getElementById('phone').value = '';
            selectedProvider = '';
            document.querySelectorAll('.mobile-provider').forEach(p => p.classList.remove('selected'));
        }
    </script>

    <!-- Profile Completion Modal -->
    <?php include __DIR__ . '/../includes/profile-completion-modal.php'; ?>

</body>

</html>