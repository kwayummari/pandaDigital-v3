<?php
session_start();
include "../admin/includes/db/connect.php";

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
$chatId = $data['chatId'];
$messageContent = $data['messageContent'];
$type = '1'; // Correct key used in JS

// Prepare the SQL query
$insert_query = "INSERT INTO productMessages (chatId, content, type) VALUES (?, ?, ?)";
$insert_stmt = mysqli_prepare($conn, $insert_query);

if (!$insert_stmt) {
    error_log("Failed to prepare insert query: " . mysqli_error($conn));
    http_response_code(500);
    exit;
}

mysqli_stmt_bind_param($insert_stmt, "iss", $chatId, $messageContent, $type);

if (mysqli_stmt_execute($insert_stmt)) {
    $response = ['success' => true];
} else {
    error_log("Failed to execute insert query: " . mysqli_error($conn));
    $response = ['success' => false];
}

echo json_encode($response);

mysqli_stmt_close($insert_stmt);
mysqli_close($conn);
