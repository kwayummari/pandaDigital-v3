<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// For now, we'll show a placeholder since shopping cart functionality isn't fully implemented
// In a real system, you would check if user is logged in and fetch their cart items

$pageTitle = "Cart - Panda Market";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Cart Yangu</h1>
                <p class="page-subtitle">Bidhaa ulizochagua kununua</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section class="cart-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cart-placeholder text-center">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Cart Yako Bado Haina Bidhaa</h3>
                    <p class="text-muted mb-4">
                        Unaweza kuongeza bidhaa kwenye cart kwa kubofya kitufe cha "Ongeza kwenye Cart"
                        unapotazama bidhaa yoyote.
                    </p>
                    <div class="cart-actions">
                        <a href="<?= app_url('panda-market.php') ?>" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-shopping-bag me-2"></i>Enda kwenye Market
                        </a>
                        <a href="<?= app_url('market/search_section.php') ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Tafuta Bidhaa
                        </a>
                    </div>
                </div>

                <!-- Sample Cart Items (for demonstration) -->
                <div class="sample-cart mt-5" style="display: none;">
                    <h4 class="section-title mb-4">Bidhaa za Mfano</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Bidhaa</th>
                                                    <th>Bei</th>
                                                    <th>Idadi</th>
                                                    <th>Jumla</th>
                                                    <th>Vitendo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/sample-product.jpg" alt="Sample Product"
                                                                class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-0">Bidhaa ya Mfano 1</h6>
                                                                <small class="text-muted">Kategoria</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Tsh.25,000</td>
                                                    <td>
                                                        <div class="input-group" style="width: 120px;">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(1, -1)">-</button>
                                                            <input type="number" class="form-control form-control-sm text-center" value="1" min="1" id="qty-1">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(1, 1)">+</button>
                                                        </div>
                                                    </td>
                                                    <td>Tsh.25,000</td>
                                                    <td>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(1)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/sample-product.jpg" alt="Sample Product"
                                                                class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                            <div>
                                                                <h6 class="mb-0">Bidhaa ya Mfano 2</h6>
                                                                <small class="text-muted">Kategoria</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Tsh.30,000</td>
                                                    <td>
                                                        <div class="input-group" style="width: 120px;">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(2, -1)">-</button>
                                                            <input type="number" class="form-control form-control-sm text-center" value="1" min="1" id="qty-2">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(2, 1)">+</button>
                                                        </div>
                                                    </td>
                                                    <td>Tsh.30,000</td>
                                                    <td>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(2)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Cart Summary -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control" placeholder="Msimbo wa punguzo (kama una)">
                                                <button class="btn btn-outline-primary">Tumia</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Muhtasari wa Cart</h6>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span>Jumla ya Bidhaa:</span>
                                                        <span>Tsh.55,000</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span>Punguzo:</span>
                                                        <span class="text-success">-Tsh.0</span>
                                                    </div>
                                                    <hr>
                                                    <div class="d-flex justify-content-between fw-bold">
                                                        <span>Jumla:</span>
                                                        <span>Tsh.55,000</span>
                                                    </div>
                                                    <button class="btn btn-success w-100 mt-3">
                                                        <i class="fas fa-credit-card me-2"></i>Endelea na Malipo
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
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    .cart-placeholder {
        padding: 4rem 0;
    }

    .cart-actions .btn {
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

    .table th {
        border-top: none;
        font-weight: 600;
        color: #333;
    }

    .input-group .btn {
        border-radius: 0;
    }

    .input-group .form-control {
        border-left: 0;
        border-right: 0;
        text-align: center;
    }
</style>

<script>
    function updateQuantity(productId, change) {
        const input = document.getElementById(`qty-${productId}`);
        let currentQty = parseInt(input.value);
        let newQty = currentQty + change;

        if (newQty >= 1) {
            input.value = newQty;
            // In a real system, this would update the cart total and make an AJAX call
            updateCartTotal();
        }
    }

    function removeFromCart(productId) {
        if (confirm('Una uhakika unataka kuondoa bidhaa hii kutoka kwenye cart?')) {
            // In a real system, this would make an AJAX call to remove the item
            alert('Bidhaa imeondolewa kutoka kwenye cart!');
            // You could also remove the DOM element here
        }
    }

    function updateCartTotal() {
        // In a real system, this would calculate the total based on quantities and prices
        console.log('Cart total updated');
    }

    // Show sample cart after 3 seconds (for demonstration)
    setTimeout(function() {
        document.querySelector('.sample-cart').style.display = 'block';
    }, 3000);
</script>