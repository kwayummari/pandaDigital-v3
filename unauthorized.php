<?php
require_once __DIR__ . "/services/AuthService.php";

$authService = new AuthService();
$isLoggedIn = $authService->isLoggedIn();
$currentUser = $isLoggedIn ? $authService->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauna Ruhusa - Panda Digital</title>
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

        .unauthorized-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .unauthorized-header {
            background: #dc3545;
            color: white;
            padding: 40px 30px;
        }

        .unauthorized-header i {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .unauthorized-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .unauthorized-body {
            padding: 40px 30px;
        }

        .error-info {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }

        .error-info h3 {
            color: #721c24;
            margin-bottom: 15px;
        }

        .error-info p {
            color: #721c24;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .suggestions {
            text-align: left;
            margin: 30px 0;
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .suggestion-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            background: #667eea;
            color: white;
        }

        .suggestion-content h5 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }

        .suggestion-content p {
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
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .contact-info h5 {
            color: #1976d2;
            margin-bottom: 15px;
        }

        .contact-info p {
            color: #1565c0;
            margin-bottom: 10px;
        }

        .contact-info a {
            color: #1976d2;
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
        <div class="unauthorized-card">
            <div class="unauthorized-header">
                <i class="fas fa-ban"></i>
                <h1>Hauna Ruhusa</h1>
                <p>Huwezi kufikia ukurasa huu</p>
            </div>

            <div class="unauthorized-body">
                <div class="error-info">
                    <h3><i class="fas fa-exclamation-triangle"></i> Kosa la Ruhusa</h3>
                    <p>Hauna ruhusa ya kufikia ukurasa huu. Hii inaweza kutokea kwa sababu:</p>
                </div>

                <div class="suggestions">
                    <div class="suggestion-item">
                        <div class="suggestion-icon">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <div class="suggestion-content">
                            <h5>Huna Akaunti</h5>
                            <p>Unaweza kuwa unahitaji kujisajili au kuingia kwanza</p>
                        </div>
                    </div>

                    <div class="suggestion-item">
                        <div class="suggestion-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="suggestion-content">
                            <h5>Huna Ruhusa ya Kutosha</h5>
                            <p>Akaunti yako inaweza kuwa na kiwango cha ruhusa cha chini</p>
                        </div>
                    </div>

                    <div class="suggestion-item">
                        <div class="suggestion-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="suggestion-content">
                            <h5>Ombi Bado Halijaudhinishwa</h5>
                            <p>Ikiwa una ombi la kuwa mtaalam, bado linachunguzwa</p>
                        </div>
                    </div>
                </div>

                <div class="contact-info">
                    <h5><i class="fas fa-question-circle"></i> Uhitaji Msaada?</h5>
                    <p>Ikiwa unaamini hii ni makosa au unahitaji msaada:</p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:support@pandadigital.co.tz">support@pandadigital.co.tz</a></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:+255123456789">+255 123 456 789</a></p>
                </div>

                <div class="mt-4">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?php echo $authService->getRoleBasedRedirect($currentUser['role']); ?>" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i> Rudi kwenye Dashboard
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Ingia
                        </a>
                        <a href="/register.php" class="btn btn-outline-secondary">
                            <i class="fas fa-user-plus me-2"></i> Jisajili
                        </a>
                    <?php endif; ?>

                    <a href="/" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i> Rudi Nyumbani
                    </a>
                </div>

                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Ikiwa unaamini hii ni makosa, tafadhali wasiliana na wasimamizi
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
