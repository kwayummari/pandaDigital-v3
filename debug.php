<?php
require_once 'config/init.php';
require_once 'models/Blog.php';
require_once 'models/Fursa.php';

// Initialize models
$blogModel = new Blog();
$fursaModel = new Fursa();

// Fetch data from database
$latestBlogPosts = $blogModel->getLatestPosts(2);
$latestOpportunities = $fursaModel->getLatestOpportunities(2);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Panda Digital V3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">🐛 Debug - Panda Digital V3</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🔧 System Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>APP_URL:</strong> <?= env('APP_URL') ?></p>
                        <p><strong>CSS URL:</strong> <?= asset('css/style.css') ?></p>
                        <p><strong>Uploads URL:</strong> <?= upload_url('Blog/') ?></p>
                        <p><strong>Current Directory:</strong> <?= __DIR__ ?></p>
                        <p><strong>CSS File Exists:</strong> <?= file_exists(__DIR__ . '/assets/css/style.css') ? '✅ Yes' : '❌ No' ?></p>
                        <p><strong>Uploads Directory:</strong> <?= is_dir(__DIR__ . '/uploads') ? '✅ Yes' : '❌ No' ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🗄️ Database Test</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Blog Posts Found:</strong> <?= count($latestBlogPosts) ?></p>
                        <p><strong>Opportunities Found:</strong> <?= count($latestOpportunities) ?></p>

                        <?php if (!empty($latestBlogPosts)): ?>
                            <h6>Sample Blog Post:</h6>
                            <p><strong>Title:</strong> <?= htmlspecialchars($latestBlogPosts[0]['name']) ?></p>
                            <p><strong>Image:</strong> <?= htmlspecialchars($latestBlogPosts[0]['photo']) ?></p>
                            <p><strong>Image URL:</strong> <?= $blogModel->getImageUrl($latestBlogPosts[0]['photo']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($latestOpportunities)): ?>
                            <h6>Sample Opportunity:</h6>
                            <p><strong>Title:</strong> <?= htmlspecialchars($latestOpportunities[0]['name']) ?></p>
                            <p><strong>Image:</strong> <?= htmlspecialchars($latestOpportunities[0]['image']) ?></p>
                            <p><strong>Image URL:</strong> <?= $fursaModel->getImageUrl($latestOpportunities[0]['image']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">🏠 Go to Homepage</a>
            <a href="test-db.php" class="btn btn-secondary">📊 Database Test</a>
            <a href="setup.php" class="btn btn-info">⚙️ Setup Wizard</a>
        </div>
    </div>
</body>

</html>