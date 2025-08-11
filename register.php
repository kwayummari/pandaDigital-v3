<?php
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
$formData = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'password' => trim($_POST['password'] ?? ''),
        'confirm_password' => trim($_POST['confirm_password'] ?? ''),
        'role' => $_POST['role'] ?? 'user',
        'region' => trim($_POST['region'] ?? ''),
        'business' => trim($_POST['business'] ?? ''),
        'gender' => $_POST['gender'] ?? '',
        'date_of_birth' => $_POST['date_of_birth'] ?? ''
    ];

    $result = $authService->registerUser($formData);

    if ($result['valid']) {
        if (isset($result['requires_authorization']) && $result['requires_authorization']) {
            // Expert registration - show pending message
            $success = $result['message'];
        } else {
            // Regular registration - redirect to dashboard
            header('Location: ' . $result['redirect_url']);
            exit();
        }
    } else {
        $error = $result['message'];
    }
}

// Regions for Tanzania
$regions = [
    'Arusha',
    'Dar es Salaam',
    'Dodoma',
    'Geita',
    'Iringa',
    'Kagera',
    'Katavi',
    'Kigoma',
    'Kilimanjaro',
    'Lindi',
    'Manyara',
    'Mara',
    'Mbeya',
    'Mjini Magharibi',
    'Morogoro',
    'Mtwara',
    'Mwanza',
    'Njombe',
    'Pemba North',
    'Pemba South',
    'Pwani',
    'Rukwa',
    'Ruvuma',
    'Shinyanga',
    'Simiyu',
    'Singida',
    'Songwe',
    'Tabora',
    'Tanga',
    'Unguja North',
    'Unguja South'
];

// Business categories
$businessCategories = [
    'Agriculture',
    'Technology',
    'Healthcare',
    'Education',
    'Finance',
    'Retail',
    'Manufacturing',
    'Tourism',
    'Transportation',
    'Construction',
    'Food & Beverage',
    'Fashion',
    'Beauty',
    'Entertainment',
    'Real Estate',
    'Consulting',
    'Other'
];

