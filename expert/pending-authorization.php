<?php
require_once __DIR__ . "/../services/AuthService.php";

$authService = new AuthService();

// Check if user is logged in and is an expert
if (!$authService->isLoggedIn()) {
    header('Location: /login.php');
    exit();
}

$currentUser = $authService->getCurrentUser();
if ($currentUser['role'] !== 'expert') {
    header('Location: /unauthorized.php');
    exit();
}

// Check if expert is already authorized
if (isset($currentUser['expert_authorization']) && $currentUser['expert_authorization'] == 1) {
    header('Location: /expert/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subiri Kuidhinishwa - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pending-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .pending-header {
            background: #ffc107;
            color: #856404;
            padding: 40px 30px;
        }

        .pending-header i {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .pending-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .pending-body {
            padding: 40px 30px;
        }

        .status-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }

        .status-info h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }

        .status-info p {
            color: #1565c0;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .timeline {
            text-align: left;
            margin: 30px 0;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .timeline-icon.completed {
            background: #28a745;
            color: white;
        }

        .timeline-icon.pending {
            background: #ffc107;
            color: #856404;
        }

        .timeline-icon.waiting {
            background: #6c757d;
            color: white;
        }

        .timeline-content h5 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }

        .timeline-content p {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .btn-primary {
            background: #667eea;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            margin: 10px;
        }

        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            margin: 10px;
        }

        .contact-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .contact-info h5 {
            color: #856404;
            margin-bottom: 15px;
        }

        .contact-info p {
            color: #856404;
            margin-bottom: 10px;
        }

        .contact-info a {
            color: #856404;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="pending-card">
            <div class="pending-header">
                <i class="fas fa-clock"></i>
                <h1>Subiri Kuidhinishwa</h1>
                <p>Ombi lako la kuwa mtaalam limetumwa kikamilifu!</p>
            </div>

            <div class="pending-body">
                <div class="status-info">
                    <h3><i class="fas fa-info-circle"></i> Hali ya Ombi</h3>
                    <p>Ombi lako la kuwa mtaalam bado linachunguzwa na wasimamizi.
                        Utapata taarifa kupitia barua pepe yako mara tu ombi litakapoidhinishwa au kukataliwa.</p>
                </div>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h5>Usajili Umekamilika</h5>
                            <p>Akaunti yako imeundwa kikamilifu</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h5>Ombi la Mtaalam Limetumwa</h5>
                            <p>Ombi lako la kuwa mtaalam limetumwa kwa wasimamizi</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon waiting">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="timeline-content">
                            <h5>Kuchunguzwa</h5>
                            <p>Wasimamizi wanachunguza ombi lako</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon pending">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="timeline-content">
                            <h5>Kuidhinishwa</h5>
                            <p>Utapata taarifa kupitia barua pepe</p>
                        </div>
                    </div>
                </div>

                <div class="contact-info">
                    <h5><i class="fas fa-phone"></i> Uhitaji Msaada?</h5>
                    <p>Ikiwa una maswali au unahitaji msaada, wasiliana nasi:</p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:support@pandadigital.co.tz">support@pandadigital.co.tz</a></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:+255123456789">+255 123 456 789</a></p>
                    <p><i class="fas fa-clock"></i> Jumatano - Ijumaa: 8:00 AM - 6:00 PM</p>
                </div>

                <div class="mt-4">
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i> Rudi Nyumbani
                    </a>
                    <a href="/login.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sign-in-alt me-2"></i> Ingia
                    </a>
                </div>

                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Muda wa kuchunguza ombi ni kati ya siku 2-5 za kazi
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh status every 30 seconds
        setInterval(function() {
            // Check if user has been authorized
            fetch('/api/check-expert-status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.authorized) {
                        window.location.href = '/expert/dashboard.php';
                    }
                })
                .catch(error => {
                    console.log('Status check failed:', error);
                });
        }, 30000);
    </script>
</body>

</html>
