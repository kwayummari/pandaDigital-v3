<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panda Digital - .htaccess Maintenance Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #37ABA6 0%, #2c7a7a 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .control-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .control-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 700px;
            width: 100%;
            backdrop-filter: blur(10px);
        }

        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .status-active {
            background: #dc3545;
            animation: pulse 2s infinite;
        }

        .status-inactive {
            background: #28a745;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .btn-maintenance {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-activate {
            background: #dc3545;
            border: none;
            color: white;
        }

        .btn-activate:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-deactivate {
            background: #28a745;
            border: none;
            color: white;
        }

        .btn-deactivate:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .advantages {
            background: #e8f5e8;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .quick-links {
            background: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .quick-links a {
            display: inline-block;
            margin: 5px 10px 5px 0;
            padding: 8px 16px;
            background: #37ABA6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .quick-links a:hover {
            background: #2c7a7a;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php
    $adminKey = 'panda_maintenance_2024';
    $hasAdminKey = isset($_GET['admin_key']) && $_GET['admin_key'] === $adminKey;

    if (!$hasAdminKey) {
        echo '<div class="control-container"><div class="control-card text-center">';
        echo '<h1 class="text-danger"><i class="fas fa-lock me-2"></i>Access Denied</h1>';
        echo '<p>This page requires an admin key for security.</p>';
        echo '<a href="?admin_key=' . $adminKey . '" class="btn btn-primary btn-lg">Access Control Panel</a>';
        echo '</div></div>';
        exit;
    }

    $htaccessFile = __DIR__ . '/.htaccess';
    $action = $_GET['action'] ?? '';
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'activate') {
            // Activate maintenance mode
            $htaccessContent = file_get_contents($htaccessFile);

            // Uncomment maintenance mode lines
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/maintenance\.php$', 'RewriteCond %{REQUEST_URI} !^/maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', 'RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', 'RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/htaccess-maintenance\.php$', 'RewriteCond %{REQUEST_URI} !^/htaccess-maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/htaccess-control\.php$', 'RewriteCond %{REQUEST_URI} !^/htaccess-control\.php$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/admin/', 'RewriteCond %{REQUEST_URI} !^/admin/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/api/', 'RewriteCond %{REQUEST_URI} !^/api/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/assets/', 'RewriteCond %{REQUEST_URI} !^/assets/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/images/', 'RewriteCond %{REQUEST_URI} !^/images/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/css/', 'RewriteCond %{REQUEST_URI} !^/css/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/js/', 'RewriteCond %{REQUEST_URI} !^/js/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/plugins/', 'RewriteCond %{REQUEST_URI} !^/plugins/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/vendor/', 'RewriteCond %{REQUEST_URI} !^/vendor/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/uploads/', 'RewriteCond %{REQUEST_URI} !^/uploads/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/sessions/', 'RewriteCond %{REQUEST_URI} !^/sessions/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/cache/', 'RewriteCond %{REQUEST_URI} !^/cache/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/logs/', 'RewriteCond %{REQUEST_URI} !^/logs/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/database/', 'RewriteCond %{REQUEST_URI} !^/database/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/dump_database/', 'RewriteCond %{REQUEST_URI} !^/dump_database/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/\.well-known/', 'RewriteCond %{REQUEST_URI} !^/\.well-known/', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/favicon\.ico$', 'RewriteCond %{REQUEST_URI} !^/favicon\.ico$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/robots\.txt$', 'RewriteCond %{REQUEST_URI} !^/robots\.txt$', $htaccessContent);
            $htaccessContent = str_replace('# RewriteRule ^(.*)$ /maintenance.php [R=503,L]', 'RewriteRule ^(.*)$ /maintenance.php [R=503,L]', $htaccessContent);

            // Comment out normal URL rewriting
            $htaccessContent = str_replace('RewriteCond %{REQUEST_FILENAME} !-f', '# RewriteCond %{REQUEST_FILENAME} !-f', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_FILENAME} !-d', '# RewriteCond %{REQUEST_FILENAME} !-d', $htaccessContent);
            $htaccessContent = str_replace('RewriteRule ^(.*)$ index.php [QSA,L]', '# RewriteRule ^(.*)$ index.php [QSA,L]', $htaccessContent);

            file_put_contents($htaccessFile, $htaccessContent);
            $message = 'success:Maintenance mode activated successfully! All visitors will see the maintenance page.';
        } elseif ($action === 'deactivate') {
            // Deactivate maintenance mode
            $htaccessContent = file_get_contents($htaccessFile);

            // Comment out maintenance mode lines
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/maintenance\.php$', '# RewriteCond %{REQUEST_URI} !^/maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', '# RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', '# RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/htaccess-maintenance\.php$', '# RewriteCond %{REQUEST_URI} !^/htaccess-maintenance\.php$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/htaccess-control\.php$', '# RewriteCond %{REQUEST_URI} !^/htaccess-control\.php$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/admin/', '# RewriteCond %{REQUEST_URI} !^/admin/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/api/', '# RewriteCond %{REQUEST_URI} !^/api/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/assets/', '# RewriteCond %{REQUEST_URI} !^/assets/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/images/', '# RewriteCond %{REQUEST_URI} !^/images/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/css/', '# RewriteCond %{REQUEST_URI} !^/css/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/js/', '# RewriteCond %{REQUEST_URI} !^/js/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/plugins/', '# RewriteCond %{REQUEST_URI} !^/plugins/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/vendor/', '# RewriteCond %{REQUEST_URI} !^/vendor/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/uploads/', '# RewriteCond %{REQUEST_URI} !^/uploads/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/sessions/', '# RewriteCond %{REQUEST_URI} !^/sessions/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/cache/', '# RewriteCond %{REQUEST_URI} !^/cache/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/logs/', '# RewriteCond %{REQUEST_URI} !^/logs/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/database/', '# RewriteCond %{REQUEST_URI} !^/database/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/dump_database/', '# RewriteCond %{REQUEST_URI} !^/dump_database/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/\.well-known/', '# RewriteCond %{REQUEST_URI} !^/\.well-known/', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/favicon\.ico$', '# RewriteCond %{REQUEST_URI} !^/favicon\.ico$', $htaccessContent);
            $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/robots\.txt$', '# RewriteCond %{REQUEST_URI} !^/robots\.txt$', $htaccessContent);
            $htaccessContent = str_replace('RewriteRule ^(.*)$ /maintenance.php [R=503,L]', '# RewriteRule ^(.*)$ /maintenance.php [R=503,L]', $htaccessContent);

            // Uncomment normal URL rewriting
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_FILENAME} !-f', 'RewriteCond %{REQUEST_FILENAME} !-f', $htaccessContent);
            $htaccessContent = str_replace('# RewriteCond %{REQUEST_FILENAME} !-d', 'RewriteCond %{REQUEST_FILENAME} !-d', $htaccessContent);
            $htaccessContent = str_replace('# RewriteRule ^(.*)$ index.php [QSA,L]', 'RewriteRule ^(.*)$ index.php [QSA,L]', $htaccessContent);

            file_put_contents($htaccessFile, $htaccessContent);
            $message = 'success:Maintenance mode deactivated successfully! Website is now live.';
        }
    }

    // Check current status
    $htaccessContent = file_get_contents($htaccessFile);
    $isMaintenanceActive = strpos($htaccessContent, 'RewriteCond %{REQUEST_URI} !^/maintenance\.php$') !== false &&
        strpos($htaccessContent, '# RewriteCond %{REQUEST_URI} !^/maintenance\.php$') === false;
    ?>

    <div class="control-container">
        <div class="control-card">
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-cogs me-3"></i>.htaccess Maintenance Control
                </h1>
                <p class="lead">Server-Level Maintenance Mode Management</p>
            </div>

            <?php if ($message): ?>
                <?php
                list($type, $text) = explode(':', $message, 2);
                $alertClass = $type === 'success' ? 'alert-success' : 'alert-danger';
                ?>
                <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                    <?= htmlspecialchars($text) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Current Status -->
            <div class="info-box">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>Current Status
                </h5>
                <div class="d-flex align-items-center mb-3">
                    <span class="status-indicator <?= $isMaintenanceActive ? 'status-active' : 'status-inactive' ?>"></span>
                    <strong>
                        <?= $isMaintenanceActive ? 'MAINTENANCE MODE ACTIVE' : 'WEBSITE IS LIVE' ?>
                    </strong>
                </div>

                <?php if ($isMaintenanceActive): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        All visitors are being redirected to the maintenance page.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Website is accessible to all visitors.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Control Buttons -->
            <div class="text-center">
                <?php if ($isMaintenanceActive): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="deactivate">
                        <button type="submit" class="btn btn-deactivate btn-maintenance">
                            <i class="fas fa-play me-2"></i>Deactivate Maintenance Mode
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="activate">
                        <button type="submit" class="btn btn-activate btn-maintenance">
                            <i class="fas fa-pause me-2"></i>Activate Maintenance Mode
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Advantages -->
            <div class="advantages">
                <h6 class="mb-3">
                    <i class="fas fa-star me-2"></i>Advantages of .htaccess Method
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Works at server level</li>
                            <li><i class="fas fa-check text-success me-2"></i>No PHP execution needed</li>
                            <li><i class="fas fa-check text-success me-2"></i>Faster response time</li>
                            <li><i class="fas fa-check text-success me-2"></i>Works even if PHP fails</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Allows admin access</li>
                            <li><i class="fas fa-check text-success me-2"></i>Preserves static files</li>
                            <li><i class="fas fa-check text-success me-2"></i>More reliable</li>
                            <li><i class="fas fa-check text-success me-2"></i>SEO friendly (503 status)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="quick-links">
                <h6 class="mb-3">
                    <i class="fas fa-link me-2"></i>Quick Links
                </h6>
                <a href="index.php" target="_blank">
                    <i class="fas fa-home me-1"></i>Homepage
                </a>
                <a href="maintenance.php" target="_blank">
                    <i class="fas fa-tools me-1"></i>Maintenance Page
                </a>
                <a href="admin/" target="_blank">
                    <i class="fas fa-cog me-1"></i>Admin Panel
                </a>
                <a href="?admin_key=<?= $adminKey ?>&refresh=1">
                    <i class="fas fa-refresh me-1"></i>Refresh Status
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>