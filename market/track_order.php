<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Get order ID from URL
$orderId = $_GET['order_id'] ?? '';
$email = $_GET['email'] ?? '';

$pageTitle = "Track Order - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Track Order</h1>
                <p class="page-subtitle">Fuatilia order yako na ujue status yake</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Track Order</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Track Order Content -->
<section class="track-order-content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Track Order Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Fuatilia Order Yako</h5>
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <input type="text" class="form-control" id="order_id" name="order_id"
                                        value="<?php echo htmlspecialchars($orderId); ?>"
                                        placeholder="Mfano: #12345" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Barua Pepe</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($email); ?>"
                                        placeholder="Barua pepe uliyotumia" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Fuatilia Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (!empty($orderId) && !empty($email)): ?>
                    <!-- Sample Order Tracking Result (for demonstration) -->
                    <div class="tracking-result">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-shipping-fast me-2"></i>
                                    Order #<?php echo htmlspecialchars($orderId); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Order Status Timeline -->
                                <div class="order-timeline">
                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-success">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Order Imepokelewa</h6>
                                            <p class="text-muted mb-0">Order yako imepokelewa na inakusanywa</p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i'); ?></small>
                                        </div>
                                    </div>

                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-success">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Inakusanywa</h6>
                                            <p class="text-muted mb-0">Bidhaa zako zinakusanywa na kuandaliwa</p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime('+2 hours')); ?></small>
                                        </div>
                                    </div>

                                    <div class="timeline-item active">
                                        <div class="timeline-marker bg-primary">
                                            <i class="fas fa-shipping-fast"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Inatumwa</h6>
                                            <p class="text-muted mb-0">Order yako imetumwa kwa usafiri</p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime('+4 hours')); ?></small>
                                        </div>
                                    </div>

                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-light">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Inasafirishwa</h6>
                                            <p class="text-muted mb-0">Bidhaa zako zinakusafirishwa kwako</p>
                                            <small class="text-muted">Inatarajiwa: <?php echo date('d/m/Y', strtotime('+2 days')); ?></small>
                                        </div>
                                    </div>

                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-light">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Imefika</h6>
                                            <p class="text-muted mb-0">Bidhaa zako zimefika kwako</p>
                                            <small class="text-muted">Inatarajiwa: <?php echo date('d/m/Y', strtotime('+3 days')); ?></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Details -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>Maelezo ya Order:</h6>
                                        <p class="mb-1"><strong>Order ID:</strong> #<?php echo htmlspecialchars($orderId); ?></p>
                                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-primary">Inatumwa</span></p>
                                        <p class="mb-1"><strong>Tarehe ya Order:</strong> <?php echo date('d/m/Y'); ?></p>
                                        <p class="mb-1"><strong>Barua Pepe:</strong> <?php echo htmlspecialchars($email); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Maelezo ya Usafiri:</h6>
                                        <p class="mb-1"><strong>Tracking Number:</strong> TRK<?php echo strtoupper(substr(md5($orderId), 0, 8)); ?></p>
                                        <p class="mb-1"><strong>Mtoa Huduma:</strong> Panda Express</p>
                                        <p class="mb-1"><strong>Muda wa Kufika:</strong> 2-3 siku</p>
                                        <p class="mb-1"><strong>Bei ya Usafiri:</strong> Tsh.5,000</p>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="alert alert-info mt-4">
                                    <h6><i class="fas fa-info-circle me-2"></i>Uhusiano:</h6>
                                    <p class="mb-2">Kama una maswali yoyote kuhusu order yako au usafiri, tafadhali wasiliana nasi:</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="https://wa.me/255767680463?text=Habari! Ninataka kujua zaidi kuhusu order #<?php echo urlencode($orderId); ?>"
                                            class="btn btn-success btn-sm" target="_blank">
                                            <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                        </a>
                                        <a href="tel:+255767680463" class="btn btn-primary btn-sm">
                                            <i class="fas fa-phone me-1"></i>Piga Simu
                                        </a>
                                        <a href="mailto:info@pandadigital.co.tz?subject=Uchunguzi kuhusu order #<?php echo urlencode($orderId); ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-envelope me-1"></i>Barua Pepe
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Instructions -->
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Jaza Maelezo ya Order</h5>
                            <p class="text-muted">
                                Ili kufuatilia order yako, tafadhali jaza Order ID na barua pepe uliyotumia
                                unapofanya order. Utapata maelezo kamili ya status ya order yako.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .timeline-item {
        position: relative;
        padding-left: 50px;
        margin-bottom: 30px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #28a745 !important;
    }

    .timeline-item.active .timeline-marker {
        background-color: #007bff !important;
    }

    .timeline-item:not(.completed):not(.active) .timeline-marker {
        background-color: #6c757d !important;
    }

    .timeline-content h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-content p {
        margin-bottom: 5px;
    }

    .timeline-content small {
        font-size: 12px;
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

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .badge {
        font-size: 12px;
        padding: 6px 12px;
    }
</style>