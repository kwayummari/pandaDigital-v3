<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();

// Set page title
$pageTitle = 'Badilisha Wasifu - ' . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=5">

    <style>
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
        }

        .profile-avatar i {
            font-size: 3rem;
        }

        .form-control:focus {
            border-color: var(--primary-color, #ffbc3b);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .btn-primary {
            background: var(--primary-color, #ffbc3b);
            border-color: var(--primary-color, #ffbc3b);
        }

        .btn-primary:hover {
            background: #e6a800;
            border-color: #e6a800;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .card-body {
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/../includes/user_top_nav.php'; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="page-title">Badilisha Wasifu</h1>
                            <p class="page-subtitle">Hifadhi na ubadilishe maelezo yako ya msingi</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="<?= app_url('user/dashboard.php') ?>" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-2"></i>Rudi Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container-fluid py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="profile-card">
                            <!-- Profile Header -->
                            <div class="profile-header">
                                <div class="profile-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h3 class="mb-1"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></h3>
                                <p class="mb-0 opacity-75"><?= htmlspecialchars($currentUser['email']) ?></p>
                            </div>

                            <!-- Profile Form -->
                            <div class="card-body">
                                <h4 class="mb-4 text-center">Maelezo ya Wasifu</h4>
                                <p class="text-muted text-center mb-4">Badilisha maelezo yoyote unayotaka. Sehemu zisizojazwa hazitabadilishwa.</p>

                                <form id="profileUpdateForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="firstName" class="form-label">Jina la Kwanza</label>
                                            <input type="text" class="form-control" id="firstName" name="first_name"
                                                value="<?= htmlspecialchars($currentUser['first_name'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="lastName" class="form-label">Jina la Mwisho</label>
                                            <input type="text" class="form-control" id="lastName" name="last_name"
                                                value="<?= htmlspecialchars($currentUser['last_name'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Barua Pepe</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" readonly>
                                            <div class="form-text">Hauwezi kubadilisha barua pepe yako.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Namba ya Simu</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>"
                                                placeholder="Mfano: 0712345678">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="region" class="form-label">Mkoa</label>
                                            <select class="form-select" id="region" name="region">
                                                <option value="">Chagua Mkoa</option>
                                                <option value="Arusha" <?= ($currentUser['region'] ?? '') === 'Arusha' ? 'selected' : '' ?>>Arusha</option>
                                                <option value="Dar es Salaam" <?= ($currentUser['region'] ?? '') === 'Dar es Salaam' ? 'selected' : '' ?>>Dar es Salaam</option>
                                                <option value="Dodoma" <?= ($currentUser['region'] ?? '') === 'Dodoma' ? 'selected' : '' ?>>Dodoma</option>
                                                <option value="Geita" <?= ($currentUser['region'] ?? '') === 'Geita' ? 'selected' : '' ?>>Geita</option>
                                                <option value="Iringa" <?= ($currentUser['region'] ?? '') === 'Iringa' ? 'selected' : '' ?>>Iringa</option>
                                                <option value="Kagera" <?= ($currentUser['region'] ?? '') === 'Kagera' ? 'selected' : '' ?>>Kagera</option>
                                                <option value="Katavi" <?= ($currentUser['region'] ?? '') === 'Katavi' ? 'selected' : '' ?>>Katavi</option>
                                                <option value="Kigoma" <?= ($currentUser['region'] ?? '') === 'Kigoma' ? 'selected' : '' ?>>Kigoma</option>
                                                <option value="Kilimanjaro" <?= ($currentUser['region'] ?? '') === 'Kilimanjaro' ? 'selected' : '' ?>>Kilimanjaro</option>
                                                <option value="Lindi" <?= ($currentUser['region'] ?? '') === 'Lindi' ? 'selected' : '' ?>>Lindi</option>
                                                <option value="Manyara" <?= ($currentUser['region'] ?? '') === 'Manyara' ? 'selected' : '' ?>>Manyara</option>
                                                <option value="Mara" <?= ($currentUser['region'] ?? '') === 'Mara' ? 'selected' : '' ?>>Mara</option>
                                                <option value="Mbeya" <?= ($currentUser['region'] ?? '') === 'Mbeya' ? 'selected' : '' ?>>Mbeya</option>
                                                <option value="Morogoro" <?= ($currentUser['region'] ?? '') === 'Morogoro' ? 'selected' : '' ?>>Morogoro</option>
                                                <option value="Mtwara" <?= ($currentUser['region'] ?? '') === 'Mtwara' ? 'selected' : '' ?>>Mtwara</option>
                                                <option value="Mwanza" <?= ($currentUser['region'] ?? '') === 'Mwanza' ? 'selected' : '' ?>>Mwanza</option>
                                                <option value="Njombe" <?= ($currentUser['region'] ?? '') === 'Njombe' ? 'selected' : '' ?>>Njombe</option>
                                                <option value="Pemba North" <?= ($currentUser['region'] ?? '') === 'Pemba North' ? 'selected' : '' ?>>Pemba North</option>
                                                <option value="Pemba South" <?= ($currentUser['region'] ?? '') === 'Pemba South' ? 'selected' : '' ?>>Pemba South</option>
                                                <option value="Pwani" <?= ($currentUser['region'] ?? '') === 'Pwani' ? 'selected' : '' ?>>Pwani</option>
                                                <option value="Rukwa" <?= ($currentUser['region'] ?? '') === 'Rukwa' ? 'selected' : '' ?>>Rukwa</option>
                                                <option value="Ruvuma" <?= ($currentUser['region'] ?? '') === 'Ruvuma' ? 'selected' : '' ?>>Ruvuma</option>
                                                <option value="Shinyanga" <?= ($currentUser['region'] ?? '') === 'Shinyanga' ? 'selected' : '' ?>>Shinyanga</option>
                                                <option value="Simiyu" <?= ($currentUser['region'] ?? '') === 'Simiyu' ? 'selected' : '' ?>>Simiyu</option>
                                                <option value="Singida" <?= ($currentUser['region'] ?? '') === 'Singida' ? 'selected' : '' ?>>Singida</option>
                                                <option value="Songwe" <?= ($currentUser['region'] ?? '') === 'Songwe' ? 'selected' : '' ?>>Songwe</option>
                                                <option value="Tabora" <?= ($currentUser['region'] ?? '') === 'Tabora' ? 'selected' : '' ?>>Tabora</option>
                                                <option value="Tanga" <?= ($currentUser['region'] ?? '') === 'Tanga' ? 'selected' : '' ?>>Tanga</option>
                                                <option value="Unguja North" <?= ($currentUser['region'] ?? '') === 'Unguja North' ? 'selected' : '' ?>>Unguja North</option>
                                                <option value="Unguja South" <?= ($currentUser['region'] ?? '') === 'Unguja South' ? 'selected' : '' ?>>Unguja South</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="gender" class="form-label">Jinsia</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Chagua Jinsia</option>
                                                <option value="Mwanamke" <?= ($currentUser['gender'] ?? '') === 'Mwanamke' ? 'selected' : '' ?>>Mwanamke</option>
                                                <option value="Mwanaume" <?= ($currentUser['gender'] ?? '') === 'Mwanaume' ? 'selected' : '' ?>>Mwanaume</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="dateOfBirth" class="form-label">Tarehe ya Kuzaliwa</label>
                                            <input type="date" class="form-control" id="dateOfBirth" name="date_of_birth"
                                                value="<?= htmlspecialchars($currentUser['date_of_birth'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="business" class="form-label">Biashara (Hiari)</label>
                                            <input type="text" class="form-control" id="business" name="business"
                                                value="<?= htmlspecialchars($currentUser['business'] ?? '') ?>"
                                                placeholder="Jina la biashara yako">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="bio" class="form-label">Maelezo Kuhusu Wewe (Hiari)</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"
                                            placeholder="Andika maelezo mafupi kuhusu wewe..."><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
                                    </div>

                                    <div class="d-flex gap-3 justify-content-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Hifadhi Maelezo
                                        </button>
                                        <a href="<?= app_url('user/dashboard.php') ?>" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-arrow-left me-2"></i>Rudi
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const dashboardContainer = document.querySelector('.dashboard-container');

            sidebarToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    dashboardContainer.classList.toggle('sidebar-collapsed');
                });
            });

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(e.target) && !e.target.closest('.sidebar-toggle')) {
                        sidebar.classList.remove('collapsed');
                        dashboardContainer.classList.remove('sidebar-collapsed');
                    }
                }
            });
        });
    </script>

    <script>
        document.getElementById('profileUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inahifadhi...';
            submitBtn.disabled = true;

            fetch('<?= app_url("api/update-profile.php") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Show success message
                        showAlert('Maelezo yamehifadhiwa kwa mafanikio!', 'success');

                        // Update the displayed name in header
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert(result.message || 'Kulikuwa na tatizo, jaribu tena', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Kulikuwa na tatizo, jaribu tena', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>

</html>