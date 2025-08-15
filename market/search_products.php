<?php
include "../admin/includes/db/connect.php";

if (isset($_GET['q'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['q']);

    // Search query to find products by name or description, include image
    $query = "
        SELECT id, name, description, amount, image 
        FROM products 
        WHERE (name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%') 
        AND status = '1'
        LIMIT 5
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Limit description to 20 words
        $descriptionWords = explode(' ', $row['description']);
        if (count($descriptionWords) > 20) {
            $row['description'] = implode(' ', array_slice($descriptionWords, 0, 20)) . '...';
        }
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
}

mysqli_close($conn);
