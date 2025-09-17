<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Get order ID from URL
$orderId = $_GET['order_id'] ?? null;

// For now, we'll show a placeholder since order confirmation functionality isn't fully implemented
// In a real system, you would fetch order details from the database

$pageTitle = "Order Confirmation - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Order Imekamilika!</h1>
                <p class="page-subtitle">Asante kwa ununuzi wako</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Order Confirmation</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Order Confirmation Content -->
<section class="order-confirmation-content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-success">
                    <div class="card-body text-center p-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h2 class="card-title text-success mb-3">Order Imekamilika!</h2>
                        <p class="card-text text-muted mb-4">
                            Asante kwa ununuzi wako. Order yako imepokelewa na inakusanywa.
                            Utapata barua pepe ya uthibitisho na maelezo ya tracking.
                        </p>

                        <?php if ($orderId): ?>
                            <div class="order-details mb-4">
                                <h5>Order ID: #<?php echo htmlspecialchars($orderId); ?></h5>
                                <p class="text-muted">Hifadhi namba hii kwa ajili ya kufuatilia order yako</p>
                            </div>
                        <?php endif; ?>

                        <div class="next-steps mb-4">
                            <h5>Hatua Zinazofuata:</h5>
                            <div class="row text-start mt-3">
                                <div class="col-md-6">
                                    <div class="step-item d-flex align-items-center mb-3">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">1</div>
                                        <span>Order itakusanywa na kuandaliwa</span>
                                    </div>
                                    <div class="step-item d-flex align-items-center mb-3">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">2</div>
                                        <span>Utapata barua pepe ya uthibitisho</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="step-item d-flex align-items-center mb-3">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">3</div>
                                        <span>Bidhaa itatumwa kwa usafiri</span>
                                    </div>
                                    <div class="step-item d-flex align-items-center mb-3">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">4</div>
                                        <span>Utapata tracking number</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info mb-4">
                            <h5>Uhusiano:</h5>
                            <p class="text-muted">
                                Kama una maswali yoyote kuhusu order yako, tafadhali wasiliana nasi kupitia:
                            </p>
                            <div class="contact-methods">
                                <a href="https://wa.me/255767680463" class="btn btn-success me-2" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                                <a href="tel:+255767680463" class="btn btn-primary me-2">
                                    <i class="fas fa-phone me-2"></i>Piga Simu
                                </a>
                                <a href="mailto:info@pandadigital.co.tz" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-2"></i>Barua Pepe
                                </a>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="<?= app_url('panda-market.php') ?>" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-shopping-bag me-2"></i>Enda kwenye Market
                            </a>
                            <a href="<?= app_url() ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-home me-2"></i>Nyumbani
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sample Order Summary (for demonstration) -->
                <div class="sample-order-summary mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Muhtasari wa Order</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Maelezo ya Mteja:</h6>
                                    <p class="mb-1"><strong>Jina:</strong> John Doe</p>
                                    <p class="mb-1"><strong>Barua Pepe:</strong> john@example.com</p>
                                    <p class="mb-1"><strong>Simu:</strong> +255 123 456 789</p>
                                    <p class="mb-1"><strong>Anwani:</strong> 123 Main St, Dar es Salaam</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Maelezo ya Order:</h6>
                                    <p class="mb-1"><strong>Order ID:</strong> #12345</p>
                                    <p class="mb-1"><strong>Tarehe:</strong> <?php echo date('d/m/Y'); ?></p>
                                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Imekamilika</span></p>
                                    <p class="mb-1"><strong>Njia ya Malipo:</strong> M-Pesa</p>
                                </div>
                            </div>

                            <hr>

                            <h6>Bidhaa:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Bidhaa</th>
                                            <th>Bei</th>
                                            <th>Idadi</th>
                                            <th>Jumla</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Bidhaa ya Mfano 1</td>
                                            <td>Tsh.25,000</td>
                                            <td>1</td>
                                            <td>Tsh.25,000</td>
                                        </tr>
                                        <tr>
                                            <td>Bidhaa ya Mfano 2</td>
                                            <td>Tsh.30,000</td>
                                            <td>1</td>
                                            <td>Tsh.30,000</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Jumla:</strong></td>
                                            <td><strong>Tsh.55,000</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
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
    .success-icon {
        animation: bounceIn 1s ease-in-out;
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }

        50% {
            transform: scale(1.05);
        }

        70% {
            transform: scale(0.9);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .step-item {
        font-size: 14px;
    }

    .step-number {
        flex-shrink: 0;
    }

    .contact-methods .btn {
        margin-bottom: 10px;
    }

    .action-buttons .btn {
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

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #333;
    }
</style>

<script>
    // Show sample order summary after 2 seconds (for demonstration)
    setTimeout(function() {
        document.querySelector('.sample-order-summary').style.display = 'block';
    }, 2000);
</script>