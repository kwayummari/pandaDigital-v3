<?php

/**
 * Maintenance Control System
 * Simple way to activate/deactivate maintenance mode
 */

// Admin key for remote access (change this to something secure)
$adminKey = 'panda_maintenance_2024';

// Security check - allow from localhost, server IP, or with admin key
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

// Get server IP to allow access from the same server
$serverIP = $_SERVER['SERVER_ADDR'] ?? '';
if ($serverIP) {
    $allowedIPs[] = $serverIP;
}

// Allow access if it's from the same server or has admin key
$isFromServer = in_array($clientIP, $allowedIPs);
$hasAdminKey = isset($_GET['admin_key']) && $_GET['admin_key'] === $adminKey;

if (!$isFromServer && !$hasAdminKey) {
    die('Access denied. This page is only accessible from the server or with admin key.<br><br>Use: <a href="?admin_key=' . $adminKey . '">' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?admin_key=' . $adminKey . '</a>');
}

// Check if admin key is provided
if (isset($_GET['admin_key']) && $_GET['admin_key'] !== $adminKey) {
    die('Invalid admin key.');
}

// Maintenance control file
$maintenanceFile = __DIR__ . '/.maintenance';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'activate') {
        // Create maintenance file
        file_put_contents($maintenanceFile, json_encode([
            'active' => true,
            'activated_at' => date('Y-m-d H:i:s'),
            'activated_by' => $clientIP,
            'message' => $_POST['message'] ?? 'Website under maintenance'
        ]));
        $success = 'Maintenance mode activated successfully!';
    } elseif ($action === 'deactivate') {
        // Remove maintenance file
        if (file_exists($maintenanceFile)) {
            unlink($maintenanceFile);
        }
        $success = 'Maintenance mode deactivated successfully!';
    }
}

// Check current maintenance status
$isMaintenanceActive = file_exists($maintenanceFile);
$maintenanceData = null;

if ($isMaintenanceActive) {
    $maintenanceData = json_decode(file_get_contents($maintenanceFile), true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Control - Panda Digital</title>
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
            max-width: 600px;
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
    <div class="control-container">
        <div class="control-card">
            <div class="text-center mb-4">
                <h1 class="display-4 text-primary">
                    <i class="fas fa-tools me-3"></i>Maintenance Control
                </h1>
                <p class="lead">Panda Digital Website Maintenance Management</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
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

                <?php if ($isMaintenanceActive && $maintenanceData): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Activated at:</small><br>
                            <strong><?= htmlspecialchars($maintenanceData['activated_at']) ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Activated by:</small><br>
                            <strong><?= htmlspecialchars($maintenanceData['activated_by']) ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($maintenanceData['message'])): ?>
                        <div class="mt-2">
                            <small class="text-muted">Message:</small><br>
                            <em><?= htmlspecialchars($maintenanceData['message']) ?></em>
                        </div>
                    <?php endif; ?>
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
                        <div class="mb-3">
                            <label for="message" class="form-label">Maintenance Message (Optional):</label>
                            <input type="text" class="form-control" id="message" name="message"
                                placeholder="Website under maintenance"
                                value="Tunafanya matengenezo muhimu kwenye tovuti yetu">
                        </div>
                        <button type="submit" class="btn btn-activate btn-maintenance">
                            <i class="fas fa-pause me-2"></i>Activate Maintenance Mode
                        </button>
                    </form>
                <?php endif; ?>
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

            <!-- Instructions -->
            <div class="mt-4">
                <h6 class="text-muted">
                    <i class="fas fa-question-circle me-2"></i>How to Use:
                </h6>
                <ul class="text-muted small">
                    <li><strong>Activate:</strong> Puts the website in maintenance mode, shows maintenance page to visitors</li>
                    <li><strong>Deactivate:</strong> Removes maintenance mode, website becomes live again</li>
                    <li><strong>Remote Access:</strong> Add <code>?admin_key=<?= $adminKey ?></code> to access remotely</li>
                    <li><strong>Auto-refresh:</strong> The maintenance page auto-refreshes every 30 seconds</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>