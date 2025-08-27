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
    $action = $_POST['action'] ?? '';
    $productId = $_POST['product_id'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);

    // Validate action
    if (!in_array($action, ['update', 'remove', 'get', 'clear'])) {
        throw new Exception('Invalid action');
    }

    // Initialize database and cart handler
    $database = new Database();
    $cartHandler = new CartHandler($database);

    switch ($action) {
        case 'update':
            // Update product quantity
            if (!$productId) {
                throw new Exception('Product ID is required for update action');
            }

            if ($quantity <= 0) {
                throw new Exception('Quantity must be greater than 0');
            }

            $result = $cartHandler->updateQuantity($productId, $quantity);

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Cart updated successfully';
            } else {
                throw new Exception('Failed to update cart');
            }
            break;

        case 'remove':
            // Remove product from cart
            if (!$productId) {
                throw new Exception('Product ID is required for remove action');
            }

            $result = $cartHandler->removeFromCart($productId);

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Product removed from cart successfully';
            } else {
                throw new Exception('Failed to remove product from cart');
            }
            break;

        case 'clear':
            // Clear entire cart
            $result = $cartHandler->clearCart();

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Cart cleared successfully';
            } else {
                throw new Exception('Failed to clear cart');
            }
            break;

        case 'get':
            // Get cart information (no additional validation needed)
            $response['success'] = true;
            $response['message'] = 'Cart information retrieved successfully';
            break;

        default:
            throw new Exception('Unknown action');
    }

    // Get updated cart information for all actions
    $cartSummary = $cartHandler->getCartSummary();
    $response['cart_count'] = $cartSummary['total_items'];
    $response['cart_total'] = $cartSummary['total_amount'];
    $response['cart_items'] = $cartSummary['items'];
    $response['is_empty'] = $cartSummary['is_empty'];

    // Log successful action
    error_log("Cart action '$action' completed successfully for session " . session_id());
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Error in cart management: " . $e->getMessage());
} catch (Error $e) {
    $response['message'] = 'System error occurred';
    error_log("System error in cart management: " . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
