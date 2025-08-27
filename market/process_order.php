<?php
require_once '../config/init.php';
require_once '../config/database.php';
require_once 'includes/cart_handler.php';
require_once 'payment/payment_gateway.php';

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
    'order_data' => null
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get POST data
    $phone = trim($_POST['phone'] ?? '');
    $mobileType = trim($_POST['mobile_type'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $shippingCity = trim($_POST['shipping_city'] ?? '');
    $shippingRegion = trim($_POST['shipping_region'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Validate required fields
    if (empty($phone) || empty($mobileType)) {
        throw new Exception('Phone number and mobile money type are required');
    }

    // Validate phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 9) {
        throw new Exception('Invalid phone number');
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        throw new Exception('User must be logged in to place an order');
    }

    $userId = $_SESSION['user_id'];

    // Initialize database and handlers
    $database = new Database();
    $cartHandler = new CartHandler($database);
    $paymentGateway = new PaymentGateway($database);

    // Check if cart is empty
    if ($cartHandler->isCartEmpty()) {
        throw new Exception('Cart is empty');
    }

    // Get cart items
    $cartItems = $cartHandler->getCart();
    $cartTotal = $cartHandler->getCartTotal();

    // Calculate shipping cost (basic calculation)
    $shippingCost = calculateShippingCost($shippingRegion);
    $totalAmount = $cartTotal + $shippingCost;

    // Process payment
    $paymentResult = $paymentGateway->processPayment($userId, $cartItems, $phone, $mobileType, $totalAmount);

    if (!$paymentResult['success']) {
        throw new Exception('Payment processing failed: ' . $paymentResult['error']);
    }

    // Save shipping information
    $shippingResult = saveShippingInfo($userId, $paymentResult['reference_no'], $shippingAddress, $shippingCity, $shippingRegion, $notes);

    if (!$shippingResult) {
        // Log warning but don't fail the order
        error_log("Warning: Failed to save shipping information for order " . $paymentResult['reference_no']);
    }

    // Clear cart after successful order
    $cartHandler->clearCart();

    // Prepare order data for response
    $orderData = [
        'reference_no' => $paymentResult['reference_no'],
        'trans_id' => $paymentResult['trans_id'],
        'mno_ref' => $paymentResult['mno_ref'],
        'total_amount' => $totalAmount,
        'cart_total' => $cartTotal,
        'shipping_cost' => $shippingCost,
        'phone' => $phone,
        'mobile_type' => $mobileType,
        'items_count' => count($cartItems),
        'payment_instructions' => $paymentGateway->getPaymentInstructions($mobileType, $totalAmount, $paymentResult['reference_no'])
    ];

    $response['success'] = true;
    $response['message'] = 'Order placed successfully! Please complete payment using the instructions below.';
    $response['order_data'] = $orderData;

    // Log successful order
    error_log("Order placed successfully: Reference {$paymentResult['reference_no']}, User {$userId}, Amount {$totalAmount}");
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Order processing error: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error in order processing: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;

/**
 * Calculate shipping cost based on region
 */
function calculateShippingCost($region)
{
    $shippingRates = [
        'Dar es Salaam' => 5000,
        'Arusha' => 8000,
        'Mwanza' => 10000,
        'Dodoma' => 8000,
        'Mbeya' => 12000,
        'Tanga' => 7000,
        'Morogoro' => 6000,
        'Iringa' => 11000,
        'Songwe' => 12000,
        'Ruvuma' => 15000,
        'Lindi' => 14000,
        'Mtwara' => 14000,
        'Pwani' => 8000,
        'Kigoma' => 15000,
        'Tabora' => 12000,
        'Shinyanga' => 10000,
        'Simiyu' => 10000,
        'Geita' => 10000,
        'Kagera' => 12000,
        'Mara' => 12000,
        'Manyara' => 9000,
        'Njombe' => 11000,
        'Katavi' => 13000,
        'Singida' => 10000
    ];

    // Default shipping cost for unknown regions
    return $shippingRates[$region] ?? 10000;
}

/**
 * Save shipping information
 */
function saveShippingInfo($userId, $referenceNo, $address, $city, $region, $notes)
{
    try {
        global $database;
        $conn = $database->getConnection();

        // Check if shipping info table exists, if not create it
        $createTableQuery = "CREATE TABLE IF NOT EXISTS shipping_info (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            reference_no VARCHAR(100) NOT NULL,
            shipping_address TEXT,
            shipping_city VARCHAR(100),
            shipping_region VARCHAR(100),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (reference_no),
            INDEX (user_id)
        )";

        $conn->exec($createTableQuery);

        // Insert shipping information
        $insertQuery = "INSERT INTO shipping_info (user_id, reference_no, shipping_address, shipping_city, shipping_region, notes) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        return $insertStmt->execute([
            $userId,
            $referenceNo,
            $address,
            $city,
            $region,
            $notes
        ]);
    } catch (Exception $e) {
        error_log("Error saving shipping info: " . $e->getMessage());
        return false;
    }
}
