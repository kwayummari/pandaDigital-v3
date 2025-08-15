<?php
include "../admin/includes/db/connect.php";

$chatId = $_GET['chatId'];

$messages_query = "SELECT * FROM productMessages WHERE chatId = ? ORDER BY date ASC";
$messages_stmt = mysqli_prepare($conn, $messages_query);
mysqli_stmt_bind_param($messages_stmt, "i", $chatId);
mysqli_stmt_execute($messages_stmt);
$messages_result = mysqli_stmt_get_result($messages_stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($messages_result)) {
    $messages[] = [
        'type' => $row['type'],
        'content' => $row['content']
    ];
}

echo json_encode(['messages' => $messages]);

mysqli_stmt_close($messages_stmt);
mysqli_close($conn);

