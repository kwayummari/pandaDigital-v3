<?php
require_once 'config/init.php';
require_once 'models/OngeaHub.php';

// Initialize the OngeaHub model
$ongeaHubModel = new OngeaHub();

// Handle form submission (same logic as old code)
$message = '';
if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $phone = $_POST['phone'];
    $region = $_POST['region'];
    $msaada = $_POST['msaada'];
    $tarehe_ya_tukio = $_POST['tarehe_ya_tukio'];
    $report = $_POST['report'];

    // Remove first character from phone (same as old code)
    $phone = substr($phone, 1);

    // Set timezone and get current timestamp (same as old code)
    date_default_timezone_set("Africa/Nairobi");
    $current_time_stamp = date("Y-m-d H:m:d");
    $report_date = $current_time_stamp;

    // Capitalize words (same as old code)
    $fname = ucwords($fname);
    $sname = ucwords($sname);
    $region = ucwords($region);
    $report = ucwords($report);
    $msaada = ucwords($msaada);

    // Check word count (same as old code)
    if (str_word_count($report) > 150) {
        $message = "Maelezo Ni Mengi. Tafadhali Punguza";
    } else {
        // Insert into database using the model
        $result = $ongeaHubModel->addReport($fname, $sname, $phone, $region, $tarehe_ya_tukio, $msaada, $report, $current_time_stamp);

        if ($result) {
            $message = "Taarifa Za Tukio Zimetumwa Kikamilifu";
        } else {
            $message = "Imefeli";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongea Hub - Panda Digital</title>
    <meta name="description" content="Ongea Hub - Jukwaa la kuripoti tukio la rushwa ya ngono na kuunganishwa na msaada. Panda Digital.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
        <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-12" data-aos="fade-right">
                    <h1 class="page-title" style="color: #fff;">Ongea Hub</h1>
                    <p class="page-subtitle">Karibu kwenye jukwaa la Ongea Hub, sehemu ambapo msichana unapata nafasi ya kuripoti tukio la rushwa ya ngono na kuunganishwa na msaada</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                            <li class="breadcrumb-item active">Ongea Hub</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="main-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mb-5">
                    <div class="text-center" data-aos="fade-up">
                        <h2 class="section-title">Ongea Hub</h2>
                        <?php if ($message): ?>
                            <div class="alert <?= strpos($message, 'Zimetumwa') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                <h6 style="color: <?= strpos($message, 'Zimetumwa') !== false ? 'green' : 'red'; ?>;"><?= htmlspecialchars($message) ?></h6>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>

                            <?php if (strpos($message, 'Zimetumwa') !== false): ?>
                                <!-- Redirection Timer (same as old code) -->
                                <p id="time" class="text-success"></p>
                                <script>
                                    function startTimer(duration, display) {
                                        var timer = duration,
                                            seconds;
                                        setInterval(function() {
                                            seconds = parseInt(timer % 60, 10);
                                            seconds = seconds < 10 ? "0" + seconds : seconds;
                                            display.textContent = "Inaelekeza ndani ya sekunde ... " + seconds + "Secs";
                                            if (--timer < 0) {
                                                timer = duration;
                                                document.location.href = './';
                                            }
                                        }, 1000);
                                    }
                                    window.onload = function() {
                                        var fiveMinutes = 1,
                                            display = document.querySelector('#time');
                                        startTimer(fiveMinutes, display);
                                    };
                                </script>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Form Section -->
                <div class="col-lg-7 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="form-card">
                        <h3 class="form-title mb-4">Ripoti Tukio Lako</h3>
                        <form method="post" class="report-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="fname" placeholder="Jina La Kwanza" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="sname" placeholder="Jina La Pili" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="phone" maxlength="10" minlength="10" placeholder="Namba Ya Simu" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" name="region" placeholder="Mkoa" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tarehe_ya_tukio" class="form-label">Tarehe Ya Tukio</label>
                                <input type="date" class="form-control" name="tarehe_ya_tukio" required>
                            </div>

                            <div class="mb-3">
                                <textarea name="report" class="form-control" rows="4" placeholder="Taarifa Za Tukio" required></textarea>
                                <small class="text-muted">Maelezo yasiyozidi maneno 150</small>
                            </div>

                            <div class="mb-4">
                                <select name="msaada" required class="form-control">
                                    <option value="">Unahitaji Msaada Gani ?</option>
                                    <option value="kisheria">Kisheria</option>
                                    <option value="kijamii">Kijamii</option>
                                    <option value="kisaikolojia">Kisaikolojia</option>
                                </select>
                            </div>

                            <button name="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>TUMA
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Information Section -->
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="info-card">
                        <h4 class="info-title mb-4">Muhimu Kujua</h4>
                        <p class="info-text mb-4">
                            Taarifa yako ni muhimu sana. Hili ni jukwaa mahsusi kwaajili ya kuripoti kesi za udhalilishaji na kuunganishwa na msaada wa haraka.
                        </p>

                        <div class="contact-info">
                            <div class="contact-item mb-3">
                                <i class="fas fa-phone me-3 text-primary"></i>
                                <a href="tel:+25573428334" class="text-decoration-none h5">+255 734 283 34</a>
                            </div>

                            <div class="contact-item mb-3">
                                <i class="fas fa-envelope me-3 text-primary"></i>
                                <a href="mailto:info@pandadigital.co.tz" class="text-decoration-none h5">info@pandadigital.co.tz</a>
                            </div>

                            <div class="contact-item mb-4">
                                <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                                <span class="h5">Dar Es Salaam, Tanzania</span>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Usiri Kamili:</strong> Taarifa zako zinatunzwa kwa usiri kamili na hazitolewa kwa mtu yeyote bila idhini yako.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            }
        });
    </script>

    <style>
        /* Page header styles */
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

        /* Breadcrumb styles */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Section styles */
        .section-title {
            color: #333;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Form card styles */
        .form-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #333;
            font-weight: 600;
        }

        .report-form .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease;
        }

        .report-form .form-control:focus {
            border-color: #ffbc3b;
            box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
        }

        .report-form .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .report-form .btn {
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            background: linear-gradient(135deg, #ffbc3b 0%, #e6a800 100%);
            border: none;
            transition: transform 0.3s ease;
        }

        .report-form .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 188, 59, 0.3);
        }

        /* Info card styles */
        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .info-title {
            color: #333;
            font-weight: 600;
        }

        .info-text {
            color: #6c757d;
            line-height: 1.8;
        }

        .contact-item {
            display: flex;
            align-items: center;
        }

        .contact-item a {
            color: #333;
            text-decoration: none;
        }

        .contact-item a:hover {
            color: #ffbc3b;
        }

        /* Alert styles */
        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .form-card,
            .info-card {
                padding: 1.5rem;
            }
        }
    </style>
</body>

</html>