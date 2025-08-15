<?php
session_start();
include "../admin/includes/db/connect.php";

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];
$productId = $data['productId'];

// Log received data for debugging
error_log("Received userId: $userId, productId: $productId");

// Check if a chat already exists
$chat_query = "SELECT id FROM productChats WHERE userId = ? AND productId = ?";
$chat_stmt = mysqli_prepare($conn, $chat_query);

if (!$chat_stmt) {
    error_log("Failed to prepare chat query: " . mysqli_error($conn));
    http_response_code(500);
    exit;
}

mysqli_stmt_bind_param($chat_stmt, "ii", $userId, $productId);
mysqli_stmt_execute($chat_stmt);
mysqli_stmt_bind_result($chat_stmt, $chatId);
mysqli_stmt_fetch($chat_stmt);
mysqli_stmt_close($chat_stmt);

if ($chatId) {
    // Chat exists
    $response = ['success' => true, 'chatId' => $chatId];
} else {
    // Create new chat
    $insert_query = "INSERT INTO productChats (userId, productId) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);

    if (!$insert_stmt) {
        error_log("Failed to prepare insert query: " . mysqli_error($conn));
        http_response_code(500);
        exit;
    }

    mysqli_stmt_bind_param($insert_stmt, "ii", $userId, $productId);

    if (mysqli_stmt_execute($insert_stmt)) {
        $chatId = mysqli_insert_id($conn);
        $response = ['success' => true, 'chatId' => $chatId];
    } else {
        error_log("Failed to execute insert query: " . mysqli_error($conn));
        $response = ['success' => false];
    }

    mysqli_stmt_close($insert_stmt);
}

echo json_encode($response);

mysqli_close($conn);
