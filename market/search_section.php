<?php
require_once '../config/init.php';
require_once '../config/database.php';

// Initialize database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Get search query from URL
$searchQuery = $_GET['q'] ?? '';
$categoryId = $_GET['category'] ?? '';

// Get categories for filter
$categoriesQuery = "SELECT id, name FROM categories WHERE status = '1' ORDER BY name ASC";
$categoriesStmt = $conn->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

// Build search query
$whereConditions = ["p.status = '1'"];
$params = [];

if (!empty($searchQuery)) {
    $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
}

if (!empty($categoryId)) {
    $whereConditions[] = "p.categoryId = ?";
    $params[] = $categoryId;
}

$whereClause = implode(" AND ", $whereConditions);

// Get products
$productsQuery = "
    SELECT p.*, c.name as category_name,
           IFNULL(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS rating_count
    FROM products p
    LEFT JOIN categories c ON p.categoryId = c.id
    LEFT JOIN ratings r ON p.id = r.productId
    WHERE {$whereClause}
    GROUP BY p.id
    ORDER BY p.id DESC
";

$productsStmt = $conn->prepare($productsQuery);
$productsStmt->execute($params);
$products = $productsStmt->fetchAll();

$pageTitle = !empty($searchQuery) ? "Tafuta: {$searchQuery}" : "Tafuta Bidhaa";

include '../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background-image: url('<?= asset('images/banner/market-banner.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div class="overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-12" data-aos="fade-right">
                <h1 class="page-title" style="color: #fff;">Tafuta Bidhaa</h1>
                <p class="page-subtitle">Pata bidhaa unazotaka kwa haraka na urahisi</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= app_url() ?>">Nyumbani</a></li>
                        <li class="breadcrumb-item"><a href="<?= app_url('panda-market.php') ?>">Panda Market</a></li>
                        <li class="breadcrumb-item active">Tafuta Bidhaa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Search Form -->
<section class="search-form py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="GET" action="" class="search-form-container">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>"
                                class="form-control form-control-lg" placeholder="Tafuta bidhaa..." required>
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select form-select-lg">
                                <option value="">Kategoria zote</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                        <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="search-results py-5">
    <div class="container">
        <?php if (!empty($searchQuery) || !empty($categoryId)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="section-title">
                        <?php if (!empty($searchQuery) && !empty($categoryId)): ?>
                            Matokeo ya "<?php echo htmlspecialchars($searchQuery); ?>" katika <?php echo htmlspecialchars($categories[array_search($categoryId, array_column($categories, 'id'))]['name'] ?? ''); ?>
                        <?php elseif (!empty($searchQuery)): ?>
                            Matokeo ya "<?php echo htmlspecialchars($searchQuery); ?>"
                        <?php elseif (!empty($categoryId)): ?>
                            Bidhaa za <?php echo htmlspecialchars($categories[array_search($categoryId, array_column($categories, 'id'))]['name'] ?? ''); ?>
                        <?php endif; ?>
                        <span class="text-muted">(<?php echo count($products); ?> bidhaa)</span>
                    </h3>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="no-results">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Hakuna matokeo</h4>
                        <p class="text-muted">
                            <?php if (!empty($searchQuery)): ?>
                                Hakuna bidhaa zinazopatikana kwa "<?php echo htmlspecialchars($searchQuery); ?>"
                            <?php else: ?>
                                Hakuna bidhaa katika kategoria hii
                            <?php endif; ?>
                        </p>
                        <a href="<?= app_url('panda-market.php') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Rudi kwenye Market
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 product-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                            <div class="thumb position-relative">
                                <?php if ($product['isOffered'] == 1): ?>
                                    <div class="offer-badge">
                                        <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="single-product.php?id=<?php echo $product['id']; ?>">
                                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <a href="single-product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <h6 class="product-name mb-2" style="color: #333; font-size: 14px; line-height: 1.3;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                </a>
                                <div class="category mb-2">
                                    <small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small>
                                </div>
                                <div class="rating mb-2">
                                    <?php
                                    $avgRating = $product['avg_rating'];
                                    for ($i = 0; $i < 5; $i++):
                                        $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                    ?>
                                        <i class="fas fa-star" style="color: <?php echo $starColor; ?>; font-size: 12px;"></i>
                                    <?php endfor; ?>
                                    <small class="text-muted ms-1">(<?php echo $product['rating_count']; ?>)</small>
                                </div>
                                <?php if ($product['isOffered'] == 1): ?>
                                    <?php
                                    $originalPrice = $product['amount'];
                                    $discount = $product['offer'];
                                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                    ?>
                                    <div class="price mb-2">
                                        <del class="text-muted" style="font-size: 12px;">Tsh.<?php echo number_format($originalPrice, 0); ?></del>
                                        <div class="text-primary fw-bold">Tsh.<?php echo number_format($discountedPrice, 0); ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="price mb-2">
                                        <span class="text-primary fw-bold">Tsh.<?php echo number_format($product['amount'], 0); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <div class="d-flex gap-2">
                                        <a href="single-product.php?id=<?php echo $product['id']; ?>"
                                            class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>Tazama
                                        </a>
                                        <button class="btn btn-outline-warning btn-sm"
                                            onclick="rateProduct(<?php echo $product['id']; ?>)">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

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

<?php include '../includes/footer.php'; ?>

<style>
    .search-form-container {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .no-results {
        padding: 3rem 0;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .offer-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #ffc107;
    }
</style>

<script>
    let selectedRating = 0;
    let selectedProductId = 0;

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

    document.addEventListener('DOMContentLoaded', function() {
        // Handle rating submission
        document.getElementById('submitRating').addEventListener('click', function() {
            if (selectedRating > 0 && selectedProductId > 0) {
                // Submit rating via AJAX
                fetch('rate_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'productId=' + selectedProductId + '&rating=' + selectedRating
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert("Asante kwa ukadiriaji wako!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Kosa katika kutuma ukadiriaji wako. Tafadhali jaribu tena.");
                    });
            } else {
                alert("Tafadhali chagua ukadiriaji.");
            }
        });
    });
</script>