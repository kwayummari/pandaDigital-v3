<?php
require_once '../../config/init.php';
require_once '../../config/database.php';
require_once 'payment_gateway.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'payment_status' => null
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get POST data
    $referenceNo = trim($_POST['reference_no'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $mobileType = trim($_POST['mobile_type'] ?? '');
    $transactionId = trim($_POST['transaction_id'] ?? '');
    $status = trim($_POST['status'] ?? 'pending');

    // Validate required fields
    if (empty($referenceNo) || empty($phone) || $amount <= 0) {
        throw new Exception('Reference number, phone, and amount are required');
    }

    // Initialize database and payment gateway
    $database = new Database();
    $paymentGateway = new PaymentGateway($database);

    // Check if order exists
    $orderExists = checkOrderExists($referenceNo);
    if (!$orderExists) {
        throw new Exception('Order not found');
    }

    // Verify payment amount matches order
    $orderAmount = getOrderAmount($referenceNo);
    if (abs($orderAmount - $amount) > 100) { // Allow small difference for rounding
        throw new Exception('Payment amount does not match order amount');
    }

    // Update payment status
    $updateResult = $paymentGateway->updatePaymentStatus($referenceNo, $status, [
        'phone' => $phone,
        'amount' => $amount,
        'mobile_type' => $mobileType,
        'transaction_id' => $transactionId,
        'status' => $status
    ]);

    if (!$updateResult) {
        throw new Exception('Failed to update payment status');
    }

    // Send confirmation notifications
    sendPaymentNotifications($referenceNo, $status, $amount);

    // Update order status based on payment status
    if ($status === 'success') {
        markOrderAsPaid($referenceNo);
        sendOrderConfirmation($referenceNo);
    }

    $response['success'] = true;
    $response['message'] = 'Payment status updated successfully';
    $response['payment_status'] = $status;

    // Log successful payment confirmation
    error_log("Payment confirmed: Reference {$referenceNo}, Status {$status}, Amount {$amount}");
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Payment confirmation error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error in payment confirmation: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;

/**
 * Check if order exists
 */
function checkOrderExists($referenceNo)
{
    global $database;
    $conn = $database->getConnection();

    $query = "SELECT COUNT(*) as count FROM sales WHERE reference_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$referenceNo]);
    $result = $stmt->fetch();

    return $result['count'] > 0;
}

/**
 * Get order total amount
 */
function getOrderAmount($referenceNo)
{
    global $database;
    $conn = $database->getConnection();

    $query = "SELECT SUM(amount) as total FROM sales WHERE reference_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$referenceNo]);
    $result = $stmt->fetch();

    return (float)$result['total'];
}

/**
 * Mark order as paid
 */
function markOrderAsPaid($referenceNo)
{
    global $database;
    $conn = $database->getConnection();

    try {
        $query = "UPDATE sales SET status = '1' WHERE reference_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$referenceNo]);

        return true;
    } catch (Exception $e) {
        error_log("Error marking order as paid: " . $e->getMessage());
        return false;
    }
}

/**
 * Send payment notifications
 */
function sendPaymentNotifications($referenceNo, $status, $amount)
{
    try {
        global $database;
        $conn = $database->getConnection();

        // Get order details
        $query = "SELECT s.buyersId, s.phone, s.mobile_type, u.email, u.first_name, u.last_name 
                  FROM sales s 
                  LEFT JOIN users u ON s.buyersId = u.id 
                  WHERE s.reference_no = ? 
                  LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$referenceNo]);
        $order = $stmt->fetch();

        if (!$order) {
            return false;
        }

        // Send SMS notification (you can implement actual SMS API here)
        sendSMSNotification($order['phone'], $status, $amount, $referenceNo);

        // Send email notification
        sendEmailNotification($order['email'], $order['first_name'], $status, $amount, $referenceNo);

        // Send WhatsApp notification (optional)
        sendWhatsAppNotification($order['phone'], $status, $amount, $referenceNo);

        return true;
    } catch (Exception $e) {
        error_log("Error sending payment notifications: " . $e->getMessage());
        return false;
    }
}

/**
 * Send SMS notification
 */
function sendSMSNotification($phone, $status, $amount, $referenceNo)
{
    $message = "Panda Market: ";

    if ($status === 'success') {
        $message .= "Malipo yako ya TSh." . number_format($amount, 0) . " yamekamilika. ";
        $message .= "Order #$referenceNo inakusanywa. Asante!";
    } else {
        $message .= "Malipo yako ya TSh." . number_format($amount, 0) . " yanashindwa. ";
        $message .= "Tafadhali jaribu tena au wasiliana nasi.";
    }

    // Log SMS message (implement actual SMS API here)
    error_log("SMS notification prepared for {$phone}: " . substr($message, 0, 100) . "...");

    return true;
}

/**
 * Send email notification
 */
