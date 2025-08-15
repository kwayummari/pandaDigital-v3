<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

$auth = new AuthMiddleware();
$auth->requireRole('expert');

$currentUser = $auth->getCurrentUser();

// Set page title
$page_title = 'Uthibitisho Unasubiriwa';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - <?= htmlspecialchars($appConfig['name']) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=8">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-clock fa-4x text-warning"></i>
                        </div>
                        
                        <h2 class="card-title text-dark mb-3">Uthibitisho Unasubiriwa</h2>
                        
                        <p class="text-muted mb-4">
                            Asante kwa kujisajili kama mtaalam! Ombi lako la uthibitisho linachunguzwa na timu yetu. 
                            Tutakupigia simu au kutuma barua pepe kuhusu uamuzi wetu.
                        </p>
                        
                        <div class="alert alert-info text-start">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Mambo Muhimu:
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li>Uthibitisho hufanywa ndani ya siku 2-3 za kazi</li>
                                <li>Tutakupigia simu kwenye namba uliyoandika</li>
                                <li>Unaweza kuangalia hali ya ombi lako hapa</li>
                                <li>Baada ya uthibitisho, utaweza kujibu maswali ya wanafunzi</li>
                            </ul>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted">Jina</h6>
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted">Barua Pepe</h6>
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($currentUser['email']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted">Namba ya Simu</h6>
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($currentUser['phone'] ?? 'Haijulikani') ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted">Mkoa</h6>
                                        <p class="mb-0 fw-bold"><?= htmlspecialchars($currentUser['region'] ?? 'Haijulikani') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="<?= app_url('logout.php') ?>" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Toka
                            </a>
                            <button type="button" class="btn btn-primary" onclick="checkStatus()">
                                <i class="fas fa-sync-alt me-2"></i>
                Angalia Hali
                            </button>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                Kama una swali, wasiliana nasi kupitia: 
                                <a href="mailto:support@pandadigital.co.tz">support@pandadigital.co.tz</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function checkStatus() {
            // Reload the page to check if status has changed
            window.location.reload();
        }
        
        // Auto-refresh every 30 seconds to check status
        setInterval(function() {
            // Make AJAX call to check status
            fetch('<?= app_url("api/check-expert-status.php") ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.authorized) {
                        window.location.href = '<?= app_url("admin/expert-dashboard.php") ?>';
                    }
                })
                .catch(error => {
                    console.log('Checking status...');
                });
        }, 30000);
    </script>
</body>
</html>
