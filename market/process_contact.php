<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get POST data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $productId = $_POST['product_id'] ?? null;
    $includeProduct = isset($_POST['includeProduct']) && $_POST['includeProduct'] == 'on';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        throw new Exception('All fields are required');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 9) {
        throw new Exception('Invalid phone number');
    }

    // Initialize database
    $database = Database::getInstance();
    $conn = $database->getConnection();

    // Get product information if product ID is provided
    $productInfo = null;
    if ($productId && $includeProduct) {
        $productQuery = "SELECT p.*, c.name as category_name FROM products p 
                        LEFT JOIN categories c ON p.categoryId = c.id 
                        WHERE p.id = ? AND p.status = '1'";
        $productStmt = $conn->prepare($productQuery);
        $productStmt->execute([$productId]);
        $productInfo = $productStmt->fetch();
    }

    // Insert contact message into database
    $insertQuery = "INSERT INTO productMessages (productId, userId, name, email, phone, subject, message, date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);

    $userId = $_SESSION['user_id'] ?? 0; // 0 for guest users

    $result = $insertStmt->execute([
        $productId ?: 0,
        $userId,
        $name,
        $email,
        $phone,
        $subject,
        $message
    ]);

    if (!$result) {
        throw new Exception('Failed to save message');
    }

    $messageId = $conn->lastInsertId();

    // Prepare email content
    $emailSubject = "Ujumbe Mpya kutoka Panda Market: $subject";

    $emailBody = "
    <html>
    <head>
        <title>Ujumbe Mpya kutoka Panda Market</title>
    </head>
    <body>
        <h2>Ujumbe Mpya kutoka Panda Market</h2>
        <p><strong>Jina:</strong> $name</p>
        <p><strong>Barua Pepe:</strong> $email</p>
        <p><strong>Simu:</strong> $phone</p>
        <p><strong>Mada:</strong> $subject</p>
        <p><strong>Ujumbe:</strong></p>
        <p>" . nl2br(htmlspecialchars($message)) . "</p>
    ";

    if ($productInfo) {
        $emailBody .= "
        <hr>
        <h3>Maelezo ya Bidhaa:</h3>
        <p><strong>Bidhaa:</strong> {$productInfo['name']}</p>
        <p><strong>Kategoria:</strong> {$productInfo['category_name']}</p>
        <p><strong>Bei:</strong> Tsh." . number_format($productInfo['amount'], 0) . "</p>
        ";
    }

    $emailBody .= "
        <hr>
        <p><small>Ujumbe huu umetumwa kutoka Panda Market website</small></p>
    </body>
    </html>
    ";

    // Send email notification (using PHP mail function)
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Panda Market <noreply@pandadigital.co.tz>',
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion()
    ];

    $mailSent = mail(
        'info@pandadigital.co.tz', // Admin email
        $emailSubject,
        $emailBody,
        implode("\r\n", $headers)
    );

    // Log the contact message
    error_log("Contact message received from $email: $subject - Message ID: $messageId");

    if ($mailSent) {
        error_log("Email notification sent successfully for message ID: $messageId");
    } else {
        error_log("Failed to send email notification for message ID: $messageId");
    }

    // Send WhatsApp notification (optional - you can implement this)
    $whatsappMessage = "Ujumbe mpya kutoka Panda Market:\n\n";
    $whatsappMessage .= "Jina: $name\n";
    $whatsappMessage .= "Email: $email\n";
    $whatsappMessage .= "Simu: $phone\n";
    $whatsappMessage .= "Mada: $subject\n\n";
    $whatsappMessage .= "Ujumbe: $message";

    if ($productInfo) {
        $whatsappMessage .= "\n\nBidhaa: {$productInfo['name']}";
        $whatsappMessage .= "\nBei: Tsh." . number_format($productInfo['amount'], 0);
    }

    // Log WhatsApp message (you can implement actual WhatsApp API here)
    error_log("WhatsApp notification prepared: " . substr($whatsappMessage, 0, 100) . "...");

    $response['success'] = true;
    $response['message'] = 'Ujumbe wako umetumwa kwa mafanikio! Tutakujibu kwa haraka iwezekanavyo.';
    $response['message_id'] = $messageId;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Contact form error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error in contact form: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
