<?php
require_once 'config/init.php';

$pageTitle = 'Tangaza Biashara - ' . env('APP_NAME');

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Tangaza Biashara Yako</h1>
                <p class="page-subtitle">Fikia wateja wengi zaidi na ukuze biashara yako kupitia jukwaa letu la kidijitali</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item active">Tangaza Biashara</li>
                    </ol>
                </nav>
            </div>
            <!-- <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <i class="fas fa-store fa-4x text-white"></i>
                </div>
            </div> -->
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12" data-aos="fade-up">
                <h2 class="section-title">Kwa nini Kutangaza na Panda Digital?</h2>
                <p class="section-subtitle">Tunakupa njia rahisi na ya ufanisi ya kufikia wateja wengi zaidi</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-visual mb-4">
                        <div class="feature-number">10K+</div>
                        <div class="feature-label">Wateja</div>
                    </div>
                    <h4 class="feature-title mb-3">Wateja Wengi</h4>
                    <p class="feature-text text-muted">
                        Fikia wateja wengi zaidi kupitia jukwaa letu la kidijitali.
                        Biashara yako itaonekana kwa watu wengi zaidi.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-visual mb-4">
                        <div class="feature-shape">
                            <div class="shape-inner">24/7</div>
                        </div>
                    </div>
                    <h4 class="feature-title mb-3">Kidijitali Kabisa</h4>
                    <p class="feature-text text-muted">
                        Tangaza biashara yako kupitia njia za kidijitali.
                        Rahisi, haraka na ya ufanisi.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center p-4 h-100">
                    <div class="feature-visual mb-4">
                        <div class="feature-progress">
                            <div class="progress-circle">
                                <span class="progress-text">85%</span>
                            </div>
                        </div>
                    </div>
                    <h4 class="feature-title mb-3">Ukuaji wa Biashara</h4>
                    <p class="feature-text text-muted">
                        Ona ukuaji wa biashara yako kupitia matangazo bora.
                        Fikia wateja mpya na ukuze mauzo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12" data-aos="fade-up">
                <h2 class="section-title">Jinsi ya Kutangaza Biashara</h2>
                <p class="section-subtitle">Hatua tatu rahisi za kutangaza biashara yako</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">1</div>
                    <h4 class="step-title mb-3">Jisajili</h4>
                    <p class="step-text text-muted">
                        Unda akaunti yako kwa kujaza fomu rahisi ya usajili
                    </p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">2</div>
                    <h4 class="step-title mb-3">Ongeza Biashara</h4>
                    <p class="step-text text-muted">
                        Ongeza maelezo ya biashara yako na picha
                    </p>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card text-center p-4">
                    <div class="step-number mb-3">3</div>
                    <h4 class="step-title mb-3">Tangaza</h4>
                    <p class="step-text text-muted">
                        Biashara yako itaonekana kwa wateja wote
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="cta-title">Je, Una Biashara Unayotaka Kutangaza?</h2>
                <p class="cta-subtitle">
                    Usisubiri tena! Jisajili sasa na uanze kutangaza biashara yako kupitia jukwaa letu la kidijitali.
                    Fikia wateja wengi zaidi na ukuze biashara yako.
                </p>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <a href="signup.php" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-rocket me-2"></i>Anza Sasa
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6" data-aos="fade-right">
                <h3 class="contact-title mb-4">Wasiliana Nasi</h3>
                <p class="contact-text mb-4">
                    Una swali? Tunahitaji kusikia kutoka kwako.
                    Wasiliana nasi kupitia njia zifuatazo:
                </p>
                <div class="contact-info">
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone text-primary me-3"></i>
                        <a href="tel:+25573428334" class="text-decoration-none">+255 734 283 34</a>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope text-primary me-3"></i>
                        <a href="mailto:info@pandadigital.co.tz" class="text-decoration-none">info@pandadigital.co.tz</a>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-map-marker-alt text-primary me-3"></i>
                        <span>Dar Es Salaam, Tanzania</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="contact-form-wrapper">
                    <h4 class="mb-3">Tuma Ujumbe</h4>
                    <form class="contact-form">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Jina lako" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Barua pepe" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Ujumbe wako" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Tuma Ujumbe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

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

    .header-icon {
        opacity: 0.8;
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

    .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Features Section */
    .feature-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-visual {
        margin: 0 auto;
    }

    .feature-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-color, #ffbc3b);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .feature-label {
        font-size: 0.9rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .feature-shape {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-shape {
        transform: rotate(45deg) scale(1.1);
    }

    .shape-inner {
        transform: rotate(-45deg);
        color: white;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .progress-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: conic-gradient(var(--primary-color, #ffbc3b) 0deg, var(--primary-color, #ffbc3b) 306deg, #e2e8f0 306deg, #e2e8f0 360deg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
        transition: all 0.3s ease;
    }

    .feature-card:hover .progress-circle {
        transform: scale(1.1);
    }

    .progress-text {
        background: white;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--primary-color, #ffbc3b);
        font-size: 1.1rem;
    }

    .feature-title {
        color: #1e293b;
        font-weight: 600;
    }

    /* How It Works Section */
    .step-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .step-title {
        color: #1e293b;
        font-weight: 600;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Contact Section */
    .contact-title {
        color: #1e293b;
        font-weight: 600;
    }

    .contact-text {
        color: #64748b;
    }

    .contact-item {
        display: flex;
        align-items: center;
    }

    .contact-item a {
        color: #1e293b;
    }

    .contact-item a:hover {
        color: var(--primary-color, #ffbc3b);
    }

    .contact-form-wrapper {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .contact-form .form-control {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .contact-form .form-control:focus {
        border-color: var(--primary-color, #ffbc3b);
        box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
    }

    /* General Styles */
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        margin-bottom: 0;
    }

    .btn {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color, #ffbc3b) 0%, #e6a800 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 188, 59, 0.3);
    }

    .btn-light {
        background: white;
        color: var(--primary-color, #ffbc3b);
        border: none;
    }

    .btn-light:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-light {
        border: 2px solid white;
        color: white;
    }

    .btn-outline-light:hover {
        background: white;
        color: var(--primary-color, #ffbc3b);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2.5rem;
        }

        .cta-title {
            font-size: 2rem;
        }

        .section-title {
            font-size: 2rem;
        }
    }
</style>