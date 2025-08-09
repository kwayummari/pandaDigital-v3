<?php
require_once 'config/init.php';
require_once 'models/Blog.php';
require_once 'models/Fursa.php';

// Initialize models
$blogModel = new Blog();
$fursaModel = new Fursa();

// Fetch data from database
$latestBlogPosts = $blogModel->getLatestPosts(3);
$latestOpportunities = $fursaModel->getLatestOpportunities(3);
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Test - Panda Digital V3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">🐼 Database Test - Panda Digital V3</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📰 Latest Blog Posts (<?= count($latestBlogPosts) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($latestBlogPosts)): ?>
                            <?php foreach ($latestBlogPosts as $post): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <h6><?= htmlspecialchars($post['name']) ?></h6>
                                    <p class="text-muted small"><?= $blogModel->formatDate($post['date_created']) ?></p>
                                    <p class="small"><?= $blogModel->truncateText($post['maelezo'], 100) ?></p>
                                    <?php if ($post['photo']): ?>
                                        <small class="text-info">📷 Image: <?= htmlspecialchars($post['photo']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No blog posts found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>💡 Latest Opportunities (<?= count($latestOpportunities) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($latestOpportunities)): ?>
                            <?php foreach ($latestOpportunities as $opportunity): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <h6><?= htmlspecialchars($opportunity['name']) ?></h6>
                                    <p class="text-muted small">📅 <?= htmlspecialchars($opportunity['date']) ?> <?= htmlspecialchars($opportunity['month']) ?></p>
                                    <p class="small"><?= $fursaModel->truncateText($opportunity['description'], 100) ?></p>
                                    <?php if ($opportunity['image']): ?>
                                        <small class="text-info">🖼️ Image: <?= htmlspecialchars($opportunity['image']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No opportunities found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">🏠 Go to Homepage</a>
            <a href="setup.php" class="btn btn-secondary">⚙️ Setup Wizard</a>
        </div>
    </div>
</body>

</html>