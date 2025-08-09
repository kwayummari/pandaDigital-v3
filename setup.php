<?php
/**
 * Panda Digital V3 - Setup Script
 * Run this file to configure your environment and test the installation
 */

// Prevent direct access in production
if (php_sapi_name() !== 'cli' && !isset($_GET['setup'])) {
    die('Setup script can only be accessed with ?setup parameter');
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Panda Digital V3 - Setup</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f8f9fa; }
        .setup-card { max-width: 800px; margin: 2rem auto; }
        .status-success { color: #198754; }
        .status-error { color: #dc3545; }
        .status-warning { color: #fd7e14; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='card setup-card'>
            <div class='card-header bg-primary text-white'>
                <h3 class='mb-0'>🐼 Panda Digital V3 - Setup Wizard</h3>
            </div>
            <div class='card-body'>";

// Check PHP version
echo "<h5>📋 System Requirements Check</h5>";
$requirements = [];

// PHP Version
$phpVersion = phpversion();
$minPhpVersion = '8.0.0';
if (version_compare($phpVersion, $minPhpVersion, '>=')) {
    echo "<p class='status-success'>✅ PHP Version: $phpVersion (Required: $minPhpVersion)</p>";
    $requirements['php'] = true;
} else {
    echo "<p class='status-error'>❌ PHP Version: $phpVersion (Required: $minPhpVersion)</p>";
    $requirements['php'] = false;
}

// PDO Extension
if (extension_loaded('pdo')) {
    echo "<p class='status-success'>✅ PDO Extension: Available</p>";
    $requirements['pdo'] = true;
} else {
    echo "<p class='status-error'>❌ PDO Extension: Not available</p>";
    $requirements['pdo'] = false;
}

// PDO MySQL Extension
if (extension_loaded('pdo_mysql')) {
    echo "<p class='status-success'>✅ PDO MySQL Extension: Available</p>";
    $requirements['pdo_mysql'] = true;
} else {
    echo "<p class='status-error'>❌ PDO MySQL Extension: Not available</p>";
    $requirements['pdo_mysql'] = false;
}

// File permissions
echo "<h5 class='mt-4'>📁 File Permissions Check</h5>";
$directories = ['uploads', 'logs', 'cache'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (is_writable($dir)) {
        echo "<p class='status-success'>✅ Directory '$dir': Writable</p>";
    } else {
        echo "<p class='status-error'>❌ Directory '$dir': Not writable</p>";
    }
}

// Environment file check
echo "<h5 class='mt-4'>⚙️ Environment Configuration</h5>";
if (file_exists('.env')) {
    echo "<p class='status-success'>✅ .env file exists</p>";
} else {
    echo "<p class='status-warning'>⚠️ .env file not found</p>";
    echo "<p>Please copy <code>env.example</code> to <code>.env</code> and configure your settings.</p>";
    
    if (file_exists('env.example')) {
        echo "<div class='alert alert-info'>
            <strong>Quick Setup:</strong><br>
            <code>cp env.example .env</code><br>
            Then edit .env with your database credentials and other settings.
        </div>";
    }
}

// Database connection test
echo "<h5 class='mt-4'>🗄️ Database Connection Test</h5>";
if (file_exists('config/Environment.php')) {
    require_once 'config/Environment.php';
    require_once 'config/database.php';
    
    Environment::load();
    $db = new Database();
    
    if ($db->testConnection()) {
        echo "<p class='status-success'>✅ Database connection successful</p>";
        $requirements['database'] = true;
    } else {
        echo "<p class='status-error'>❌ Database connection failed</p>";
        echo "<p>Please check your database configuration in .env file</p>";
        $requirements['database'] = false;
    }
} else {
    echo "<p class='status-error'>❌ Configuration files not found</p>";
    $requirements['database'] = false;
}

// Security check
echo "<h5 class='mt-4'>🔒 Security Check</h5>";
$appKey = Environment::get('APP_KEY', '');
if (strlen($appKey) >= 32) {
    echo "<p class='status-success'>✅ Application key is set</p>";
} else {
    echo "<p class='status-warning'>⚠️ Application key is weak or not set</p>";
    echo "<p>Generate a strong 32-character key in your .env file</p>";
}

// Summary
echo "<h5 class='mt-4'>📊 Setup Summary</h5>";
$allRequirementsMet = !in_array(false, $requirements);

if ($allRequirementsMet) {
    echo "<div class='alert alert-success'>
        <h6>🎉 Setup Complete!</h6>
        <p>All requirements are met. Your Panda Digital V3 installation is ready to use.</p>
        <a href='index.php' class='btn btn-success'>Go to Homepage</a>
    </div>";
} else {
    echo "<div class='alert alert-warning'>
        <h6>⚠️ Setup Incomplete</h6>
        <p>Some requirements are not met. Please fix the issues above before proceeding.</p>
    </div>";
}

// Configuration guide
echo "<h5 class='mt-4'>📖 Configuration Guide</h5>";
echo "<div class='accordion' id='configGuide'>
    <div class='accordion-item'>
        <h2 class='accordion-header'>
            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#databaseConfig'>
                Database Configuration
            </button>
        </h2>
        <div id='databaseConfig' class='accordion-collapse collapse' data-bs-parent='#configGuide'>
            <div class='accordion-body'>
                <p>Configure your database settings in the .env file:</p>
                <pre><code>DB_HOST=localhost
DB_NAME=pandadigital
DB_USER=root
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4</code></pre>
            </div>
        </div>
    </div>
    
    <div class='accordion-item'>
        <h2 class='accordion-header'>
            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#appConfig'>
                Application Configuration
            </button>
        </h2>
        <div id='appConfig' class='accordion-collapse collapse' data-bs-parent='#configGuide'>
            <div class='accordion-body'>
                <p>Configure your application settings:</p>
                <pre><code>APP_NAME=\"Panda Digital\"
APP_URL=http://localhost/pandadigitalV3
APP_ENV=development
APP_DEBUG=true
APP_KEY=your-32-character-secret-key-here</code></pre>
            </div>
        </div>
    </div>
    
    <div class='accordion-item'>
        <h2 class='accordion-header'>
            <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#mailConfig'>
                Mail Configuration
            </button>
        </h2>
        <div id='mailConfig' class='accordion-collapse collapse' data-bs-parent='#configGuide'>
            <div class='accordion-body'>
                <p>Configure email settings for notifications:</p>
                <pre><code>MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls</code></pre>
            </div>
        </div>
    </div>
</div>";

echo "</div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 