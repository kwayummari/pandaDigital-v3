<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../models/Business.php";

$auth = new AuthMiddleware();
$auth->requireRole('user');

$currentUser = $auth->getCurrentUser();
$businessModel = new Business();

$message = '';
$messageType = '';
$showSuccessActions = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = trim($_POST['business_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $businessType = trim($_POST['business_type'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $website = trim($_POST['website'] ?? '');

    // Validation
    if (empty($businessName)) {
        $message = 'Jina la biashara ni lazima';
        $messageType = 'danger';
    } elseif (empty($description)) {
        $message = 'Maelezo ya biashara ni lazima';
        $messageType = 'danger';
    } elseif (empty($location)) {
        $message = 'Mahali pa biashara ni lazima';
        $messageType = 'danger';
    } elseif (empty($phone)) {
        $message = 'Namba ya simu ni lazima';
        $messageType = 'danger';
    } else {
        try {
            // Check if user already has a business with this name
            $existingBusinesses = $businessModel->getBusinessesByUserId($currentUser['id']);
            $businessExists = false;

            foreach ($existingBusinesses as $business) {
                if (strtolower($business['name']) === strtolower($businessName)) {
                    $businessExists = true;
                    break;
                }
            }

            if ($businessExists) {
                $message = 'Biashara na jina hili tayari ipo';
                $messageType = 'warning';
            } else {
                // Add business using the old system table structure
                if ($businessModel->registerBusinessOldSystem($currentUser['id'], $businessName, $description, $location)) {
                    $message = 'Biashara yako imesajiliwa kwa mafanikio! Inasubiri idhinisho.';
                    $messageType = 'success';
                    $showSuccessActions = true;

                    // Clear form data
                    $businessName = $description = $location = $businessType = $phone = $email = $website = '';
                } else {
                    $message = 'Kuna tatizo la kiufundi. Jaribu tena.';
                    $messageType = 'danger';
                }
            }
        } catch (Exception $e) {
            error_log("Error registering business: " . $e->getMessage());
            $message = 'Kuna tatizo la kiufundi. Jaribu tena.';
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sajili Biashara - Panda Digital</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css') ?>?v=7">
    <style>
        /* Additional styles for business registration page */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: var(--gray-color);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--gray-dark);
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .business-type-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .business-type-option {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .business-type-option:hover {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.05);
        }

        .business-type-option.selected {
            border-color: var(--primary-color);
            background: rgba(255, 188, 59, 0.1);
        }

        .business-type-option input[type="radio"] {
            display: none;
        }

        .business-type-option label {
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Sidebar and Main Content Layout -->
    <div class="dashboard-container">
        <?php include __DIR__ . '/../includes/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            $page_title = 'Sajili Biashara';
            include __DIR__ . '/../includes/user_top_nav.php';
            ?>

            <div class="content-wrapper">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">
                            Sajili Biashara
                        </h1>
                        <p class="text-muted">Jaza taarifa za biashara yako ili uanze kufanya biashara</p>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Success Actions -->
                    <?php if ($showSuccessActions): ?>
                        <div class="alert alert-success border-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1">Biashara Imesajiliwa!</h6>
                                    <p class="mb-0">Biashara yako imesajiliwa kwa mafanikio na inasubiri idhinisho.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= app_url('user/business.php') ?>" class="btn btn-primary">
                                        Tazama Biashara Zangu
                                    </a>
                                    <a href="<?= app_url('user/register-business.php') ?>" class="btn btn-outline-primary">
                                        Sajili Biashara Nyingine
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Registration Form -->
                <?php if (!$showSuccessActions): ?>
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body p-4">
                                    <form method="POST" action="">
                                        <div class="row">
                                            <!-- Business Name -->
                                            <div class="col-md-12 mb-3">
                                                <label for="business_name" class="form-label">Jina la Biashara *</label>
                                                <input type="text" class="form-control" id="business_name" name="business_name"
                                                    value="<?php echo htmlspecialchars($businessName ?? ''); ?>"
                                                    placeholder="Jina la biashara yako" required>
                                            </div>

                                            <!-- Business Type -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Aina ya Biashara</label>
                                                <div class="business-type-options">
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'retail')">
                                                        <input type="radio" name="business_type" value="retail" id="retail">
                                                        <label for="retail">Uuzaji wa Bidhaa</label>
                                                    </div>
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'service')">
                                                        <input type="radio" name="business_type" value="service" id="service">
                                                        <label for="service">Utumishi wa Huduma</label>
                                                    </div>
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'manufacturing')">
                                                        <input type="radio" name="business_type" value="manufacturing" id="manufacturing">
                                                        <label for="manufacturing">Uzalishaji</label>
                                                    </div>
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'consulting')">
                                                        <input type="radio" name="business_type" value="consulting" id="consulting">
                                                        <label for="consulting">Mashauriano</label>
                                                    </div>
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'food')">
                                                        <input type="radio" name="business_type" value="food" id="food">
                                                        <label for="food">Chakula na Vinywaji</label>
                                                    </div>
                                                    <div class="business-type-option" onclick="selectBusinessType(this, 'other')">
                                                        <input type="radio" name="business_type" value="other" id="other">
                                                        <label for="other">Nyingine</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="col-md-12 mb-3">
                                                <label for="description" class="form-label">Maelezo ya Biashara *</label>
                                                <textarea class="form-control" id="description" name="description" rows="4"
                                                    placeholder="Eleza biashara yako, bidhaa au huduma unazotoa..." required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                            </div>

                                            <!-- Location -->
                                            <div class="col-md-12 mb-3">
                                                <label for="location" class="form-label">Mahali pa Biashara *</label>
                                                <input type="text" class="form-control" id="location" name="location"
                                                    value="<?php echo htmlspecialchars($location ?? ''); ?>"
                                                    placeholder="Mtaa, jiji au mkoa" required>
                                            </div>

                                            <!-- Contact Information -->
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Namba ya Simu *</label>
                                                <input type="tel" class="form-control" id="phone" name="phone"
                                                    value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                                                    placeholder="+255 7XX XXX XXX" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Barua Pepe</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                                    placeholder="email@example.com">
                                            </div>

                                            <!-- Website -->
                                            <div class="col-md-12 mb-4">
                                                <label for="website" class="form-label">Tovuti (Ikiwepo)</label>
                                                <input type="url" class="form-control" id="website" name="website"
                                                    value="<?php echo htmlspecialchars($website ?? ''); ?>"
                                                    placeholder="https://www.example.com">
                                            </div>

                                            <!-- Submit Buttons -->
                                            <div class="col-12">
                                                <div class="d-flex gap-3 justify-content-end">
                                                    <a href="<?= app_url('user/business.php') ?>" class="btn btn-secondary">
                                                        Ghairi
                                                    </a>
                                                    <button type="submit" class="btn btn-primary">
                                                        Sajili Biashara
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Information Card -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <h6 class="card-title">Maelezo Muhimu</h6>
                                    <ul class="mb-0">
                                        <li>Biashara yako itakaguliwa na timu yetu kwa ajili ya uhakiki</li>
                                        <li>Utapokea ujumbe wa SMS au barua pepe wakati biashara yako itakapoidhinishwa</li>
                                        <li>Unaweza kuongeza bidhaa na kufanya biashara baada ya kuidhinishwa</li>
                                        <li>Taarifa zako zitalindwa na hazitashirikiwa na mtu mwingine</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        function selectBusinessType(element, value) {
            // Remove selected class from all options
            document.querySelectorAll('.business-type-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selected class to clicked option
            element.classList.add('selected');

            // Check the radio button
            document.getElementById(value).checked = true;
        }

        // Auto-select business type if form was submitted with errors
        document.addEventListener('DOMContentLoaded', function() {
            const businessType = '<?php echo $_POST['business_type'] ?? ''; ?>';
            if (businessType) {
                const element = document.querySelector(`[onclick*="${businessType}"]`);
                if (element) {
                    selectBusinessType(element, businessType);
                }
            }
        });
    </script>
</body>

</html>