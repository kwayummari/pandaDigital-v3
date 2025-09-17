<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

$pageTitle = "Center ya Msaada - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Center ya Msaada</h1>
                <p class="page-subtitle">Pata majibu ya maswali yako na msaada wa haraka</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Msaada</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Help Center Content -->
<section class="help-center-content py-5">
    <div class="container">
        <!-- Search Help -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                        <h4>Tafuta Msaada</h4>
                        <p class="text-muted">Andika swali lako au chagua kategoria ya msaada</p>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="helpSearch"
                                placeholder="Mfano: Jinsi ya kununua bidhaa...">
                            <button class="btn btn-primary btn-lg" type="button" onclick="searchHelp()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="text-center mb-4">Kategoria za Msaada</h3>
            </div>
            <div class="col-md-4 mb-4">
                <div class="help-category-card text-center p-4 h-100">
                    <div class="category-icon mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                    </div>
                    <h5>Kununua na Kuuza</h5>
                    <p class="text-muted">Jifunze jinsi ya kununua na kuuza bidhaa kwenye Panda Market</p>
                    <button class="btn btn-outline-primary" onclick="showCategory('buying-selling')">
                        <i class="fas fa-arrow-right me-2"></i>Jifunze Zaidi
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="help-category-card text-center p-4 h-100">
                    <div class="category-icon mb-3">
                        <i class="fas fa-credit-card fa-3x text-success"></i>
                    </div>
                    <h5>Malipo na Usafiri</h5>
                    <p class="text-muted">Maelezo kuhusu njia za malipo na usafiri wa bidhaa</p>
                    <button class="btn btn-outline-success" onclick="showCategory('payment-shipping')">
                        <i class="fas fa-arrow-right me-2"></i>Jifunze Zaidi
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="help-category-card text-center p-4 h-100">
                    <div class="category-icon mb-3">
                        <i class="fas fa-user-shield fa-3x text-warning"></i>
                    </div>
                    <h5>Usalama na Privacy</h5>
                    <p class="text-muted">Jifunze kuhusu usalama wa data yako na privacy</p>
                    <button class="btn btn-outline-warning" onclick="showCategory('security-privacy')">
                        <i class="fas fa-arrow-right me-2"></i>Jifunze Zaidi
                    </button>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mb-4">Maswali Yanayoulizwa Sana (FAQ)</h3>
            </div>
        </div>

        <!-- Buying and Selling FAQ -->
        <div class="faq-category" id="buying-selling">
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-shopping-cart me-2"></i>Kununua na Kuuza
                    </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="accordion" id="buyingSellingAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Jinsi ya kununua bidhaa kwenye Panda Market?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#buyingSellingAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Enda kwenye Panda Market</li>
                                        <li>Chagua bidhaa unayopenda</li>
                                        <li>Bofya "Ongeza kwenye Cart"</li>
                                        <li>Enda kwenye Cart na uendelee na checkout</li>
                                        <li>Jaza maelezo yako na ufanye malipo</li>
                                        <li>Subiri bidhaa ifike kwako</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    Je, ninaweza kuuza bidhaa zangu kwenye Panda Market?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#buyingSellingAccordion">
                                <div class="accordion-body">
                                    Ndiyo! Unaweza kuuza bidhaa zako kwenye Panda Market. Ili kuanza:
                                    <ul>
                                        <li>Jisajili kama muuzaji</li>
                                        <li>Jaza maelezo ya biashara yako</li>
                                        <li>Ongeza bidhaa zako</li>
                                        <li>Subiri uthibitisho</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    Je, bidhaa zote zina warranty?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#buyingSellingAccordion">
                                <div class="accordion-body">
                                    Si bidhaa zote zina warranty. Tafadhali soma maelezo ya bidhaa kwa makini
                                    kabla ya kununua. Kama una maswali, wasiliana na muuzaji moja kwa moja.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-lightbulb me-2"></i>Kidokezo
                            </h6>
                            <p class="card-text">
                                Kabla ya kununua, hakikisha unaweza kuwasiliana na muuzaji kupitia
                                WhatsApp au simu ili kupata maelezo zaidi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment and Shipping FAQ -->
        <div class="faq-category" id="payment-shipping" style="display: none;">
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="text-success mb-3">
                        <i class="fas fa-credit-card me-2"></i>Malipo na Usafiri
                    </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="accordion" id="paymentShippingAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading4">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    Njia zipi za malipo zinazokubalika?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse show" data-bs-parent="#paymentShippingAccordion">
                                <div class="accordion-body">
                                    Tunakubali njia zifuatazo za malipo:
                                    <ul>
                                        <li><strong>M-Pesa:</strong> Namba: 0767-680-463</li>
                                        <li><strong>Airtel Money:</strong> Namba: 0767-680-463</li>
                                        <li><strong>Benki:</strong> Tunaungana na benki mbalimbali</li>
                                        <li><strong>Cash on Delivery:</strong> Kwa baadhi ya bidhaa</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                    Muda gani utachukua bidhaa ifike kwangu?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#paymentShippingAccordion">
                                <div class="accordion-body">
                                    Muda wa kufika hutegemea:
                                    <ul>
                                        <li><strong>Mahali ulipo:</strong> Dar es Salaam (1-2 siku), Miji mingine (2-3 siku)</li>
                                        <li><strong>Aina ya bidhaa:</strong> Bidhaa kubwa zinaweza kuchukua muda mrefu</li>
                                        <li><strong>Muda wa malipo:</strong> Mara tu malipo yanapothibitishwa</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
                                    Je, bei ya usafiri imojumuishwa?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#paymentShippingAccordion">
                                <div class="accordion-body">
                                    Bei ya usafiri haijumuishwa kwenye bei ya bidhaa. Utapata maelezo kamili
                                    ya bei ya usafiri kabla ya kufanya malipo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>Maelezo
                            </h6>
                            <p class="card-text">
                                Bei ya usafiri hutegemea umbali na ukubwa wa bidhaa.
                                Unaweza kupata makadirio ya bei ya usafiri kabla ya kununua.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security and Privacy FAQ -->
        <div class="faq-category" id="security-privacy" style="display: none;">
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="text-warning mb-3">
                        <i class="fas fa-user-shield me-2"></i>Usalama na Privacy
                    </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="accordion" id="securityPrivacyAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading7">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7">
                                    Je, data yangu iko salama?
                                </button>
                            </h2>
                            <div id="collapse7" class="accordion-collapse collapse show" data-bs-parent="#securityPrivacyAccordion">
                                <div class="accordion-body">
                                    Ndiyo! Tunatumia teknolojia za usalama za hali ya juu:
                                    <ul>
                                        <li>Encryption ya data yote</li>
                                        <li>Secure servers</li>
                                        <li>Hakuna kushiriki data na watu wengine</li>
                                        <li>Ufikiaji mdogo kwa data ya watumiaji</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading8">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8">
                                    Je, ninaweza kufuta account yangu?
                                </button>
                            </h2>
                            <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#securityPrivacyAccordion">
                                <div class="accordion-body">
                                    Ndiyo, unaweza kufuta account yako wakati wowote. Data yako itafutwa
                                    kabisa kutoka kwenye servers zetu. Tafadhali wasiliana nasi kupitia
                                    barua pepe au WhatsApp.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading9">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9">
                                    Je, ninaweza kubadilisha password yangu?
                                </button>
                            </h2>
                            <div id="collapse9" class="accordion-collapse collapse" data-bs-parent="#securityPrivacyAccordion">
                                <div class="accordion-body">
                                    Ndiyo! Unaweza kubadilisha password yako wakati wowote kutoka kwenye
                                    profile yako. Hakikisha unatumia password ngumu na isiyo rahisi kugundua.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-shield-alt me-2"></i>Usalama
                            </h6>
                            <p class="card-text">
                                Tunatumia teknolojia za usalama za hali ya juu kuhakikisha data yako
                                iko salama na haifikiwi na watu wasio na ruhusa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-headset fa-3x mb-3"></i>
                        <h3>Bado Unahitaji Msaada?</h3>
                        <p class="mb-4">Timu yetu ya msaada iko tayari kukusaidia. Wasiliana nasi kupitia njia zifuatazo:</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="https://wa.me/255767680463" class="btn btn-success btn-lg" target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                            <a href="tel:+255767680463" class="btn btn-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Piga Simu
                            </a>
                            <a href="mailto:info@pandadigital.co.tz" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>Barua Pepe
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .help-category-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: white;
    }

    .help-category-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1);
        transform: translateY(-2px);
    }

    .category-icon {
        margin-bottom: 20px;
    }

    .faq-category {
        margin-bottom: 4rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

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

    .btn-lg {
        padding: 12px 24px;
        font-weight: 600;
    }
</style>

<script>
    function showCategory(categoryId) {
        // Hide all categories
        document.querySelectorAll('.faq-category').forEach(cat => {
            cat.style.display = 'none';
        });

        // Show selected category
        document.getElementById(categoryId).style.display = 'block';

        // Scroll to category
        document.getElementById(categoryId).scrollIntoView({
            behavior: 'smooth'
        });
    }

    function searchHelp() {
        const searchTerm = document.getElementById('helpSearch').value.toLowerCase();

        if (searchTerm.trim() === '') {
            alert('Tafadhali andika neno la kutafuta');
            return;
        }

        // Simple search implementation
        const searchResults = [];

        // Search in FAQ content
        const faqItems = document.querySelectorAll('.accordion-body');
        faqItems.forEach((item, index) => {
            if (item.textContent.toLowerCase().includes(searchTerm)) {
                searchResults.push(index);
            }
        });

        if (searchResults.length > 0) {
            // Show first result
            const firstResult = searchResults[0];
            const accordionItem = document.querySelectorAll('.accordion-item')[firstResult];
            const collapse = accordionItem.querySelector('.accordion-collapse');

            // Expand the accordion item
            const bsCollapse = new bootstrap.Collapse(collapse, {
                show: true
            });

            // Scroll to result
            accordionItem.scrollIntoView({
                behavior: 'smooth'
            });

            alert(`Pata matokeo ${searchResults.length}. Tumeonyesha matokeo ya kwanza.`);
        } else {
            alert('Hakuna matokeo yaliyopatikana. Jaribu neno lingine.');
        }
    }

    // Show buying-selling category by default
    document.addEventListener('DOMContentLoaded', function() {
        showCategory('buying-selling');
    });
</script>