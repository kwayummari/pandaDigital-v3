<?php
include "connect.php";

// Get the last 4 categories
$query = "SELECT * FROM categories ORDER BY id ASC LIMIT 3";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

$allProducts = [];

foreach ($categories as $category) {
    $categoryId = $category['id'];

    // Get the first 16 products for the current category
    $query = "
        SELECT p.*, IFNULL(AVG(r.rating), 0) AS avg_rating
        FROM products p
        LEFT JOIN ratings r ON p.id = r.productId
        WHERE p.categoryId = $categoryId AND p.status = '1'
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT 12
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    $allProducts[] = [
        'category' => $category,
        'products' => $products
    ];

    mysqli_free_result($result);
}

mysqli_close($conn);
?>

<section class="section" id="men">
    <div class="container-fluid">
        <?php foreach ($allProducts as $categoryProducts): ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h2><?php echo htmlspecialchars($categoryProducts['category']['name']); ?> Karibuni</h2>
                        <span><?php echo $categoryProducts['category']['description']; ?></span>
                    </div>
                </div>
            </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <?php if (!empty($categoryProducts['products'])): ?>
                <?php foreach ($categoryProducts['products'] as $product): ?>
                    <div class="col-6 col-md-2 mb-4 justify-content-center">
                        <div class="item justify-content-center">
                            <div class="card">
                                <div class="thumb position-relative">
                                    <?php if ($product['isOffered'] == 1): ?>
                                        <div class="offer-badge">
                                            <?php echo htmlspecialchars($product['offer']) . '% OFF'; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="single-product.php?id=<?php echo $product['id']; ?>">
                                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                    </a>
                                </div>
                                <div class="down-content p-2">
                                    <a href="single-product.php?id=<?php echo $product['id']; ?>">
                                        <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                                    </a>
                                    <div>
                                        <ul style="list-style: none; padding: 0; margin: 0;">
                                            <?php
                                            $avgRating = $product['avg_rating'];
                                            for ($i = 0; $i < 5; $i++):
                                                $starColor = ($i < round($avgRating)) ? '#FFD700' : '#D3D3D3';
                                            ?>
                                                <li style="display: inline; margin-right: 2px;">
                                                    <i class="fa fa-star" style="color: <?php echo $starColor; ?>;"></i>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                    <?php if ($product['isOffered'] == 1): ?>
                                        <?php
                                        $originalPrice = $product['amount'];
                                        $discount = $product['offer'];
                                        $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                        ?>
                                        <span class="price">
                                            <del>Tsh.<?php echo number_format($originalPrice, 2); ?>/=</del>
                                            <br>
                                            Tsh.<?php echo number_format($discountedPrice, 2); ?>/=
                                        </span>
                                    <?php else: ?>
                                        <span class="price">Tsh.<?php echo $product['amount']; ?>/=</span>
                                    <?php endif; ?>
                                    <div class="hover-content">
                                        <ul class="list-inline" style="margin: 0; padding: 0;">
                                            <li class="list-inline-item">
                                                <a href="single-product.php?id=<?php echo $product['id']; ?>" style="color: black; border: 1px solid black; border-radius: 5%; padding: 5px; display: inline-block;">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0)" class="rate-product" data-product-id="<?php echo $product['id']; ?>" style="color: black; border: 1px solid black; border-radius: 5%; padding: 5px; display: inline-block;">
                                                    <i class="fa fa-star"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Hakuna bidhaa zilizopatikana katika kategoria hii.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
</section>

<style>
    .thumb {
        position: relative;
    }

    .offer-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: red;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .price del {
        color: red;
    }
</style>