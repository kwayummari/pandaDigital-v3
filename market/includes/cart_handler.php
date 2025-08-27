<?php
/**
 * Cart Handler for Panda Market
 * Uses session-based cart management with existing database structure
 */

class CartHandler {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    /**
     * Add product to cart (session-based)
     */
    public function addToCart($productId, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product already exists in cart
        $existingIndex = $this->findProductInCart($productId);
        
        if ($existingIndex !== false) {
            // Update quantity
            $_SESSION['cart'][$existingIndex]['quantity'] += $quantity;
        } else {
            // Add new product to cart
            $product = $this->getProductDetails($productId);
            if ($product) {
                $_SESSION['cart'][] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['amount'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'category_id' => $product['categoryId']
                ];
            }
        }
        
        return true;
    }
    
    /**
     * Remove product from cart
     */
    public function removeFromCart($productId) {
        if (!isset($_SESSION['cart'])) {
            return false;
        }
        
        $index = $this->findProductInCart($productId);
        if ($index !== false) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            return true;
        }
        
        return false;
    }
    
    /**
     * Update product quantity in cart
     */
    public function updateQuantity($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            return false;
        }
        
        $index = $this->findProductInCart($productId);
        if ($index !== false) {
            if ($quantity <= 0) {
                return $this->removeFromCart($productId);
            }
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            return true;
        }
        
        return false;
    }
    
    /**
     * Get cart contents
     */
    public function getCart() {
        return $_SESSION['cart'] ?? [];
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal() {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Get cart item count
     */
    public function getCartItemCount() {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
    
    /**
     * Clear cart
     */
    public function clearCart() {
        unset($_SESSION['cart']);
        return true;
    }
    
    /**
     * Check if cart is empty
     */
    public function isCartEmpty() {
        return empty($_SESSION['cart']);
    }
    
    /**
     * Find product index in cart
     */
    private function findProductInCart($productId) {
        if (!isset($_SESSION['cart'])) {
            return false;
        }
        
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_id'] == $productId) {
                return $index;
            }
        }
        
        return false;
    }
    
    /**
     * Get product details from database
     */
    private function getProductDetails($productId) {
        $query = "SELECT id, name, amount, image, categoryId FROM products WHERE id = ? AND status = '1'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
    
    /**
     * Save cart to database (when user checks out)
     */
    public function saveCartToDatabase($userId, $phone, $mobileType) {
        if ($this->isCartEmpty()) {
            return false;
        }
        
        try {
            $this->conn->beginTransaction();
            
            foreach ($_SESSION['cart'] as $item) {
                $query = "INSERT INTO sales (buyersId, productId, quantity, amount, phone, reference_no, mobile_type, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, '0')";
                $stmt = $this->conn->prepare($query);
                
                $referenceNo = $this->generateReferenceNo();
                $stmt->execute([
                    $userId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'] * $item['quantity'],
                    $phone,
                    $referenceNo,
                    $mobileType
                ]);
            }
            
            $this->conn->commit();
            $this->clearCart(); // Clear cart after successful save
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error saving cart to database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate unique reference number
     */
    private function generateReferenceNo() {
        return 'REF' . time() . rand(1000, 9999);
    }
    
    /**
     * Get cart summary for display
     */
    public function getCartSummary() {
        $cart = $this->getCart();
        $summary = [
            'items' => $cart,
            'total_items' => $this->getCartItemCount(),
            'total_amount' => $this->getCartTotal(),
            'is_empty' => $this->isCartEmpty()
        ];
        
        return $summary;
    }
}
?>