function sendEmailNotification($email, $firstName, $status, $amount, $referenceNo)
{
    $subject = "Panda Market - Payment " . ucfirst($status);

    $emailBody = "
    <html>
    <head>
        <title>Payment {$status} - Panda Market</title>
    </head>
    <body>
        <h2>Habari {$firstName}!</h2>
        <p>Malipo yako ya Panda Market yamekamilika.</p>
        
        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3>Maelezo ya Malipo:</h3>
            <p><strong>Order Reference:</strong> #{$referenceNo}</p>
            <p><strong>Kiasi:</strong> TSh." . number_format($amount, 0) . "</p>
            <p><strong>Status:</strong> " . ucfirst($status) . "</p>
            <p><strong>Tarehe:</strong> " . date('d/m/Y H:i') . "</p>
        </div>
    ";

    if ($status === 'success') {
        $emailBody .= "
            <p>Order yako inakusanywa na itatumwa kwako kwa haraka iwezekanavyo.</p>
            <p>Unaweza kufuatilia order yako kupitia <a href='https://pandadigital.co.tz/v3/market/track_order.php?order_id={$referenceNo}'>hapa</a>.</p>
        ";
    } else {
        $emailBody .= "
            <p>Kama una maswali yoyote, tafadhali wasiliana nasi kupitia:</p>
            <ul>
                <li>WhatsApp: +255 767 680 463</li>
                <li>Simu: +255 767 680 463</li>
                <li>Barua pepe: info@pandadigital.co.tz</li>
            </ul>
        ";
    }

    $emailBody .= "
        <hr>
        <p><small>Ujumbe huu umetumwa kutoka Panda Market website</small></p>
    </body>
    </html>
    ";

    // Send email (using PHP mail function)
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Panda Market <noreply@pandadigital.co.tz>',
        'Reply-To: info@pandadigital.co.tz',
        'X-Mailer: PHP/' . phpversion()
    ];

    $mailSent = mail($email, $subject, $emailBody, implode("\r\n", $headers));

    if ($mailSent) {
        error_log("Payment confirmation email sent to {$email}");
    } else {
        error_log("Failed to send payment confirmation email to {$email}");
    }

    return $mailSent;
}

/**
 * Send WhatsApp notification
 */
function sendWhatsAppNotification($phone, $status, $amount, $referenceNo)
{
    $message = "Panda Market - Payment " . ucfirst($status) . "\n\n";
    $message .= "Order: #{$referenceNo}\n";
    $message .= "Amount: TSh." . number_format($amount, 0) . "\n";
    $message .= "Status: " . ucfirst($status) . "\n";
    $message .= "Date: " . date('d/m/Y H:i') . "\n\n";

    if ($status === 'success') {
        $message .= "✅ Malipo yamekamilika!\n";
        $message .= "Order yako inakusanywa na itatumwa kwako kwa haraka.";
    } else {
        $message .= "❌ Malipo yanashindwa.\n";
        $message .= "Tafadhali jaribu tena au wasiliana nasi.";
    }

    // Log WhatsApp message (implement actual WhatsApp API here)
    error_log("WhatsApp notification prepared for {$phone}: " . substr($message, 0, 100) . "...");

    return true;
}

/**
 * Send order confirmation
 */
function sendOrderConfirmation($referenceNo)
{
    try {
        global $database;
        $conn = $database->getConnection();

        // Get order details
        $query = "SELECT s.*, u.email, u.first_name, u.last_name, u.phone 
                  FROM sales s 
                  LEFT JOIN users u ON s.buyersId = u.id 
                  WHERE s.reference_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$referenceNo]);
        $orders = $stmt->fetchAll();

        if (empty($orders)) {
            return false;
        }

        $order = $orders[0];
        $totalAmount = array_sum(array_column($orders, 'amount'));

        // Send order confirmation email
        $subject = "Panda Market - Order Confirmation #{$referenceNo}";

        $emailBody = "
        <html>
        <head>
            <title>Order Confirmation - Panda Market</title>
        </head>
        <body>
            <h2>Asante kwa Order yako!</h2>
            <p>Habari {$order['first_name']}!</p>
            <p>Order yako imepokelewa na inakusanywa. Hapa kuna maelezo ya order yako:</p>
            
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>Maelezo ya Order:</h3>
                <p><strong>Order Reference:</strong> #{$referenceNo}</p>
                <p><strong>Tarehe ya Order:</strong> " . date('d/m/Y H:i') . "</p>
                <p><strong>Jumla ya Bidhaa:</strong> " . count($orders) . "</p>
                <p><strong>Jumla ya Malipo:</strong> TSh." . number_format($totalAmount, 0) . "</p>
            </div>
            
            <h3>Bidhaa Zilizonunuliwa:</h3>
            <ul>
        ";

        foreach ($orders as $item) {
            $emailBody .= "<li>{$item['quantity']} x Bidhaa (ID: {$item['productId']}) - TSh." . number_format($item['amount'], 0) . "</li>";
        }

        $emailBody .= "
            </ul>
            
            <p><strong>Hatua Zinazofuata:</strong></p>
            <ol>
                <li>Order yako inakusanywa na kuandaliwa</li>
                <li>Itatumwa kwako kupitia usafiri</li>
                <li>Utapata barua pepe ya tracking</li>
                <li>Bidhaa zitaweza kufika kwako ndani ya siku 2-3</li>
            </ol>
            
            <p>Unaweza kufuatilia order yako kupitia <a href='https://pandadigital.co.tz/v3/market/track_order.php?order_id={$referenceNo}'>hapa</a>.</p>
            
            <p>Kama una maswali yoyote, tafadhali wasiliana nasi:</p>
            <ul>
                <li>WhatsApp: +255 767 680 463</li>
                <li>Simu: +255 767 680 463</li>
                <li>Barua pepe: info@pandadigital.co.tz</li>
            </ul>
            
            <hr>
            <p><small>Ujumbe huu umetumwa kutoka Panda Market website</small></p>
        </body>
        </html>
        ";

        // Send email
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: Panda Market <noreply@pandadigital.co.tz>',
            'Reply-To: info@pandadigital.co.tz',
            'X-Mailer: PHP/' . phpversion()
        ];

        $mailSent = mail($order['email'], $subject, $emailBody, implode("\r\n", $headers));

        if ($mailSent) {
            error_log("Order confirmation email sent to {$order['email']}");
        } else {
            error_log("Failed to send order confirmation email to {$order['email']}");
        }

        return $mailSent;
    } catch (Exception $e) {
        error_log("Error sending order confirmation: " . $e->getMessage());
        return false;
    }
}
