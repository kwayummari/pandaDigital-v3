<?php

/**
 * Profile Completion Page
 * Dedicated page for users to complete their profile
 */

require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/models/User.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get action and redirect URL from query parameters
$action = $_GET['action'] ?? '';
$redirectUrl = $_GET['redirect'] ?? '/';

// Initialize User model
$userModel = new User($pdo);
$userId = $_SESSION['userId'];

// Get user data
$user = $userModel->getUserById($userId);
if (!$user) {
    header('Location: /login.php');
    exit;
}

// Get profile completion status
$profileStatus = $userModel->getProfileCompletionStatus($userId);
$fieldLabels = $userModel->getFieldLabels();
$regions = $userModel->getRegions();
$genderOptions = $userModel->getGenderOptions();

// Get action requirements
$actionRequirements = [
    'download_certificate' => 'Kupakua Cheti',
    'contact_expert' => 'Kuwasiliana na Mtaalamu',
    'sell_product' => 'Kuuza Bidhaa',
    'buy_product' => 'Kununua Bidhaa',
    'study_course' => 'Kusoma Kozi'
];

$actionName = $actionRequirements[$action] ?? 'Kutekeleza Kitendo';
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamilisha Wasifu - Panda Digital</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .profile-completion-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #5f4594 0%, #ffbc3b 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-body {
            padding: 2rem;
        }

        .progress-custom {
            height: 12px;
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar-custom {
            background: linear-gradient(90deg, #5f4594 0%, #ffbc3b 100%);
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #5f4594;
            box-shadow: 0 0 0 0.2rem rgba(95, 69, 148, 0.25);
        }

        .btn-primary {
            background: #5f4594;
            border-color: #5f4594;
        }

        .btn-primary:hover {
            background: #4a3a7a;
            border-color: #4a3a7a;
        }

        .action-requirement {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="profile-completion-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="profile-card">
                        <!-- Header -->
                        <div class="profile-header">
                            <h1 class="mb-3">
                                <i class="fas fa-user-edit me-3"></i>
                                Kamilisha Wasifu Wako
                            </h1>
                            <p class="mb-0">
                                Unahitaji kukamilisha wasifu wako ili <?php echo strtolower($actionName); ?>
                            </p>
                        </div>

                        <!-- Body -->
                        <div class="profile-body">
                            <!-- Progress Section -->
                            <div class="text-center mb-4">
                                <h5 class="mb-3">Ukomo wa Wasifu Wako</h5>
                                <div class="progress-custom mb-2">
                                    <div class="progress-bar-custom" role="progressbar"
                                        style="width: <?php echo $profileStatus['percentage']; ?>%"
                                        aria-valuenow="<?php echo $profileStatus['percentage']; ?>"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">0%</span>
                                    <span class="fw-bold text-primary"><?php echo $profileStatus['percentage']; ?>%</span>
                                    <span class="text-muted">100%</span>
                                </div>
                                <small class="text-muted">
                                    <?php echo $profileStatus['completed']; ?> kati ya <?php echo $profileStatus['total']; ?> sehemu zimekamilika
                                </small>
                            </div>

                            <!-- Action Requirement Info -->
                            <div class="action-requirement">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                    <strong>Kwa <?php echo $actionName; ?> unahitaji:</strong>
                                </div>
                                <ul class="mb-0">
                                    <?php
                                    $missingFields = $userModel->getMissingFieldsForAction($userId, $action);
                                    foreach ($missingFields as $field) {
                                        $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                                        echo '<li>' . htmlspecialchars($label) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!-- Profile Form -->
                            <form id="profileCompletionForm" method="POST" action="update-profile.php">
                                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirectUrl); ?>">

                                <div class="row">
                                    <!-- First Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">
                                            <?php echo $fieldLabels['first_name']; ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">
                                            <?php echo $fieldLabels['last_name']; ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">
                                            <?php echo $fieldLabels['phone']; ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                        <small class="text-muted">Mfano: 0712345678 au +255712345678</small>
                                    </div>

                                    <!-- Region -->
                                    <div class="col-md-6 mb-3">
                                        <label for="region" class="form-label">
                                            <?php echo $fieldLabels['region']; ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="region" name="region" required>
                                            <option value="">Chagua Mkoa</option>
                                            <?php foreach ($regions as $region): ?>
                                                <option value="<?php echo htmlspecialchars($region); ?>"
                                                    <?php echo (($user['region'] ?? '') === $region) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($region); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Business -->
                                    <div class="col-md-6 mb-3">
                                        <label for="business" class="form-label">
                                            <?php echo $fieldLabels['business']; ?>
                                        </label>
                                        <input type="text" class="form-control" id="business" name="business"
                                            value="<?php echo htmlspecialchars($user['business'] ?? ''); ?>"
                                            placeholder="Jina la biashara yako (si lazima)">
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">
                                            <?php echo $fieldLabels['gender']; ?>
                                        </label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Chagua Jinsia</option>
                                            <?php foreach ($genderOptions as $value => $label): ?>
                                                <option value="<?php echo htmlspecialchars($value); ?>"
                                                    <?php echo (($user['gender'] ?? '') === $value) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Date of Birth -->
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">
                                            <?php echo $fieldLabels['date_of_birth']; ?>
                                        </label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                            value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                                    </div>

                                    <!-- Bio -->
                                    <div class="col-md-6 mb-3">
                                        <label for="bio" class="form-label">
                                            <?php echo $fieldLabels['bio']; ?>
                                        </label>
                                        <textarea class="form-control" id="bio" name="bio" rows="3"
                                            placeholder="Maelezo mafupi kuhusu wewe..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save me-2"></i>
                                        Hifadhi Wasifu na Uendelee
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileCompletionForm = document.getElementById('profileCompletionForm');

            if (profileCompletionForm) {
                profileCompletionForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inahifadhi...';
                    submitBtn.disabled = true;

                    // Submit form via AJAX
                    fetch('update-profile.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                showAlert('Wasifu wako umekamilishwa kwa mafanikio!', 'success');

                                // Redirect after a short delay
                                setTimeout(() => {
                                    const redirectUrl = formData.get('redirect_url') || '/';
                                    window.location.href = redirectUrl;
                                }, 2000);
                            } else {
                                showAlert(data.message || 'Kuna tatizo. Tafadhali jaribu tena.', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('Kuna tatizo. Tafadhali jaribu tena.', 'danger');
                        })
                        .finally(() => {
                            // Restore button state
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }
        });

        // Function to show alerts
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer') || createAlertContainer();

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

            alertContainer.appendChild(alert);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        function createAlertContainer() {
            const container = document.createElement('div');
            container.id = 'alertContainer';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }
    </script>
</body>

</html>