<?php
include "../admin/includes/db/connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    if ($productId > 0 && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO ratings (productId, rating) VALUES (?, ?)");
        $stmt->bind_param("ii", $productId, $rating);
        if ($stmt->execute()) {
            echo "Ukadiriaji umewasilishwa.";
        } else {
            echo "Hitilafu katika kuwasilisha ukadiriaji.";
        }
        $stmt->close();
    } else {
        echo "Invalid input.";
    }
} else {
    echo "Invalid request.";
}
