<?php

/**
 * Quick Maintenance Mode Enabler
 * Simple script to quickly enable/disable maintenance mode
 */

// Security check
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '';

if (!in_array($clientIP, $allowedIPs)) {
    die('Access denied. This script only works from localhost.');
}

$maintenanceFile = __DIR__ . '/.maintenance';

// Handle GET parameters for quick activation/deactivation
$action = $_GET['action'] ?? '';

if ($action === 'on') {
    // Activate maintenance mode
    file_put_contents($maintenanceFile, json_encode([
        'active' => true,
        'activated_at' => date('Y-m-d H:i:s'),
        'activated_by' => $clientIP,
        'message' => 'Website under maintenance'
    ]));
    echo "✅ Maintenance mode ACTIVATED!\n";
    echo "Visit: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/maintenance.php\n";
} elseif ($action === 'off') {
    // Deactivate maintenance mode
    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
    }
    echo "✅ Maintenance mode DEACTIVATED!\n";
    echo "Website is now LIVE!\n";
} elseif ($action === 'status') {
    // Check status
    if (file_exists($maintenanceFile)) {
        $data = json_decode(file_get_contents($maintenanceFile), true);
        echo "🔴 MAINTENANCE MODE IS ACTIVE\n";
        echo "Activated at: " . $data['activated_at'] . "\n";
        echo "Activated by: " . $data['activated_by'] . "\n";
    } else {
        echo "🟢 WEBSITE IS LIVE\n";
    }
} else {
    // Show usage instructions
    echo "Panda Digital Maintenance Control\n";
    echo "===============================\n\n";
    echo "Usage:\n";
    echo "  ?action=on    - Activate maintenance mode\n";
    echo "  ?action=off   - Deactivate maintenance mode\n";
    echo "  ?action=status - Check current status\n\n";
    echo "Examples:\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=on\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=off\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=status\n\n";

    // Show current status
    if (file_exists($maintenanceFile)) {
        $data = json_decode(file_get_contents($maintenanceFile), true);
        echo "Current Status: 🔴 MAINTENANCE MODE IS ACTIVE\n";
        echo "Activated at: " . $data['activated_at'] . "\n";
    } else {
        echo "Current Status: 🟢 WEBSITE IS LIVE\n";
    }
}
