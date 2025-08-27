<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit();
}

// Get POST data
$productId = $_POST['productId'] ?? null;
$rating = $_POST['rating'] ?? null;

// Validate input
if (!$productId || !$rating) {
    http_response_code(400);
    echo "Missing required parameters";
    exit();
}

if (!is_numeric($productId) || !is_numeric($rating)) {
    http_response_code(400);
    echo "Invalid parameters";
    exit();
}

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo "Rating must be between 1 and 5";
    exit();
}

try {
    // Check if product exists
    $productQuery = "SELECT id FROM products WHERE id = ? AND status = '1'";
    $productStmt = $conn->prepare($productQuery);
    $productStmt->execute([$productId]);

    if (!$productStmt->fetch()) {
        http_response_code(404);
        echo "Product not found";
        exit();
    }

    // For now, we'll just insert the rating
    // In a real system, you might want to check if user is logged in
    // and prevent duplicate ratings from the same user

    $insertQuery = "INSERT INTO ratings (productId, rating, date_created) VALUES (?, ?, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->execute([$productId, $rating]);

    if ($insertStmt->rowCount() > 0) {
        echo "Rating submitted successfully";
    } else {
        http_response_code(500);
        echo "Failed to submit rating";
    }
} catch (Exception $e) {
    error_log("Error submitting rating: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error";
}