// Gender options
$genders = ['Male', 'Female', 'Other'];
?>
<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jisajili - Panda Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 700px;
            width: 100%;
        }

        .register-header {
            background: #667eea;
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .register-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .register-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .register-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-register {
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

        .btn-register:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .register-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
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

        .row {
            margin-left: -10px;
            margin-right: -10px;
        }

        .col-md-6 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.875rem;
        }

        .strength-weak {
            color: #dc3545;
        }

        .strength-medium {
            color: #ffc107;
        }

        .strength-strong {
            color: #28a745;
        }

        .role-selector {
            margin-bottom: 20px;
            text-align: center;
        }

        .role-option {
            display: inline-block;
            margin: 0 10px;
            padding: 15px 25px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
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

        .role-option i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .expert-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1><i class="fas fa-paw"></i></h1>
                <h2>Jiunge na Panda Digital</h2>
                <p>Unda akaunti yako na uanze safari yako</p>
            </div>

            <div class="register-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <div class="mt-3">
                            <a href="/login.php" class="btn btn-primary">Ingia Sasa</a>
                            <a href="/" class="btn btn-outline-secondary ms-2">Rudi Nyumbani</a>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="registerForm">
                    <!-- Role Selection -->
                    <div class="role-selector">
                        <h5 class="mb-3">Chagua Aina ya Akaunti</h5>
                        <div class="role-option <?php echo ($formData['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>" onclick="selectRole('user')">
                            <input type="radio" name="role" value="user" <?php echo ($formData['role'] ?? 'user') === 'user' ? 'checked' : ''; ?>>
                            <i class="fas fa-user"></i>
                            <strong>Mtumiaji</strong>
                            <small>Kwa watumiaji wa kawaida</small>
                        </div>
                        <div class="role-option <?php echo ($formData['role'] ?? 'user') === 'expert' ? 'selected' : ''; ?>" onclick="selectRole('expert')">
                            <input type="radio" name="role" value="expert" <?php echo ($formData['role'] ?? 'user') === 'expert' ? 'checked' : ''; ?>>
                            <i class="fas fa-graduation-cap"></i>
                            <strong>Mtaalam</strong>
                            <small>Kwa wataalam na washauri</small>
                        </div>
                    </div>

                    <!-- Expert Information -->
                    <div id="expertInfo" class="expert-info" style="display: <?php echo ($formData['role'] ?? 'user') === 'expert' ? 'block' : 'none'; ?>;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Maelezo:</strong> Ukiwa mtaalam, ombi lako litatumwa kwa wasimamizi kwa ajili ya kuidhinishwa.
                        Utapata taarifa kupitia barua pepe yako.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    placeholder="Ingiza jina la kwanza" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" required>
                                <label for="first_name">Jina la Kwanza</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    placeholder="Ingiza jina la mwisho" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" required>
                                <label for="last_name">Jina la Mwisho</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Ingiza barua pepe yako" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                        <label for="email">Barua Pepe</label>
                    </div>

                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phone" name="phone"
                            placeholder="Ingiza nambari ya simu" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" required>
                        <label for="phone">Nambari ya Simu</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Chagua Jinsia</option>
                                    <?php foreach ($genders as $gender): ?>
                                        <option value="<?php echo htmlspecialchars($gender); ?>"
                                            <?php echo ($formData['gender'] ?? '') === $gender ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($gender); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="gender">Jinsia</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                    value="<?php echo htmlspecialchars($formData['date_of_birth'] ?? ''); ?>" required>
                                <label for="date_of_birth">Tarehe ya Kuzaliwa</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="region" name="region" required>
                                    <option value="">Chagua Mkoa</option>
                                    <?php foreach ($regions as $region): ?>
                                        <option value="<?php echo htmlspecialchars($region); ?>"
                                            <?php echo ($formData['region'] ?? '') === $region ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($region); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="region">Mkoa</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="business" name="business" required>
                                    <option value="">Chagua Kategoria ya Biashara</option>
                                    <?php foreach ($businessCategories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>"
                                            <?php echo ($formData['business'] ?? '') === $category ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="business">Kategoria ya Biashara</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Ingiza nenosiri lako" required>
                        <label for="password">Nenosiri</label>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Thibitisha nenosiri lako" required>
                        <label for="confirm_password">Thibitisha Nenosiri</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Nakubaliana na <a href="/terms.php" target="_blank">Sheria za Huduma</a> na
                            <a href="/privacy.php" target="_blank">Sera ya Faragha</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i> Unda Akaunti
                    </button>
                </form>

                <div class="register-footer">
                    <p>Una akaunti tayari? <a href="/login.php">Ingia hapa</a></p>
                    <p><a href="/">Rudi Nyumbani</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Role selection
        function selectRole(role) {
            // Update radio button
            document.querySelector(`input[value="${role}"]`).checked = true;

            // Update visual selection
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            // Show/hide expert info
            const expertInfo = document.getElementById('expertInfo');
            if (role === 'expert') {
                expertInfo.style.display = 'block';
            } else {
                expertInfo.style.display = 'none';
            }
        }

        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');

            // Password strength checker
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);

                passwordStrength.textContent = strength.message;
                passwordStrength.className = 'password-strength ' + strength.class;
            });

            // Confirm password validation
            confirmPasswordInput.addEventListener('blur', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;

                if (password !== confirmPassword) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const terms = document.getElementById('terms').checked;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    confirmPasswordInput.classList.add('is-invalid');
                    return false;
                }

                if (!terms) {
                    e.preventDefault();
                    alert('Tafadhali kubaliana na Sheria za Huduma na Sera ya Faragha.');
                    return false;
                }
            });

            function checkPasswordStrength(password) {
                let score = 0;
                let feedback = [];

                if (password.length >= 8) score++;
                if (password.match(/[a-z]/)) score++;
                if (password.match(/[A-Z]/)) score++;
                if (password.match(/[0-9]/)) score++;
                if (password.match(/[^a-zA-Z0-9]/)) score++;

                if (score < 2) {
                    return {
                        message: 'Nenosiri dhaifu',
                        class: 'strength-weak'
                    };
                } else if (score < 4) {
                    return {
                        message: 'Nenosiri la kati',
                        class: 'strength-medium'
                    };
                } else {
                    return {
                        message: 'Nenosiri kali',
                        class: 'strength-strong'
                    };
                }
            }
        });
    </script>
</body>

</html>