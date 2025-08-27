<?php
require_once '../config/init.php';
require_once '../config/database.php';
require_once '../includes/profile-check.php';

// Debug session variables
error_log("Session variables: " . print_r($_SESSION, true));

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get product ID from URL
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header('Location: ../panda-market.php');
    exit();
}

// Get product information
$productQuery = "
    SELECT p.*, c.name as category_name, c.description as category_description,
           IFNULL(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS rating_count,
           COUNT(s.productId) AS sold_count
    FROM products p
    LEFT JOIN categories c ON p.categoryId = c.id
    LEFT JOIN ratings r ON p.id = r.productId
    LEFT JOIN sales s ON p.id = s.productId
    WHERE p.id = ? AND p.status = '1'
    GROUP BY p.id
";
$productStmt = $conn->prepare($productQuery);
$productStmt->execute([$productId]);
$product = $productStmt->fetch();

if (!$product) {
    header('Location: ../panda-market.php');
    exit();
}

// Get related products from same category
$relatedQuery = "
    SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating
    FROM products p
    LEFT JOIN ratings r ON p.id = r.productId
    WHERE p.categoryId = ? AND p.id != ? AND p.status = '1'
    GROUP BY p.id
    ORDER BY p.id DESC
    LIMIT 4
";
$relatedStmt = $conn->prepare($relatedQuery);
$relatedStmt->execute([$product['categoryId'], $productId]);
$relatedProducts = $relatedStmt->fetchAll();

$pageTitle = $product['name'] . ' - Panda Market';

include '../includes/header.php';
?>



<!-- JavaScript Functions - Load First -->
<script>
    console.log('Script loading...');

    let selectedRating = 0;
    let selectedProductId = <?php echo $product['id']; ?>;

    // Define all functions immediately (before DOMContentLoaded)
    function rateProduct(productId) {
        selectedProductId = productId;
        selectedRating = 0;

        // Reset stars
        document.querySelectorAll('.star').forEach(star => {
            star.classList.remove('filled');
            star.style.color = '#D3D3D3';
        });

        // Show modal
        const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
        ratingModal.show();
    }

    function selectStar(rating) {
        selectedRating = rating;

        // Update star colors
        document.querySelectorAll('.star').forEach((star, index) => {
            if (index < rating) {
                star.classList.add('filled');
                star.style.color = '#FFD700';
            } else {
                star.classList.remove('filled');
                star.style.color = '#D3D3D3';
            }
        });
    }

    function contactSeller(productId) {
        // Check profile completion for contacting seller
        <?php if (isset($_SESSION['userId'])): ?>
            if (!checkProfileCompletion('contact_expert', 'Kuwasiliana na Muuzaji')) {
                console.log('Profile completion required for contacting seller');
                return false;
            }
        <?php endif; ?>

        const contactModal = new bootstrap.Modal(document.getElementById('contactSellerModal'));
        contactModal.show();
    }

    function addToWishlist(productId) {
        // This would typically save to user's wishlist
        alert('Bidhaa imewekwa kwenye wishlist yako!');
    }

    function testFunction() {
        alert('Test button works!');
        console.log('Test function called');
    }

    // Simple quantity functions that work immediately
    function increaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
        updateTotal();
    }

    function decreaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotal();
        }
    }

    function updateTotal() {
        var quantity = parseInt(document.getElementById('quantity').value);
        var unitPrice = <?php echo $product['isOffered'] == 1 ? $discountedPrice : $product['amount']; ?>;
        var total = unitPrice * quantity;

        // Update total display
        const totalDisplay = document.querySelector('.total h5');
        if (totalDisplay) {
            totalDisplay.innerHTML = 'Jumla: Tsh.' + total + '/=';
        }

        // Update modal total if it exists
        if (document.getElementById('total_amount')) {
            document.getElementById('total_amount').value = total;
        }
    }

    function purchaseProduct(productId) {
        console.log('=== PURCHASE PRODUCT FUNCTION CALLED ===');
        console.log('Product ID:', productId);
        console.log('Session check:', <?php echo json_encode(isset($_SESSION['userId']) || isset($_SESSION['user_id']) || isset($_SESSION['id'])); ?>);
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        console.log('Purchase modal element:', document.getElementById('purchaseModal'));

        // Check if user is logged in
        <?php if (isset($_SESSION['userId']) || isset($_SESSION['user_id']) || isset($_SESSION['id'])) : ?>
            console.log('User is logged in, checking profile completion...');

            // Check profile completion for buying products
            <?php if (isset($_SESSION['userId'])): ?>
                if (!checkProfileCompletion('buy_product', 'Kununua Bidhaa')) {
                    console.log('Profile completion required');
                    return false;
                }
            <?php endif; ?>

            console.log('Profile complete, showing purchase modal');
            // Show purchase modal
            const purchaseModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
            purchaseModal.show();
        <?php else : ?>
            console.log('User is not logged in, showing login modal');
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        <?php endif; ?>
    }

    // Function to update modal total when quantity changes
    function updateModalTotal() {
        var modalQuantity = parseInt(document.getElementById('custom_amount').value);
        var unitPrice = <?php echo $product['isOffered'] == 1 ? $discountedPrice : $product['amount']; ?>;
        var total = unitPrice * modalQuantity;
        document.getElementById('total_amount').value = total;
    }

    // Make functions globally available immediately
    window.testFunction = testFunction;
    window.purchaseProduct = purchaseProduct;
    window.rateProduct = rateProduct;
    window.selectStar = selectStar;
    window.contactSeller = contactSeller;
    window.addToWishlist = addToWishlist;
    window.increaseQuantity = increaseQuantity;
    window.decreaseQuantity = decreaseQuantity;
    window.updateTotal = updateTotal;
    window.updateModalTotal = updateModalTotal;
