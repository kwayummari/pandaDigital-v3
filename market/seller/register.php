<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../../auth/login.php');
    exit;
}

// Check if user is already a seller
if ($_SESSION['is_seller']) {
    header('Location: ../dashboard.php');
    exit;
}

$pageTitle = "Jisajili kama Muuzaji - Panda Market";

include '../../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Jisajili kama Muuzaji</h1>
                <p class="page-subtitle">Uza bidhaa zako kwenye Panda Market na ufike watumiaji wengi</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Jisajili Muuzaji</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Seller Registration Content -->
<section class="seller-registration-content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Benefits Card -->
                <div class="card mb-4 bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-3x mb-3"></i>
                        <h4>Kwa nini ujisajili kama Muuzaji?</h4>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h6>Wateja Wengi</h6>
                                <p class="small">Fikia watumiaji wengi kote Tanzania</p>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h6>Biashara Ikuwe</h6>
                                <p class="small">Ongeza mapato na ukuaji wa biashara</p>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-tools fa-2x mb-2"></i>
                                <h6>Huduma Kamili</h6>
                                <p class="small">Pata msaada wa teknolojia na usimamizi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Fomu ya Usajili wa Muuzaji
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="sellerRegistrationForm">
                            <!-- Business Information -->
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-building me-2"></i>Maelezo ya Biashara
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_name" class="form-label">Jina la Biashara *</label>
                                    <input type="text" class="form-control" id="business_name" name="business_name"
                                        value="<?= htmlspecialchars($_SESSION['user_business'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_type" class="form-label">Aina ya Biashara *</label>
                                    <select class="form-select" id="business_type" name="business_type" required>
                                        <option value="">Chagua aina ya biashara</option>
                                        <option value="Mkulima">Mkulima</option>
                                        <option value="Mfanyikazi">Mfanyikazi</option>
                                        <option value="Mfanyabiashara">Mfanyabiashara</option>
                                        <option value="Mtaalamu">Mtaalamu</option>
                                        <option value="Nyingine">Nyingine</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_address" class="form-label">Anwani ya Biashara</label>
                                    <input type="text" class="form-control" id="business_address" name="business_address"
                                        placeholder="Mfano: Mtaa wa Mwenge, Dar es Salaam">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_phone" class="form-label">Simu ya Biashara *</label>
                                    <input type="tel" class="form-control" id="business_phone" name="business_phone"
                                        value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_email" class="form-label">Barua Pepe ya Biashara</label>
                                    <input type="email" class="form-control" id="business_email" name="business_email"
                                        value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>">
                                    <small class="text-muted">Ikiwa tofauti na barua pepe yako ya kibinafsi</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_website" class="form-label">Website ya Biashara</label>
                                    <input type="url" class="form-control" id="business_website" name="business_website"
                                        placeholder="https://example.com">
                                </div>
                            </div>

                            <!-- Product Information -->
                            <hr class="my-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-box me-2"></i>Maelezo ya Bidhaa
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="product_categories" class="form-label">Kategoria za Bidhaa *</label>
                                    <select class="form-select" id="product_categories" name="product_categories[]" multiple required>
                                        <option value="Mazao">Mazao</option>
                                        <option value="Vifaa">Vifaa</option>
                                        <option value="Nguo">Nguo</option>
                                        <option value="Chakula">Chakula</option>
                                        <option value="Dawa">Dawa</option>
                                        <option value="Teknolojia">Teknolojia</option>
                                        <option value="Nyingine">Nyingine</option>
                                    </select>
                                    <small class="text-muted">Chagua kategoria 1 au zaidi</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estimated_products" class="form-label">Idadi ya Bidhaa *</label>
                                    <select class="form-select" id="estimated_products" name="estimated_products" required>
                                        <option value="">Chagua idadi</option>
                                        <option value="1-10">1-10 bidhaa</option>
                                        <option value="11-50">11-50 bidhaa</option>
                                        <option value="51-100">51-100 bidhaa</option>
                                        <option value="100+">Zaidi ya 100 bidhaa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="business_description" class="form-label">Maelezo ya Biashara *</label>
                                <textarea class="form-control" id="business_description" name="business_description" rows="4"
                                    placeholder="Eleza biashara yako, bidhaa unazouza, na uzoefu wako..." required></textarea>
                                <small class="text-muted">Jaza maelezo kamili ya biashara yako</small>
                            </div>

                            <!-- Terms and Conditions -->
                            <hr class="my-4">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                    <label class="form-check-label" for="agree_terms">
                                        Nakubaliana na <a href="../terms_conditions.php" target="_blank">sheria na masharti</a> ya Panda Market
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree_policies" name="agree_policies" required>
                                    <label class="form-check-label" for="agree_policies">
                                        Nakubaliana na <a href="../help_center.php" target="_blank">siasa za biashara</a> na kanuni za ubora
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../panda-market.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Rudi Nyuma
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-2"></i>Wasilisha Ombi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Maelezo Muhimu
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Ombi lako litakaguliwa ndani ya siku 2-3 za kazi</li>
                            <li>Utapata barua pepe ya uthibitisho mara tu ombi likapokewa</li>
                            <li>Unaweza kuanza kuuza bidhaa mara tu uthibitisho upokelewe</li>
                            <li>Timu yetu itakusaidia kuanza na kukuza biashara yako</li>
                            <li>Kama una maswali, wasiliana nasi kupitia WhatsApp au barua pepe</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../../includes/footer.php'; ?>

<style>
    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
    }
</style>

<script>
    // Form submission
    document.getElementById('sellerRegistrationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        // Get form data
        const formData = new FormData(this);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inatumwa...';
        submitBtn.disabled = true;

        // Submit form data
        fetch('process_seller_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ombi lako limetumwa kwa mafanikio! Tutakujibu ndani ya siku 2-3 za kazi.');
                    window.location.href = '../user/profile.php';
                } else {
                    alert('Kosa: ' + data.message);
                }
            })
            .catch(error => {
                alert('Kosa la mfumo. Tafadhali jaribu tena.');
                console.error('Error:', error);
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
    });

    // Multiple select for product categories
    document.getElementById('product_categories').addEventListener('change', function() {
        const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
        if (selectedOptions.length > 0) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('Tafadhali chagua kategoria angalau moja');
        }
    });
</script>