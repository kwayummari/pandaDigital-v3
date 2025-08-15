<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/User.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

$userModel = new User();

// Get export format from request
$format = $_GET['format'] ?? 'csv';

if ($format === 'csv') {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="panda_digital_users_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: max-age=0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // CSV Headers
    $headers = [
        'ID',
        'Jina la Kwanza',
        'Jina la Mwisho',
        'Jina Kamili',
        'Barua Pepe',
        'Simu',
        'Mkoa',
        'Jukumu',
        'Hali ya Akaunti',
        'Tarehe ya Usajili',
        'Tarehe ya Mwisho ya Kuingia',
        'Idadi ya Kuingia',
        'Bio',
        'Mtaalam Mwenye Idhini',
        'Mwenye Biashara'
    ];

    fputcsv($output, $headers);

    // Get all users (without pagination for export)
    $users = $userModel->getAllUsersForExport();

    if (empty($users)) {
        // No users found
        fputcsv($output, ['No users found']);
        fclose($output);
        exit;
    }

    foreach ($users as $user) {
        $row = [
            $user['id'] ?? '',
            $user['first_name'] ?? '',
            $user['last_name'] ?? '',
            ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
            $user['email'] ?? '',
            ($user['phone'] && $user['phone'] !== 'null') ? '+255 ' . $user['phone'] : '',
            $user['region'] ?? '',
            $user['role'] ?? '',
            $user['account_status'] ?? '',
            $user['date_created'] ? date('d/m/Y H:i', strtotime($user['date_created'])) : '',
            $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '',
            $user['login_count'] ?? 0,
            $user['bio'] ?? '',
            $user['expert_authorization'] ?? '',
            $user['isSeller'] ? 'Ndio' : 'Hapana'
        ];

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
} else {
    // Invalid format
    header('Location: users.php?error=Invalid export format');
    exit;
}