</script>



<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/new-banner2.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; min-height: 400px; padding: 120px 0;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="page-subtitle"><?php echo htmlspecialchars($product['category_name']); ?> - Bidhaa bora na bei nafuu</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item"><a href="productsByCategories.php?categoryId=<?php echo $product['categoryId']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Product Details -->
<section class="product-details py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="product-image-container">
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="img-fluid rounded" style="width: 100%; max-height: 500px; object-fit: cover;">
                    <?php if ($product['isOffered'] == 1): ?>
                        <div class="offer-badge-large">
                            <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="product-info">
                    <h1 class="product-title mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>

                    <!-- Rating -->
                    <div class="product-rating mb-3">
                        <div class="stars d-inline-block me-2">
                            <?php
                            $avgRating = (float)$product['avg_rating'];
                            for ($i = 0; $i < 5; $i++):
                                $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                            ?>
                                <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 18px;"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">
                            <?php echo number_format($avgRating, 1); ?> (<?php echo $product['rating_count']; ?> ukadiriaji)
                        </span>
                        <button class="btn btn-outline-warning btn-sm ms-3" onclick="rateProduct(<?php echo $product['id']; ?>)">
                            <i class="fas fa-star me-1"></i>Kadiria
                        </button>
                    </div>

                    <!-- Price -->
                    <div class="product-price mb-4">
                        <?php if ($product['isOffered'] == 1): ?>
                            <?php
                            $originalPrice = (float)$product['amount'];
                            $discount = (float)$product['offer'];
                            $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                            ?>
                            <div class="original-price text-muted text-decoration-line-through">
                                Tsh.<?php echo $originalPrice; ?>/=
                            </div>
                            <div class="current-price text-primary fw-bold fs-3">
                                Tsh.<?php echo $discountedPrice; ?>/=
                            </div>
                            <div class="discount-badge bg-danger text-white px-2 py-1 rounded d-inline-block">
                                -<?php echo $discount; ?>%
                            </div>
                        <?php else: ?>
                            <div class="current-price text-primary fw-bold fs-3">
                                Tsh.<?php echo $product['amount']; ?>/=
                            </div>
                        <?php endif; ?>

                        <!-- Quantity Selector -->
                        <div class="quantity-content mb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6>Weka Idadi:</h6>
                                </div>
                                <div class="col-6">
                                    <div class="quantity buttons_added">
                                        <input type="button" value="-" class="minus btn btn-outline-secondary btn-sm" onclick="decreaseQuantity();">
                                        <input type="number" step="1" min="1" max="" name="quantity" value="1" title="Qty" class="input-text qty text form-control" size="4" pattern="" inputmode="" id="quantity" onchange="updateTotal();">
                                        <input type="button" value="+" class="plus btn btn-outline-secondary btn-sm" onclick="increaseQuantity();">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Price -->
                        <div class="total mb-3">
                            <h5>Jumla:
                                <?php if ($product['isOffered'] == 1): ?>
                                    Tsh.<?php echo $discountedPrice; ?>/=
                                <?php else: ?>
                                    Tsh.<?php echo $product['amount']; ?>/=
                                <?php endif; ?>
                            </h5>
                        </div>

                        <!-- Buy Button -->
                        <div class="buy-button-section mt-3">
                            <button type="button" class="btn btn-success btn-lg w-100" id="purchase-button" onclick="purchaseProduct(<?php echo $product['id']; ?>);">
                                <i class="fas fa-shopping-cart me-2"></i>Nunua Bidhaa
                            </button>

                        </div>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($product['description'])): ?>
                        <div class="product-description mb-4">
                            <h5>Maelezo</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Details -->
                    <div class="product-details-list mb-4">
                        <h5>Maelezo ya Bidhaa</h5>
                        <ul class="list-unstyled">
                            <li><strong>Kategoria:</strong> <?php echo htmlspecialchars($product['category_name']); ?></li>
                            <li><strong>Bidhaa ID:</strong> #<?php echo $product['id']; ?></li>
                            <?php if ($product['sold_count'] > 0): ?>
                                <li><strong>Imeuzwa:</strong> <?php echo $product['sold_count']; ?> mara</li>
                            <?php endif; ?>
                            <li><strong>Bei:</strong>
                                <?php if ($product['isOffered'] == 1): ?>
                                    <span class="text-decoration-line-through text-muted">Tsh.<?php echo $originalPrice; ?>/=</span>
                                    <span class="text-primary fw-bold">Tsh.<?php echo $discountedPrice; ?>/=</span>
                                <?php else: ?>
                                    <span class="text-primary fw-bold">Tsh.<?php echo $product['amount']; ?>/=</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-lg w-100" onclick="contactSeller(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-phone me-2"></i>Wasiliana na Muuzaji
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-success btn-lg w-100" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-heart me-2"></i>Weka kwenye Wishlist
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
    <section class="related-products py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="section-title mb-4">Bidhaa Zinazohusiana</h3>
                </div>
            </div>
            <div class="row">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <div class="thumb position-relative">
                                <?php if ($relatedProduct['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <?php echo htmlspecialchars($relatedProduct['offer']) . '% OFF'; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="single-product.php?id=<?php echo $relatedProduct['id']; ?>">
                                    <img src="assets/images/<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                                        alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <a href="single-product.php?id=<?php echo $relatedProduct['id']; ?>" class="text-decoration-none">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        <?php echo htmlspecialchars($relatedProduct['name']); ?>
                                    </h6>
                                </a>
                                <div class="rating mb-2">
                                    <?php
                                    $avgRating = (float)$relatedProduct['avg_rating'];
                                    for ($i = 0; $i < 5; $i++):
                                        $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                    ?>
                                        <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($relatedProduct['isOffered'] == 1): ?>
                                    <?php
                                    $relatedOriginalPrice = (float)$relatedProduct['amount'];
                                    $relatedDiscount = (float)$relatedProduct['offer'];
                                    $relatedDiscountedPrice = $relatedOriginalPrice - ($relatedOriginalPrice * ($relatedDiscount / 100));
                                    ?>
                                    <div class="price mb-2">
                                        <del class="text-muted" style="font-size: 12px;">Tsh.<?php echo $relatedOriginalPrice; ?>/=</del>
                                        <div class="text-primary fw-bold">Tsh.<?php echo $relatedDiscountedPrice; ?>/=</div>
                                    </div>
                                <?php else: ?>
                                    <div class="price mb-2">
                                        <span class="text-primary fw-bold">Tsh.<?php echo $relatedProduct['amount']; ?>/=</span>
                                    </div>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <a href="single-product.php?id=<?php echo $relatedProduct['id']; ?>"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>Tazama
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Kadiria bidhaa hii</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="stars mb-3" id="modalStars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <i class="fas fa-star star" style="font-size: 40px; color: #D3D3D3; cursor: pointer; margin: 0 10px;"
                                data-rating="<?php echo $i; ?>" onclick="selectStar(<?php echo $i; ?>)"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-muted">Chagua ukadiriaji wako</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                <button type="button" class="btn btn-primary" id="submitRating">Wasilisha Ukadiriaji</button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Seller Modal -->
<div class="modal fade" id="contactSellerModal" tabindex="-1" role="dialog" aria-labelledby="contactSellerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactSellerModalLabel">Wasiliana na Muuzaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Ili kuwasiliana na muuzaji wa bidhaa hii, tafadhali tuma ujumbe kupitia Panda Chat au piga simu.
                </div>
                <div class="contact-options">
                    <a href="https://wa.me/255767680463?text=Habari! Ninataka kujua zaidi kuhusu bidhaa: <?php echo urlencode($product['name']); ?>"
                        class="btn btn-success w-100 mb-2" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                    <a href="tel:+255767680463" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-phone me-2"></i>Piga Simu
                    </a>
                    <a href="mailto:info@pandadigital.co.tz?subject=Uchunguzi kuhusu bidhaa: <?php echo urlencode($product['name']); ?>"
                        class="btn btn-outline-primary w-100">
                        <i class="fas fa-envelope me-2"></i>Barua Pepe
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Kamilisha Ununuzi Wako</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="process_order.php">
                    <div class="mb-3">Kamilisha agizo lako la <?php echo htmlspecialchars($product['name']); ?></div>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <input type="hidden" name="currency" value="TZS">
                    <input type="hidden" name="payment_options" value="mobilemoney">
                    <input type="hidden" name="redirect_url" value="<?= app_url('market/single-product.php?id=' . $product['id']) ?>">
                    <input type="hidden" name="customer[email]" value="<?php echo isset($_SESSION['userEmail']) ? $_SESSION['userEmail'] : (isset($_SESSION['email']) ? $_SESSION['email'] : ''); ?>">
                    <input type="hidden" name="customer[name]" value="<?php echo isset($_SESSION['userFullName']) ? $_SESSION['userFullName'] : (isset($_SESSION['fullName']) ? $_SESSION['fullName'] : ''); ?>">
                    <input type="hidden" name="customization[title]" value="My store">
                    <input type="hidden" name="customization[description]" value="Payment for items in cart">

                    <div class="form-group mb-3">
                        <label for="total_amount">Bei:</label>
                        <input readonly id="total_amount" name="price" class="form-control" value="<?php echo $product['isOffered'] == 1 ? $discountedPrice : $product['amount']; ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label for="custom_amount">Weka Idadi:</label>
                        <input type="number" id="custom_amount" name="quantity" class="form-control" min="1" value="1" onchange="updateModalTotal()">
                    </div>

                    <div class="form-group mb-3">
                        <label for="mobile_type">Chagua M-Pesa:</label>
                        <select id="mobile_type" name="mobile_type" class="form-control">
                            <option value="Tigo">Tigo</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Halopesa">Halopesa</option>
                            <option value="Azampesa">Azampesa</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone">Namba ya simu:</label>
                        <input type="text" id="phone" name="phone" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                        <button type="submit" class="btn btn-primary">Nunua Bidhaa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .product-image-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .offer-badge-large {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
    }

    .product-title {
        color: #333;
        font-weight: 700;
        font-size: 2.5rem;
        line-height: 1.2;
    }

    .product-rating {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .rating-text {
        color: #6c757d;
        font-size: 14px;
    }

    .original-price {
        font-size: 1.2rem;
    }

    .current-price {
        font-size: 2.5rem;
    }

    .discount-badge {
        font-weight: bold;
        font-size: 14px;
    }

    .product-description h5,
    .product-details-list h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .product-details-list ul li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .product-details-list ul li:last-child {
        border-bottom: none;
    }

    .product-actions .btn {
        padding: 12px 24px;
        font-weight: 600;
    }

    .related-products .product-card:hover {
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

    .contact-options .btn {
        padding: 12px 24px;
        font-weight: 600;
    }

    .quantity-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .quantity.buttons_added {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .quantity.buttons_added input[type="number"] {
        width: 60px;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 5px;
    }

    .quantity.buttons_added .btn {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        cursor: pointer;
        z-index: 10;
        position: relative;
    }

    .total h5 {
        color: #28a745;
        font-weight: 700;
        margin: 0;
    }
</style>