<?php
require_once __DIR__ . "/config/init.php";
require_once __DIR__ . "/services/AuthService.php";

// Check if user is already logged in
$authService = new AuthService();
if ($authService->isLoggedIn()) {
    $currentUser = $authService->getCurrentUser();
    header('Location: ' . $authService->getRoleBasedRedirect($currentUser['role']));
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Tafadhali toa barua pepe na nenosiri.';
    } else {
        $result = $authService->loginUser($email, $password);

        if ($result['valid']) {
            // Redirect based on role
            header('Location: ' . $result['redirect_url']);
            exit();
        } else {
            $error = $result['message'];

            // Handle expert authorization pending
            if (isset($result['requires_authorization']) && $result['requires_authorization']) {
                $error .= ' <a href="' . app_url('expert/pending-authorization.php') . '" class="alert-link">Tazama hali ya ombi</a>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingia - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: #667eea;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: #667eea;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        .input-group:focus-within .form-control {
            border-color: #667eea;
        }

        .role-selector {
            margin-bottom: 20px;
            text-align: center;
        }

        .role-option {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-option:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }

        .role-option.selected {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .role-option input[type="radio"] {
            display: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-paw"></i></h1>
                <h2>Karibu Tena</h2>
                <p>Ingia kwenye akaunti yako ya Panda Digital</p>
            </div>

            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Ingiza barua pepe yako" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <label for="email">Barua Pepe</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Ingiza nenosiri lako" required>
                        <label for="password">Nenosiri</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Nikumbuke
                            </label>
                        </div>
                        <a href="/forgot-password.php" class="text-decoration-none">Umesahau nenosiri?</a>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Ingia
                    </button>
                </form>

                <div class="login-footer">
                    <p>Huna akaunti? <a href="<?= app_url('register.php') ?>">Jisajili hapa</a></p>
                    <p><a href="<?= app_url('') ?>">Rudi Nyumbani</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Real-time validation
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            passwordInput.addEventListener('blur', function() {
                if (this.value.trim().length < 1) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                const email = emailInput.value.trim();
                const password = passwordInput.value.trim();

                if (!email || !password) {
                    e.preventDefault();
                    if (!email) emailInput.classList.add('is-invalid');
                    if (!password) passwordInput.classList.add('is-invalid');
                    return false;
                }

                if (!isValidEmail(email)) {
                    e.preventDefault();
                    emailInput.classList.add('is-invalid');
                    return false;
                }
            });

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        });
    </script>
</body>

</html>