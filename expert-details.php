<?php
require_once 'config/init.php';
require_once 'models/Expert.php';
require_once 'services/AuthService.php';

// Check if user is logged in
$authService = new AuthService();
$isLoggedIn = $authService->isLoggedIn();
$currentUser = $isLoggedIn ? $authService->getCurrentUser() : null;

// Get expert ID from URL parameter
$expertId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$expertId) {
    header('Location: ' . app_url('uliza-swali.php'));
    exit;
}

// Initialize the Expert model
$expertModel = new Expert();

// Get expert details
$expert = $expertModel->getExpertById($expertId);

if (!$expert) {
    header('Location: ' . app_url('uliza-swali.php'));
    exit;
}

// Get other experts for the sidebar
$otherExperts = $expertModel->getOtherExperts($expertId, 8);

$pageTitle = 'Maelezo ya Mtaalamu - ' . $appConfig['name'];
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Page header styles - same as fursa.php */
        .page-header {
            padding: 120px 0 80px;
            color: white;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .expert-profile {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        .expert-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center top;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .expert-status {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .expert-status.free {
            background: #28a745;
            color: white;
        }

        .expert-status.premium {
            background: #6c757d;
            color: white;
        }

        .expert-bio {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
        }

        .other-experts {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-top: 30px;
        }

        .expert-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .expert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .expert-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center top;
        }

        .expert-card-info {
            padding: 20px;
        }

        .expert-card-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .expert-card-status.free {
            background: #28a745;
            color: white;
        }

        .expert-card-status.premium {
            background: #6c757d;
            color: white;
        }

        .contact-section {
            background: #f8f9fa;
            color: #333;
            padding: 60px 0;
            margin-top: 50px;
        }



        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= app_url() ?>" class="text-decoration-none text-black">Nyumbani</a></li>
                <li class="breadcrumb-item"><a href="<?= app_url('uliza-swali.php') ?>" class="text-decoration-none text-black">Uliza Swali</a></li>
                <li class="breadcrumb-item active text-black">Maelezo ya Mtaalamu</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="page-title" style="color: #ffffff;"><?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?></h1>
                    <p class="page-subtitle"><?= htmlspecialchars($expert['business']) ?> - <?= htmlspecialchars($expert['region']) ?></p>
                    <a href="#" class="btn btn-primary btn-lg">Ongea na <?= htmlspecialchars($expert['first_name']) ?></a>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-left">
                    <img src="<?= $expertModel->getExpertImageUrl($expert['profile_photo']) ?>"
                        alt="<?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?>"
                        class="expert-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Expert Profile Section -->
    <section class="container">
        <div class="expert-profile" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-8">
                    <div class="position-relative">
                        <h2 class="mb-4">Kuhusu <?= htmlspecialchars($expert['first_name']) ?></h2>
                        <div class="expert-status <?= $expert['status'] ?>">
                            <?= ucfirst($expert['status']) ?>
                        </div>

                        <div class="expert-bio">
                            <h5 class="mb-3">Maelezo ya Mtaalamu</h5>
                            <p class="mb-0">
                                <?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?> ni mtaalamu wa <?= htmlspecialchars($expert['business']) ?>
                                kutoka <?= htmlspecialchars($expert['region']) ?>. Ana uzoefu wa kutosha katika nyanja hii na anaweza kukusaidia
                                kutatua changamoto zako za biashara.
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Jina:</strong> <?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Biashara:</strong> <?= htmlspecialchars($expert['business']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Mkoa:</strong> <?= htmlspecialchars($expert['region']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Simu:</strong> <?= htmlspecialchars($expert['phone']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Barua pepe:</strong> <?= htmlspecialchars($expert['email']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Hali:</strong> <span class="badge bg-<?= $expert['status'] === 'premium' ? 'warning' : 'success' ?>"><?= ucfirst($expert['status']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="text-center">
                        <a href="#" class="btn btn-primary btn-lg w-100 mb-3">Ongea na <?= htmlspecialchars($expert['first_name']) ?></a>
                        <p class="text-muted small">Ingia au jisajili ili uweze kuuliza swali</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Other Experts Section -->
    <?php if (!empty($otherExperts)): ?>
        <section class="container">
            <div class="other-experts" data-aos="fade-up">
                <h3 class="mb-4">Wataalamu Wengine</h3>
                <div class="row">
                    <?php foreach ($otherExperts as $otherExpert): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up">
                            <div class="expert-card position-relative">
                                <div class="position-relative">
                                    <img src="<?= $expertModel->getExpertImageUrl($otherExpert['profile_photo']) ?>"
                                        alt="<?= htmlspecialchars($otherExpert['first_name'] . ' ' . $otherExpert['last_name']) ?>"
                                        class="expert-card-image">
                                    <div class="expert-card-status <?= $otherExpert['status'] ?>">
                                        <?= ucfirst($otherExpert['status']) ?>
                                    </div>
                                </div>
                                <div class="expert-card-info">
                                    <h6 class="mb-2"><?= htmlspecialchars($otherExpert['first_name'] . ' ' . $otherExpert['last_name']) ?></h6>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($otherExpert['business']) ?></p>
                                    <p class="text-muted small mb-3"><?= htmlspecialchars($otherExpert['region']) ?></p>
                                    <a href="<?= app_url('expert-details.php?id=' . $otherExpert['id']) ?>" class="btn btn-outline-primary btn-sm">Soma Zaidi</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center mb-4">Tunawezaje Kukusaidia?</h2>
                    <p class="lead text-center mb-5">Jiunge na Panda Chat na upate msaada wa wataalamu wenye uzoefu</p>
                    
                    <?php if ($isLoggedIn): ?>
                        <!-- Contact Form for Logged In Users -->
                        <div class="card">
                            <div class="card-body p-4">
                                <h5 class="card-title text-center mb-4">Wasiliana na <?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?></h5>
                                <form id="contactExpertForm">
                                    <input type="hidden" id="expertId" value="<?= $expertId ?>">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Mada</label>
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Ujumbe</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">Tuma Ujumbe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Register Prompt for Non-Logged In Users -->
                        <div class="card">
                            <div class="card-body p-4 text-center">
                                <h5 class="card-title mb-4">Ingia au Jisajili Ili Kuwasiliana</h5>
                                <p class="mb-4">Unahitaji kuingia au kujisajili ili kuweza kuwasiliana na mtaalamu huyu.</p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <a href="#" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fas fa-sign-in-alt me-2"></i>Ingia
                                    </a>
                                    <a href="#" class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#signupModal">
                                        <i class="fas fa-user-plus me-2"></i>Jisajili
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="<?= app_url('uliza-swali.php') ?>" class="btn btn-outline-secondary">Tazama Wataalamu Wote</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>