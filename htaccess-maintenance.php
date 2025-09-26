<?php

/**
 * .htaccess Maintenance Mode Controller
 * Simple script to activate/deactivate maintenance mode using .htaccess
 */

// Security check
$adminKey = 'panda_maintenance_2024';
$hasAdminKey = isset($_GET['admin_key']) && $_GET['admin_key'] === $adminKey;

if (!$hasAdminKey) {
    die('Access denied. Use: <a href="?admin_key=' . $adminKey . '">' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?admin_key=' . $adminKey . '</a>');
}

$htaccessFile = __DIR__ . '/.htaccess';
$action = $_GET['action'] ?? '';

if ($action === 'on') {
    // Activate maintenance mode
    $htaccessContent = file_get_contents($htaccessFile);

    // Uncomment maintenance mode lines
    $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/maintenance\.php$', 'RewriteCond %{REQUEST_URI} !^/maintenance\.php$', $htaccessContent);
    $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', 'RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', $htaccessContent);
    $htaccessContent = str_replace('# RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', 'RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', $htaccessContent);
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

    echo "✅ MAINTENANCE MODE ACTIVATED!\n";
    echo "All visitors will see the maintenance page.\n";
    echo "Admin areas and static files are still accessible.\n\n";
    echo "Visit your website to see the maintenance page.\n";
} elseif ($action === 'off') {
    // Deactivate maintenance mode
    $htaccessContent = file_get_contents($htaccessFile);

    // Comment out maintenance mode lines
    $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/maintenance\.php$', '# RewriteCond %{REQUEST_URI} !^/maintenance\.php$', $htaccessContent);
    $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', '# RewriteCond %{REQUEST_URI} !^/maintenance-control\.php$', $htaccessContent);
    $htaccessContent = str_replace('RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', '# RewriteCond %{REQUEST_URI} !^/enable-maintenance\.php$', $htaccessContent);
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

    echo "✅ MAINTENANCE MODE DEACTIVATED!\n";
    echo "Website is now LIVE and accessible to all visitors.\n";
} elseif ($action === 'status') {
    // Check status
    $htaccessContent = file_get_contents($htaccessFile);
    $isMaintenanceActive = strpos($htaccessContent, 'RewriteCond %{REQUEST_URI} !^/maintenance\.php$') !== false &&
        strpos($htaccessContent, '# RewriteCond %{REQUEST_URI} !^/maintenance\.php$') === false;

    if ($isMaintenanceActive) {
        echo "🔴 MAINTENANCE MODE IS ACTIVE\n";
        echo "All visitors see the maintenance page.\n";
    } else {
        echo "🟢 WEBSITE IS LIVE\n";
        echo "All visitors can access the website normally.\n";
    }
} else {
    // Show usage instructions
    echo "Panda Digital .htaccess Maintenance Control\n";
    echo "==========================================\n\n";
    echo "This system uses .htaccess to control maintenance mode.\n";
    echo "It's more reliable than PHP-based systems.\n\n";
    echo "Usage:\n";
    echo "  ?action=on     - Activate maintenance mode\n";
    echo "  ?action=off    - Deactivate maintenance mode\n";
    echo "  ?action=status - Check current status\n\n";
    echo "Examples:\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=on&admin_key=" . $adminKey . "\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=off&admin_key=" . $adminKey . "\n";
    echo "  " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?action=status&admin_key=" . $adminKey . "\n\n";

    // Show current status
    $htaccessContent = file_get_contents($htaccessFile);
    $isMaintenanceActive = strpos($htaccessContent, 'RewriteCond %{REQUEST_URI} !^/maintenance\.php$') !== false &&
        strpos($htaccessContent, '# RewriteCond %{REQUEST_URI} !^/maintenance\.php$') === false;

    if ($isMaintenanceActive) {
        echo "Current Status: 🔴 MAINTENANCE MODE IS ACTIVE\n";
    } else {
        echo "Current Status: 🟢 WEBSITE IS LIVE\n";
    }

    echo "\nAdvantages of .htaccess method:\n";
    echo "✅ Works at server level (more reliable)\n";
    echo "✅ No PHP execution needed\n";
    echo "✅ Faster response\n";
    echo "✅ Works even if PHP fails\n";
    echo "✅ Allows access to admin areas\n";
    echo "✅ Preserves static files (CSS, JS, images)\n";
}
