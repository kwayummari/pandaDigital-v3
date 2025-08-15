<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../services/PaymentService.php";
require_once __DIR__ . "/../models/Course.php";
require_once __DIR__ . "/../config/init.php";

// Set content type to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
$requiredFields = ['courseId', 'userId', 'amount', 'phone', 'provider'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit();
    }
}

try {
    $auth = new AuthMiddleware();
    $auth->requireRole('user');

    $currentUser = $auth->getCurrentUser();

    // Verify user ID matches current user
    if ($currentUser['id'] != $data['userId']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    $paymentService = new PaymentService();

    // Get course information using the Course model directly
    $courseModel = new Course();
    $course = $courseModel->getCourseById($data['courseId'], $data['userId']);
    if (!$course) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit();
    }

    // Check if course is paid
    if ($course['courseIsPaidStatusId'] != 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course is free, no payment required']);
        exit();
    }

    // Validate amount
    if ($course['price'] != $data['amount']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Price mismatch']);
        exit();
    }

    // Check if user is already enrolled
    if ($courseModel->hasPaidCourseAccess($data['userId'], $data['courseId'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Already enrolled in this course']);
        exit();
    }

    // Generate unique reference number
    $referenceNumber = 'PAY-' . time() . '-' . $data['userId'] . '-' . $data['courseId'];

    // Create payment transaction record
    $transactionId = $courseModel->createPaymentTransaction([
        'userId' => $data['userId'],
        'courseId' => $data['courseId'],
        'amount' => $data['amount'],
        'phone' => $data['phone'],
        'provider' => $data['provider'],
        'referenceNumber' => $referenceNumber,
        'status' => 0 // pending
    ]);

    if (!$transactionId) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create transaction record']);
        exit();
    }

    // Process payment through AzamPay using the simplified method
    try {
        $paymentResult = $paymentService->processCoursePayment($data['phone'], $data['amount'], $data['provider']);

        // PaymentService returns an array directly, not JSON
        if ($paymentResult['success'] && isset($paymentResult['transactionId'])) {
            // Update transaction status to processing
            $courseModel->updatePaymentTransaction($transactionId, 2); // 2 = processing

            echo json_encode([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'referenceNumber' => $referenceNumber,
                'transactionId' => $paymentResult['transactionId']
            ]);
        } else {
            // Update transaction status to failed
            $courseModel->updatePaymentTransaction($transactionId, 3); // 3 = failed

            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $paymentResult['message'] ?? 'Payment initiation failed'
            ]);
        }
    } catch (Exception $e) {
        // Update transaction status to failed
        $courseModel->updatePaymentTransaction($transactionId, 3); // 3 = failed

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
