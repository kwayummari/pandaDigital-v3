<?php
require_once 'config/init.php';
require_once 'models/OngeaHub.php';

// Initialize the Ongea Hub model
$ongeaHubModel = new OngeaHub();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Hali ya usalama imekataa. Tafadhali jaribu tena.';
        $messageType = 'danger';
    } else {
        // Validate input
        $fname = trim($_POST['fname'] ?? '');
        $sname = trim($_POST['sname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $tarehe_ya_tukio = $_POST['tarehe_ya_tukio'] ?? '';
        $msaada = $_POST['msaada'] ?? '';
        $report = trim($_POST['report'] ?? '');

        // Basic validation
        if (empty($fname) || empty($sname) || empty($phone) || empty($region) || empty($tarehe_ya_tukio) || empty($msaada) || empty($report)) {
            $message = 'Tafadhali jaza sehemu zote za muhimu.';
            $messageType = 'danger';
        } elseif (!$ongeaHubModel->validatePhone($phone)) {
            $message = 'Namba ya simu si sahihi. Tafadhali weka namba sahihi ya Tanzania.';
            $messageType = 'danger';
        } elseif (!$ongeaHubModel->validateReportText($report)) {
            $message = 'Maelezo ni mengi. Tafadhali punguza hadi maneno 150.';
            $messageType = 'danger';
        } else {
            // Prepare data for submission
            $data = [
                'fname' => ucwords($fname),
                'sname' => ucwords($sname),
                'phone' => $ongeaHubModel->validatePhone($phone),
                'region' => ucwords($region),
                'tarehe_ya_tukio' => $tarehe_ya_tukio,
                'msaada' => ucwords($msaada),
                'report' => ucwords($report),
                'report_date' => date('Y-m-d H:i:s')
            ];

            // Submit report
            if ($ongeaHubModel->submitReport($data)) {
                $message = 'Taarifa za tukio zimetumwa kikamilifu. Tutakuhusiana nawe hivi karibuni.';
                $messageType = 'success';

                // Clear form data after successful submission
                $_POST = [];
            } else {
                $message = 'Kuna tatizo la kiufundi. Tafadhali jaribu tena au wasiliana nasi moja kwa moja.';
                $messageType = 'danger';
            }
        }
    }
}

// Get assistance types and regions for the form
$assistanceTypes = $ongeaHubModel->getAssistanceTypes();
$regions = $ongeaHubModel->getRegions();

$pageTitle = 'Ongea Hub - ' . $appConfig['name'];

include 'includes/header.php';
?>

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
            <!-- <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="header-icon">
                    <div class="chat-icon text-white" style="font-size: 4rem; font-weight: bold;"></div>
                </div>
            </div> -->
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="ongea-hub-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Ongea Hub</h2>
                <p class="section-subtitle">Jukwaa la kusaidia wasichana kuripoti kesi za udhalilishaji na kupata msaada</p>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <span class="me-2"></span>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-7 mb-4 mb-lg-0" data-aos="fade-right">
                <div class="form-card">
                    <h3 class="form-title mb-4">
                        <span class="me-2 text-primary"></span>
                        Ripoti Tukio
                    </h3>

                    <form method="post" class="ongea-form">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">
                                    <span class="me-2 text-primary"></span>
                                    Jina La Kwanza <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control <?= isset($_POST['fname']) && empty($_POST['fname']) ? 'is-invalid' : '' ?>"
                                    id="fname"
                                    name="fname"
                                    value="<?= old('fname', $_POST['fname'] ?? '') ?>"
                                    placeholder="Jina La Kwanza"
                                    required>
                                <div class="invalid-feedback">
                                    Jina la kwanza ni muhimu.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sname" class="form-label">
                                    <span class="me-2 text-primary"></span>
                                    Jina La Pili <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control <?= isset($_POST['sname']) && empty($_POST['sname']) ? 'is-invalid' : '' ?>"
                                    id="sname"
                                    name="sname"
                                    value="<?= old('sname', $_POST['sname'] ?? '') ?>"
                                    placeholder="Jina La Pili"
                                    required>
                                <div class="invalid-feedback">
                                    Jina la pili ni muhimu.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <span class="me-2 text-primary"></span>
                                    Namba Ya Simu <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                    class="form-control <?= isset($_POST['phone']) && !empty($_POST['phone']) && !$ongeaHubModel->validatePhone($_POST['phone']) ? 'is-invalid' : '' ?>"
                                    id="phone"
                                    name="phone"
                                    value="<?= old('phone', $_POST['phone'] ?? '') ?>"
                                    placeholder="Mfano: 0712345678"
                                    maxlength="10"
                                    minlength="10"
                                    required>
                                <div class="invalid-feedback">
                                    Namba ya simu si sahihi. Tafadhali weka namba sahihi ya Tanzania.
                                </div>
                                <small class="form-text text-muted">Weka namba ya simu ya Tanzania (mwanzo na 0)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="region" class="form-label">
                                    <span class="me-2 text-primary"></span>
                                    Mkoa <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= isset($_POST['region']) && empty($_POST['region']) ? 'is-invalid' : '' ?>"
                                    id="region"
                                    name="region"
                                    required>
                                    <option value="">Chagua Mkoa</option>
                                    <?php foreach ($regions as $region): ?>
                                        <option value="<?= $region ?>" <?= (old('region', $_POST['region'] ?? '') === $region) ? 'selected' : '' ?>>
                                            <?= $region ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Tafadhali chagua mkoa.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tarehe_ya_tukio" class="form-label">
                                <span class="me-2 text-primary"></span>
                                Tarehe Ya Tukio <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                class="form-control <?= isset($_POST['tarehe_ya_tukio']) && empty($_POST['tarehe_ya_tukio']) ? 'is-invalid' : '' ?>"
                                id="tarehe_ya_tukio"
                                name="tarehe_ya_tukio"
                                value="<?= old('tarehe_ya_tukio', $_POST['tarehe_ya_tukio'] ?? '') ?>"
                                required>
                            <div class="invalid-feedback">
                                Tarehe ya tukio ni muhimu.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="msaada" class="form-label">
                                <span class="me-2 text-primary"></span>
                                Unahitaji Msaada Gani? <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= isset($_POST['msaada']) && empty($_POST['msaada']) ? 'is-invalid' : '' ?>"
                                id="msaada"
                                name="msaada"
                                required>
                                <option value="">Chagua Aina Ya Msaada</option>
                                <?php foreach ($assistanceTypes as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= (old('msaada', $_POST['msaada'] ?? '') === $key) ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Tafadhali chagua aina ya msaada unayohitaji.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="report" class="form-label">
                                <span class="me-2 text-primary"></span>
                                Taarifa Za Tukio <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?= isset($_POST['report']) && !empty($_POST['report']) && !$ongeaHubModel->validateReportText($_POST['report']) ? 'is-invalid' : '' ?>"
                                id="report"
                                name="report"
                                rows="5"
                                placeholder="Eleza tukio kwa ufupi na uwazi..."
                                required><?= old('report', $_POST['report'] ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                Maelezo ni mengi au mafupi mno. Tafadhali weka maelezo sahihi.
                            </div>
                            <small class="form-text text-muted">
                                <span class="me-1"></span>
                                Maelezo yanapaswa kuwa kati ya maneno 1-150.
                                <span id="word-count" class="text-muted">0 maneno</span>
                            </small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                <span class="me-2"></span>
                                TUMA TAARIFA
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Section -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="info-card">
                    <h3 class="info-title mb-4">
                        <span class="me-2 text-primary"></span>
                        Kwa Nini Ongea Hub?
                    </h3>

                    <div class="info-content">
                        <p class="mb-4">
                            Taarifa yako ni muhimu sana. Hili ni jukwaa mahsusi kwaajili ya kuripoti kesi za udhalilishaji na kuunganishwa na msaada wa haraka.
                        </p>

                        <div class="info-features mb-4">
                            <div class="feature-item mb-3">
                                <span class="text-success me-2"></span>
                                <span>Usiri kamili wa taarifa zako</span>
                            </div>
                            <div class="feature-item mb-3">
                                <span class="text-info me-2"></span>
                                <span>Msaada wa haraka na wa karibu</span>
                            </div>
                            <div class="feature-item mb-3">
                                <span class="text-warning me-2"></span>
                                <span>Ushirikiano na mamlaka husika</span>
                            </div>
                            <div class="feature-item mb-3">
                                <span class="text-danger me-2"></span>
                                <span>Msaada wa kisaikolojia na kijamii</span>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h5 class="mb-3">
                                <span class="me-2 text-primary"></span>
                                Wasiliana Nasi
                            </h5>

                            <div class="contact-item mb-3">
                                <span class="me-2 text-success"></span>
                                <a href="tel:+25573428334" class="text-decoration-none">
                                    +255 734 283 34
                                </a>
                            </div>

                            <div class="contact-item mb-3">
                                <span class="me-2 text-info"></span>
                                <a href="mailto:info@pandadigital.co.tz" class="text-decoration-none">
                                    info@pandadigital.co.tz
                                </a>
                            </div>

                            <div class="contact-item mb-3">
                                <span class="me-2 text-warning"></span>
                                <span>Dar Es Salaam, Tanzania</span>
                            </div>
                        </div>

                        <div class="emergency-info mt-4 p-3 bg-light rounded">
                            <h6 class="text-danger mb-2">
                                <span class="me-2"></span>
                                Dharura?
                            </h6>
                            <p class="mb-2 small">Kama unahitaji msaada wa haraka, piga simu moja kwa moja:</p>
                            <a href="tel:+25573428334" class="btn btn-danger btn-sm w-100">
                                <span class="me-2"></span>
                                Piga Sasa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Ongea Hub Nambari</h2>
                <p class="section-subtitle">Tunaendelea kusaidia wasichana kupata msaada wao</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card text-center">
                    <div class="stat-icon mb-3">
                        <span class="text-primary" style="font-size: 3rem;"></span>
                    </div>
                    <h3 class="stat-number">500+</h3>
                    <p class="stat-label">Wasichana Wamepokea Msaada</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card text-center">
                    <div class="stat-icon mb-3">
                        <span class="text-success" style="font-size: 3rem;"></span>
                    </div>
                    <h3 class="stat-number">25+</h3>
                    <p class="stat-label">Mashirika Yanayoshiriki</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card text-center">
                    <div class="stat-icon mb-3">
                        <span class="text-info" style="font-size: 3rem;"></span>
                    </div>
                    <h3 class="stat-number">24h</h3>
                    <p class="stat-label">Muda Wa Msaada</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card text-center">
                    <div class="stat-icon mb-3">
                        <span class="text-warning" style="font-size: 3rem;"></span>
                    </div>
                    <h3 class="stat-number">98%</h3>
                    <p class="stat-label">Uthibitisho Wa Msaada</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h2 class="cta-title">Je, Unahitaji Msaada Wa Haraka?</h2>
                <p class="cta-subtitle">
                    Usikie ukimya. Ongea Hub ni jukwaa salama la kuripoti na kupata msaada.
                    Tunahitaji kusikia kutoka kwako.
                </p>
                <div class="cta-buttons">
                    <a href="tel:+25573428334" class="btn btn-light btn-lg me-3">
                        <span class="me-2"></span>Piga Simu
                    </a>
                    <a href="mailto:info@pandadigital.co.tz" class="btn btn-outline-light btn-lg">
                        <span class="me-2"></span>Tuma Barua
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Additional Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
        }

        // Word count for report textarea
        const reportTextarea = document.getElementById('report');
        const wordCountElement = document.getElementById('word-count');

        if (reportTextarea && wordCountElement) {
            reportTextarea.addEventListener('input', function() {
                const text = this.value;
                const wordCount = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
                wordCountElement.textContent = wordCount + ' maneno';

                // Change color based on word count
                if (wordCount > 150) {
                    wordCountElement.className = 'text-muted text-danger';
                } else if (wordCount > 100) {
                    wordCountElement.className = 'text-muted text-warning';
                } else {
                    wordCountElement.className = 'text-muted';
                }
            });
        }

        // Phone number formatting
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 0 && value[0] !== '0') {
                    value = '0' + value;
                }
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                this.value = value;
            });
        }

        // Form validation
        const form = document.querySelector('.ongea-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = this.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        }

        // Debug logging
        console.log('Ongea Hub JavaScript loaded successfully');
        console.log('Report textarea:', reportTextarea);
        console.log('Phone input:', phoneInput);
        console.log('Form:', form);
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

    /* Section title styles */
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

    /* Form card styles */
    .form-card {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .form-title {
        color: #1e293b;
        font-weight: 600;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color, #ffbc3b);
        box-shadow: 0 0 0 0.2rem rgba(255, 188, 59, 0.25);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
    }

    /* Info card styles */
    .info-card {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .info-title {
        color: #1e293b;
        font-weight: 600;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
    }

    .info-content p {
        color: #64748b;
        line-height: 1.6;
    }

    .feature-item {
        display: flex;
        align-items: center;
        color: #374151;
    }

    .feature-item i {
        width: 20px;
    }

    .contact-info h5 {
        color: #1e293b;
        font-weight: 600;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .contact-item a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-item a:hover {
        color: var(--primary-color, #ffbc3b);
    }

    .emergency-info {
        border-left: 4px solid #dc3545;
    }

    /* Stats section styles */
    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        color: var(--primary-color, #ffbc3b);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #64748b;
        font-weight: 500;
        margin: 0;
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
        margin-bottom: 2rem;
    }

    .cta-buttons {
        margin-top: 2rem;
    }

    /* Button styles */
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
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 255, 255, 0.3);
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

    .btn-danger {
        background: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background: #c82333;
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

        .form-card,
        .info-card {
            padding: 2rem;
        }

        .stat-number {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {

        .form-card,
        .info-card {
            padding: 1.5rem;
        }

        .cta-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .cta-buttons .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>