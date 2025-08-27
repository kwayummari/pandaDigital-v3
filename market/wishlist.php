<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// For now, we'll show a placeholder since user authentication isn't implemented
// In a real system, you would check if user is logged in and fetch their wishlist

$pageTitle = "Wishlist - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Wishlist Yangu</h1>
                <p class="page-subtitle">Bidhaa ulizoweka kwenye wishlist</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Wishlist</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Wishlist Content -->
<section class="wishlist-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="wishlist-placeholder text-center">
                    <i class="fas fa-heart fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Wishlist Yako Bado Haina Bidhaa</h3>
                    <p class="text-muted mb-4">
                        Unaweza kuweka bidhaa kwenye wishlist kwa kubofya kitufe cha "Weka kwenye Wishlist"
                        unapotazama bidhaa yoyote.
                    </p>
                    <div class="wishlist-actions">
                        <a href="<?= app_url('panda-market.php') ?>" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-shopping-bag me-2"></i>Enda kwenye Market
                        </a>
                        <a href="<?= app_url('market/search_section.php') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Tafuta Bidhaa
                        </a>
                    </div>
                </div>

                <!-- Sample Wishlist Items (for demonstration) -->
                <div class="sample-wishlist mt-5" style="display: none;">
                    <h4 class="section-title mb-4">Bidhaa za Mfano</h4>
                    <div class="row">
                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 product-card wishlist-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                <div class="thumb position-relative">
                                    <div class="wishlist-badge">
                                        <i class="fas fa-heart text-danger"></i>
                                    </div>
                                    <img src="assets/images/sample-product.jpg" alt="Sample Product"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        Bidhaa ya Mfano
                                    </h6>
                                    <div class="category mb-2">
                                        <small class="text-muted">Kategoria</small>
                                    </div>
                                    <div class="rating mb-2">
                                        <i class="fas fa-star" style="color: #FFD700; font-size: 12px;"></i>
                                        <i class="fas fa-star" style="color: #FFD700; font-size: 12px;"></i>
                                        <i class="fas fa-star" style="color: #FFD700; font-size: 12px;"></i>
                                        <i class="fas fa-star" style="color: #FFD700; font-size: 12px;"></i>
                                        <i class="fas fa-star" style="color: #D3D3D3; font-size: 12px;"></i>
                                        <small class="text-muted ms-1">(4)</small>
                                    </div>
                                    <div class="price mb-2">
                                        <span class="text-primary fw-bold">Tsh.25,000</span>
                                    </div>
                                    <div class="product-actions">
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>Tazama
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm" onclick="removeFromWishlist(1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    .wishlist-placeholder {
        padding: 4rem 0;
    }

    .wishlist-actions .btn {
        padding: 12px 24px;
        font-weight: 600;
    }

    .wishlist-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .wishlist-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
</style>

<script>
    function removeFromWishlist(productId) {
        if (confirm('Una uhakika unataka kuondoa bidhaa hii kutoka kwenye wishlist?')) {
            // In a real system, this would make an AJAX call to remove the item
            alert('Bidhaa imeondolewa kutoka kwenye wishlist!');
            // You could also remove the DOM element here
        }
    }

    // Show sample wishlist after 3 seconds (for demonstration)
    setTimeout(function() {
        document.querySelector('.sample-wishlist').style.display = 'block';
    }, 3000);
</script>