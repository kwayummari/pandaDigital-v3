<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// For now, we'll show a placeholder since checkout functionality isn't fully implemented
// In a real system, you would check if user is logged in and fetch their cart items

$pageTitle = "Checkout - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Checkout</h1>
                <p class="page-subtitle">Kamilisha ununuzi wako</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('market/cart.php') ?>">Cart</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Content -->
<section class="checkout-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="checkout-placeholder text-center">
                    <i class="fas fa-credit-card fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Checkout Bado Haijakamilika</h3>
                    <p class="text-muted mb-4">
                        Kipengele cha checkout kiko katika maendeleo. Tafadhali rudi kwenye cart au market.
                    </p>
                    <div class="checkout-actions">
                        <a href="<?= app_url('market/cart.php') ?>" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-shopping-cart me-2"></i>Rudi kwenye Cart
                        </a>
                        <a href="<?= app_url('panda-market.php') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Enda kwenye Market
                        </a>
                    </div>
                </div>

                <!-- Sample Checkout Form (for demonstration) -->
                <div class="sample-checkout mt-5" style="display: none;">
                    <h4 class="section-title mb-4">Fomu ya Checkout</h4>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Maelezo ya Mteja</h5>
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="firstName" class="form-label">Jina la Kwanza</label>
                                                <input type="text" class="form-control" id="firstName" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="lastName" class="form-label">Jina la Mwisho</label>
                                                <input type="text" class="form-control" id="lastName" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Barua Pepe</label>
                                            <input type="email" class="form-control" id="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Namba ya Simu</label>
                                            <input type="tel" class="form-control" id="phone" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Anwani</label>
                                            <textarea class="form-control" id="address" rows="3" required></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="city" class="form-label">Mji</label>
                                                <input type="text" class="form-control" id="city" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="postalCode" class="form-label">Msimbo wa Posta</label>
                                                <input type="text" class="form-control" id="postalCode">
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <h5 class="card-title mb-4">Njia ya Malipo</h5>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="paymentMethod" id="mpesa" value="mpesa" checked>
                                                <label class="form-check-label" for="mpesa">
                                                    <i class="fas fa-mobile-alt me-2"></i>M-Pesa
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="paymentMethod" id="airtel" value="airtel">
                                                <label class="form-check-label" for="airtel">
                                                    <i class="fas fa-mobile-alt me-2"></i>Airtel Money
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="paymentMethod" id="bank" value="bank">
                                                <label class="form-check-label" for="bank">
                                                    <i class="fas fa-university me-2"></i>Benki
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="goBack()">
                                                <i class="fas fa-arrow-left me-2"></i>Rudi Nyuma
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-lock me-2"></i>Lipa Sasa
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Muhtasari wa Order</h5>
                                    <div class="order-summary">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Bidhaa ya Mfano 1</span>
                                            <span>Tsh.25,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Bidhaa ya Mfano 2</span>
                                            <span>Tsh.30,000</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Jumla ya Bidhaa:</span>
                                            <span>Tsh.55,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Punguzo:</span>
                                            <span class="text-success">-Tsh.0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Kodi:</span>
                                            <span>Tsh.0</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>Jumla:</span>
                                            <span>Tsh.55,000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .checkout-placeholder {
        padding: 4rem 0;
    }

    .checkout-actions .btn {
        padding: 12px 24px;
        font-weight: 600;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }

    .section-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .order-summary {
        font-size: 14px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    function goBack() {
        window.history.back();
    }

    // Show sample checkout after 3 seconds (for demonstration)
    setTimeout(function() {
        document.querySelector('.sample-checkout').style.display = 'block';
    }, 3000);
</script>