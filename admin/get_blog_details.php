<?php
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Blog.php";

// Check if user is admin
$auth = new AuthMiddleware();
$auth->requireRole('admin');

// Get blog ID from request
$blogId = $_GET['id'] ?? null;

if (!$blogId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Blog ID haijatolewa'
    ]);
    exit;
}

try {
    // Initialize Blog model
    $blogModel = new Blog();

    // Get blog details
    $blog = $blogModel->getBlogById($blogId);

    // Debug logging
    error_log("Blog ID requested: " . $blogId);
    error_log("Blog data returned: " . json_encode($blog));

    if (!$blog) {
        echo json_encode([
            'success' => false,
            'message' => 'Blog haijapatikana'
        ]);
        exit;
    }

    // Return blog data
    echo json_encode([
        'success' => true,
        'blog' => $blog
    ]);
} catch (Exception $e) {
    error_log("Error getting blog details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Kuna tatizo la kupata maelezo ya blog'
    ]);
}
