<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get search query
$searchQuery = $_GET['q'] ?? '';

if (empty($searchQuery) || strlen($searchQuery) < 2) {
    echo json_encode([]);
    exit();
}

try {
    // Search products by name or description
    $searchQuery = "%{$searchQuery}%";

    $searchSql = "
        SELECT p.id, p.name, p.amount, p.image, p.description, p.isOffered, p.offer,
               c.name as category_name,
               IFNULL(AVG(r.rating), 0) AS avg_rating
        FROM products p
        LEFT JOIN categories c ON p.categoryId = c.id
        LEFT JOIN ratings r ON p.id = r.productId
        WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.status = '1'
        GROUP BY p.id
        ORDER BY p.name ASC
        LIMIT 10
    ";

    $searchStmt = $conn->prepare($searchSql);
    $searchStmt->execute([$searchQuery, $searchQuery]);
    $results = $searchStmt->fetchAll();

    // Format results for display
    $formattedResults = [];
    foreach ($results as $product) {
        $formattedResults[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'amount' => number_format($product['amount'], 0),
            'image' => $product['image'],
            'description' => $product['description'],
            'category' => $product['category_name'],
            'rating' => round($product['avg_rating'], 1),
            'isOffered' => $product['isOffered'],
            'offer' => $product['offer']
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($formattedResults);
} catch (Exception $e) {
    error_log("Error searching products: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
