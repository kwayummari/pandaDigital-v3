<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$courseModel = new Course();

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

// Check if course is paid
if ($course['courseIsPaidStatusId'] != 1) {
    header('Location: ' . app_url('user/course-overview.php') . '?id=' . $courseId . '&error=course_is_free');
    exit();
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malipo ya Kozi - <?php echo htmlspecialchars($course['name']); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/style.css'); ?>?v=9">

    <style>
        .payment-modal {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
            overflow: hidden;
        }

        .payment-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-body {
            padding: 2rem;
        }

        .course-info {
            background: var(--light-color);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .price-display {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            margin: 1rem 0;
        }

        .payment-form .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .payment-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .payment-btn {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .payment-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 188, 59, 0.3);
        }

        .payment-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .payment-status {
            text-align: center;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            display: none;
        }

        .payment-status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .payment-status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .payment-status.processing {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .countdown {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-align: center;
            margin: 1rem 0;
        }

        .mobile-providers {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .mobile-provider {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-provider:hover {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.1);
        }

        .mobile-provider.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .mobile-provider img {
            width: 30px;
            height: 30px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="payment-modal">
            <!-- Payment Header -->
            <div class="payment-header">
                <h2 class="mb-0">Malipo ya Kozi</h2>
                <p class="mb-0 mt-2">Jiunge kwenye kozi hii</p>
            </div>

            <!-- Payment Body -->
            <div class="payment-body">
                <!-- Course Information -->
                <div class="course-info">
                    <h5 class="mb-2"><?php echo htmlspecialchars($course['name']); ?></h5>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($course['description'] ?? 'Kozi ya kujifunza na kujenga ujuzi wa kidigitali.'); ?></p>
                </div>

                <!-- Price Display -->
                <div class="price-display">
                    TSh <?php echo number_format($course['price'] ?? 0); ?>
                </div>

                <!-- Payment Status -->
                <div id="paymentStatus" class="payment-status"></div>

                <!-- Countdown Timer -->
                <div id="countdown" class="countdown" style="display: none;"></div>

                <!-- Payment Form -->
                <form id="paymentForm" class="payment-form">
                    <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">
                    <input type="hidden" id="userId" value="<?php echo $currentUser['id']; ?>">
                    <input type="hidden" id="amount" value="<?php echo $course['price']; ?>">

                    <!-- Mobile Provider Selection -->
                    <div class="mb-3">
                        <label class="form-label">Chagua Mtoa Huduma wa Simu</label>
                        <div class="mobile-providers">
                            <div class="mobile-provider" data-provider="mpesa">
                                <img src="<?php echo app_url('assets/images/mpesa.png'); ?>" alt="M-Pesa">
                                <div>M-Pesa</div>
                            </div>
                            <div class="mobile-provider" data-provider="tigopesa">
                                <img src="<?php echo app_url('assets/images/tigopesa.png'); ?>" alt="Tigo Pesa">
                                <div>Tigo Pesa</div>
                            </div>
                            <div class="mobile-provider" data-provider="airtel">
                                <img src="<?php echo app_url('assets/images/airtel.png'); ?>" alt="Airtel Money">
                                <div>Airtel Money</div>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Namba ya Simu</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            placeholder="Mfano: 0712345678" required>
                    </div>

                    <!-- Payment Button -->
                    <button type="submit" id="payButton" class="payment-btn">
                        Lipa Sasa
                    </button>
                </form>

                <!-- Back Button -->
                <div class="text-center mt-3">
                    <a href="<?php echo app_url('user/course-overview.php'); ?>?id=<?php echo $courseId; ?>"
                        class="btn btn-outline-secondary">
                        Rudi kwenye Kozi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedProvider = '';
        let paymentInterval = null;
        let attemptCount = 0;
        const maxAttempts = 4;
        const checkInterval = 30000; // 30 seconds

        // Mobile provider selection
        document.querySelectorAll('.mobile-provider').forEach(provider => {
            provider.addEventListener('click', function() {
                document.querySelectorAll('.mobile-provider').forEach(p => p.classList.remove('selected'));
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
            const userId = document.getElementById('userId').value;
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
                        showStatus('Umepokea ussd ibukizi weka nenosiri lako na uthibitishe...', 'success');
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
                    showStatus('Malipo hayajakamilika. Tafadhali jaribu tena au wasiliana nasi.', 'error');
                    resetForm();
                    hideCountdown();
                }
            }, checkInterval);
        }

        function checkPaymentStatus() {
            const courseId = document.getElementById('courseId').value;
            const userId = document.getElementById('userId').value;

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
                            window.location.href = '<?php echo app_url('user/course-overview.php'); ?>?id=' + courseId + '&message=payment_success';
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
            statusDiv.className = `payment-status ${type}`;
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
</body>

</html>