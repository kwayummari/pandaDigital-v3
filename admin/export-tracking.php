<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get filters from query parameters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$pageType = $_GET['page_type'] ?? '';
$userId = $_GET['user_id'] ?? '';

// Build WHERE clause for filtering
$whereConditions = [];
$params = [];

if ($startDate && $endDate) {
    $whereConditions[] = "DATE(t.visit_date) BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
}

if ($pageType) {
    $whereConditions[] = "t.page_type = ?";
    $params[] = $pageType;
}

if ($userId) {
    $whereConditions[] = "t.user_id = ?";
    $params[] = $userId;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get tracking data for export
$exportQuery = "
    SELECT 
        u.first_name,
        u.last_name,
        u.email,
        u.role,
        t.page_type,
        t.page_url,
        t.course_id,
        c.name as course_name,
        t.video_id,
        t.certificate_id,
        t.action_type,
        t.session_id,
        t.ip_address,
        t.user_agent,
        t.visit_date
    FROM user_page_tracking t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN course c ON t.course_id = c.id
    $whereClause
    ORDER BY t.visit_date DESC
";

$exportStmt = $pdo->prepare($exportQuery);
$exportStmt->execute($params);
$trackingData = $exportStmt->fetchAll();

// Set headers for CSV download
$filename = 'user_tracking_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper encoding
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV headers
$headers = [
    'First Name',
    'Last Name',
    'Email',
    'Role',
    'Page Type',
    'Page URL',
    'Course ID',
    'Course Name',
    'Video ID',
    'Certificate ID',
    'Action Type',
    'Session ID',
    'IP Address',
    'User Agent',
    'Visit Date'
];

fputcsv($output, $headers);

// Add data rows
foreach ($trackingData as $row) {
    $csvRow = [
        $row['first_name'] ?? '',
        $row['last_name'] ?? '',
        $row['email'] ?? '',
        $row['role'] ?? '',
        $row['page_type'] ?? '',
        $row['page_url'] ?? '',
        $row['course_id'] ?? '',
        $row['course_name'] ?? '',
        $row['video_id'] ?? '',
        $row['certificate_id'] ?? '',
        $row['action_type'] ?? '',
        $row['session_id'] ?? '',
        $row['ip_address'] ?? '',
        $row['user_agent'] ?? '',
        $row['visit_date'] ?? ''
    ];

    fputcsv($output, $csvRow);
}

fclose($output);
exit;
