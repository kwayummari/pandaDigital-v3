<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get product ID from URL
$productId = $_GET['product_id'] ?? null;
$sellerId = $_GET['seller_id'] ?? null;

// Get product information if product ID is provided
$product = null;
if ($productId) {
    $productQuery = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.categoryId = c.id WHERE p.id = ? AND p.status = '1'";
    $productStmt = $conn->prepare($productQuery);
    $productStmt->execute([$productId]);
    $product = $productStmt->fetch();
}

$pageTitle = "Wasiliana na Muuzaji - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Wasiliana na Muuzaji</h1>
                <p class="page-subtitle">Pata maelezo zaidi kuhusu bidhaa na muuzaji</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <?php if ($product): ?>
                            <li class="breadcrumb-item"><a href="productsByCategories.php?categoryId=<?php echo $product['categoryId']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                            <li class="breadcrumb-item"><a href="single-product.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">Wasiliana na Muuzaji</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Contact Seller Content -->
<section class="contact-seller-content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($product): ?>
                    <!-- Product Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="img-fluid rounded" style="width: 100%; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <div class="price-info">
                                        <?php if ($product['isOffered'] == 1): ?>
                                            <?php
                                            $originalPrice = $product['amount'];
                                            $discount = $product['offer'];
                                            $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                            ?>
                                            <span class="text-decoration-line-through text-muted me-2">Tsh.<?php echo number_format($originalPrice, 0); ?></span>
                                            <span class="text-primary fw-bold fs-5">Tsh.<?php echo number_format($discountedPrice, 0); ?></span>
                                            <span class="badge bg-danger ms-2">-<?php echo $discount; ?>%</span>
                                        <?php else: ?>
                                            <span class="text-primary fw-bold fs-5">Tsh.<?php echo number_format($product['amount'], 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Contact Form -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope me-2"></i>
                            Tuma Ujumbe kwa Muuzaji
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Jina Lako</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Barua Pepe</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Namba ya Simu</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label">Mada</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Chagua mada</option>
                                        <option value="Uchunguzi">Uchunguzi kuhusu bidhaa</option>
                                        <option value="Bei">Maswali kuhusu bei</option>
                                        <option value="Usafiri">Maelezo ya usafiri</option>
                                        <option value="Warranty">Warranty na huduma</option>
                                        <option value="Nyingine">Nyingine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Ujumbe</label>
                                <textarea class="form-control" id="message" name="message" rows="5"
                                    placeholder="Andika ujumbe wako hapa..." required></textarea>
                            </div>

                            <?php if ($product): ?>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="includeProduct" name="includeProduct" checked>
                                        <label class="form-check-label" for="includeProduct">
                                            Jumuisha maelezo ya bidhaa kwenye ujumbe
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?= app_url('panda-market.php') ?>" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Rudi Nyuma
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Tuma Ujumbe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alternative Contact Methods -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-phone me-2"></i>
                            Njia Zingine za Uhusiano
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="contact-method text-center">
                                    <div class="contact-icon mb-2">
                                        <i class="fab fa-whatsapp fa-2x text-success"></i>
                                    </div>
                                    <h6>WhatsApp</h6>
                                    <p class="text-muted small">Pata majibu ya haraka</p>
                                    <a href="https://wa.me/255767680463?text=Habari! Ninataka kujua zaidi kuhusu bidhaa<?php echo $product ? ': ' . urlencode($product['name']) : ''; ?>"
                                        class="btn btn-success btn-sm w-100" target="_blank">
                                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="contact-method text-center">
                                    <div class="contact-icon mb-2">
                                        <i class="fas fa-phone fa-2x text-primary"></i>
                                    </div>
                                    <h6>Piga Simu</h6>
                                    <p class="text-muted small">Wasiliana moja kwa moja</p>
                                    <a href="tel:+255767680463" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-phone me-1"></i>Piga Simu
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="contact-method text-center">
                                    <div class="contact-icon mb-2">
                                        <i class="fas fa-envelope fa-2x text-info"></i>
                                    </div>
                                    <h6>Barua Pepe</h6>
                                    <p class="text-muted small">Tuma barua pepe</p>
                                    <a href="mailto:info@pandadigital.co.tz?subject=Uchunguzi kuhusu bidhaa<?php echo $product ? ': ' . urlencode($product['name']) : ''; ?>"
                                        class="btn btn-info btn-sm w-100">
                                        <i class="fas fa-envelope me-1"></i>Barua Pepe
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Saa za Kazi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Jumatatu - Ijumaa:</h6>
                                <p class="text-muted">8:00 AM - 6:00 PM</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Jumamosi:</h6>
                                <p class="text-muted">9:00 AM - 4:00 PM</p>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Jumapili:</strong> Hufungwa (Lakini unaweza kutuma ujumbe kupitia WhatsApp au barua pepe)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .contact-method {
        padding: 20px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .contact-method:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
    }

    .contact-icon {
        margin-bottom: 15px;
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

    .price-info {
        margin-top: 10px;
    }

    .badge {
        font-size: 12px;
        padding: 6px 12px;
    }
</style>

<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);

        // Show success message (in a real system, this would submit to a server)
        alert('Asante! Ujumbe wako umetumwa. Tutakujibu kwa haraka iwezekanavyo.');

        // Reset form
        this.reset();
    });

    // Auto-fill message with product details if checkbox is checked
    document.getElementById('includeProduct')?.addEventListener('change', function() {
        const messageField = document.getElementById('message');
        const currentMessage = messageField.value;

        if (this.checked && currentMessage === '') {
            <?php if ($product): ?>
                messageField.value = `Habari! Ninataka kujua zaidi kuhusu bidhaa hii:\n\nBidhaa: <?php echo addslashes($product['name']); ?>\nKategoria: <?php echo addslashes($product['category_name']); ?>\nBei: Tsh.<?php echo number_format($product['amount'], 0); ?>\n\nTafadhali nisaidie na maelezo zaidi.`;
            <?php endif; ?>
        }
    });
</script>