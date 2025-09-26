<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panda Digital - Kwenye Matengenezo</title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo/favicon.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #37ABA6 0%, #2c7a7a 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .maintenance-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            backdrop-filter: blur(10px);
        }

        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: #37ABA6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(55, 171, 166, 0.3);
        }

        .logo i {
            font-size: 50px;
            color: white;
        }

        .maintenance-title {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .maintenance-subtitle {
            color: #37ABA6;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .maintenance-message {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .progress-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #37ABA6, #2c7a7a);
            border-radius: 4px;
            transition: width 2s ease-in-out;
        }

        .progress-text {
            color: #37ABA6;
            font-weight: 600;
            margin-top: 10px;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
            color: #2c3e50;
        }

        .contact-item i {
            color: #37ABA6;
            margin-right: 10px;
            width: 20px;
        }

        .social-links {
            margin-top: 30px;
        }

        .social-links a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #37ABA6;
            color: white;
            border-radius: 50%;
            line-height: 50px;
            margin: 0 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #2c7a7a;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(55, 171, 166, 0.4);
        }

        .estimated-time {
            background: linear-gradient(135deg, #37ABA6, #2c7a7a);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            display: inline-block;
            font-weight: 600;
            margin: 20px 0;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        @media (max-width: 768px) {
            .maintenance-card {
                padding: 40px 20px;
                margin: 20px;
            }

            .maintenance-title {
                font-size: 2rem;
            }

            .maintenance-subtitle {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="maintenance-container">
        <div class="maintenance-card">
            <!-- Logo -->
            <div class="logo pulse">
                <i class="fas fa-tools"></i>
            </div>

            <!-- Title -->
            <h1 class="maintenance-title">Kwenye Matengenezo</h1>
            <h2 class="maintenance-subtitle">Tunafanya Uboreshaji</h2>

            <!-- Message -->
            <div class="maintenance-message">
                <p>Samahani kwa usumbufu! Tunafanya matengenezo muhimu kwenye tovuti yetu ili kukupa uzoefu bora zaidi.</p>
                <p>Tunafanya uboreshaji wa mfumo wetu na tutarudi hivi karibuni.</p>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" style="width: 75%"></div>
                </div>
                <div class="progress-text">75% Imekamilika</div>
            </div>

            <!-- Estimated Time -->
            <div class="estimated-time">
                <i class="fas fa-clock me-2"></i>
                Tunatarajia kurudi katika masaa 2-4
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h5 class="mb-3" style="color: #2c3e50;">Wasiliana Nasi</h5>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>+255 767 680 463</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>info@pandadigital.co.tz</span>
                </div>
                <div class="contact-item">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp: +255 767 680 463</span>
                </div>
            </div>

            <!-- Social Links -->
            <div class="social-links">
                <a href="https://facebook.com/pandadigital" target="_blank" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/pandadigital" target="_blank" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://instagram.com/pandadigital" target="_blank" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com/company/pandadigital" target="_blank" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>

            <!-- Refresh Button -->
            <div class="mt-4">
                <button class="btn btn-primary btn-lg" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Jaribu Tena
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Animate progress bar on load
        window.addEventListener('load', function() {
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = '75%';
        });
    </script>
</body>

</html>