<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Course.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

$format = $_GET['format'] ?? 'csv';

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="panda_digital_courses_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

    // CSV headers
    $headers = [
        'ID',
        'Jina la Kozi',
        'Maelezo',
        'Mwalimu',
        'Bei',
        'Hali',
        'Tarehe ya Uundaji',
        'Idadi ya Video',
        'Idadi ya Wanafunzi'
    ];
    fputcsv($output, $headers);

    // Get courses data
    $courseModel = new Course();
    $courses = $courseModel->getAllCoursesForAdmin(1, 10000); // Get all courses

    if (empty($courses)) {
        fputcsv($output, ['No courses found']);
    } else {
        foreach ($courses as $course) {
            $row = [
                $course['id'] ?? 'N/A',
                $course['title'] ?? 'N/A',
                $course['description'] ?? 'N/A',
                $course['instructor_name'] ?? 'N/A',
                !empty($course['price']) && $course['price'] > 0 ? 'TSh ' . number_format($course['price']) : 'Bure',
                $course['status'] ?? 'N/A',
                !empty($course['created_at']) ? date('d/m/Y H:i', strtotime($course['created_at'])) : 'N/A',
                $course['total_videos'] ?? 0,
                $course['enrollment_count'] ?? 0
            ];
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit;
} else {
    // For now, only CSV is supported
    echo "Only CSV export is currently supported.";
}
