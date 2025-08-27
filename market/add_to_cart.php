<?php
require_once '../config/init.php';
require_once '../config/database.php';
require_once 'includes/cart_handler.php';

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
    'cart_count' => 0,
    'cart_total' => 0
];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Get POST data
    $productId = $_POST['product_id'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);

    // Validate inputs
    if (!$productId) {
        throw new Exception('Product ID is required');
    }

    if ($quantity <= 0) {
        throw new Exception('Quantity must be greater than 0');
    }

    // Initialize database and cart handler
    $database = new Database();
    $cartHandler = new CartHandler($database);

    // Add product to cart
    $result = $cartHandler->addToCart($productId, $quantity);

    if ($result) {
        // Get updated cart information
        $cartSummary = $cartHandler->getCartSummary();

        $response['success'] = true;
        $response['message'] = 'Product added to cart successfully';
        $response['cart_count'] = $cartSummary['total_items'];
        $response['cart_total'] = $cartSummary['total_amount'];
        $response['cart_items'] = $cartSummary['items'];

        // Log successful addition
        error_log("Product $productId added to cart for session " . session_id());
    } else {
        throw new Exception('Failed to add product to cart');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Error adding product to cart: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error adding product to cart: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
