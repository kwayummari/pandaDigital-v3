<?php
// Test page to verify .htaccess URL rewriting
$page_title = 'Test URLs';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test URLs - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3>Test .htaccess URL Rewriting</h3>
                    </div>
                    <div class="card-body">
                        <h5>Test these URLs (they should work without .php extension):</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="index" class="btn btn-primary btn-sm">index</a> 
                                <span class="text-muted">(should load index.php)</span>
                            </li>
                            <li class="list-group-item">
                                <a href="about" class="btn btn-primary btn-sm">about</a> 
                                <span class="text-muted">(should load about.php)</span>
                            </li>
                            <li class="list-group-item">
                                <a href="kozi" class="btn btn-primary btn-sm">kozi</a> 
                                <span class="text-muted">(should load kozi.php)</span>
                            </li>
                            <li class="list-group-item">
                                <a href="fursa" class="btn btn-primary btn-sm">fursa</a> 
                                <span class="text-muted">(should load fursa.php)</span>
                            </li>
                            <li class="list-group-item">
                                <a href="habari" class="btn btn-primary btn-sm">habari</a> 
                                <span class="text-muted">(should load habari.php)</span>
                            </li>
                            <li class="list-group-item">
                                <a href="contact" class="btn btn-primary btn-sm">contact</a> 
                                <span class="text-muted">(should load contact.php)</span>
                            </li>
                        </ul>
                        
                        <hr>
                        
                        <h5>Test Right-Click Disabling:</h5>
                        <div class="alert alert-info">
                            <strong>Try to:</strong>
                            <ul class="mb-0">
                                <li>Right-click anywhere on this page</li>
                                <li>Press F12 key</li>
                                <li>Press Ctrl+Shift+I</li>
                                <li>Press Ctrl+U</li>
                                <li>Press Ctrl+S</li>
                            </ul>
                            <p class="mb-0 mt-2"><strong>All of these should be blocked and show a warning message!</strong></p>
                        </div>
                        
                        <hr>
                        
                        <h5>Current URL Information:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Current Script:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'N/A'; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>PHP Self:</strong> <?php echo $_SERVER['PHP_SELF'] ?? 'N/A'; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Query String:</strong> <?php echo $_SERVER['QUERY_STRING'] ?? 'None'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
