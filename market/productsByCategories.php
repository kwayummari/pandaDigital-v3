<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>

<body>
    <?php include "header.php" ?>
    <?php
    include "connect.php";
    $categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : 0;
    $categoryName = '';
    if ($categoryId > 0) {
        $categoryQuery = $conn->prepare("SELECT name FROM categories WHERE id = ?");
        $categoryQuery->bind_param("i", $categoryId);
        $categoryQuery->execute();
        $categoryQuery->bind_result($categoryName);
        $categoryQuery->fetch();
        $categoryQuery->close();
    }
    $products = [];
    if ($categoryId > 0) {
        $productQuery = $conn->prepare("SELECT id, name, amount, image, isOffered, offer FROM products WHERE categoryId = ? AND status = '1'");
        $productQuery->bind_param("i", $categoryId);
        $productQuery->execute();
        $result = $productQuery->get_result();
        while ($row = $result->fetch_assoc()) {
            $ratingQuery = $conn->prepare("SELECT AVG(rating) as avgRating FROM ratings WHERE productId = ?");
            $ratingQuery->bind_param("i", $row['id']);
            $ratingQuery->execute();
            $ratingResult = $ratingQuery->get_result();
            $ratingRow = $ratingResult->fetch_assoc();
            $avgRating = round($ratingRow['avgRating']);
            $ratingQuery->close();
            $row['avgRating'] = $avgRating;
            $products[] = $row;
        }
        $productQuery->close();
    }
    ?>
    <div class="page-heading" id="top">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="inner-content">
                        <h2><?php echo htmlspecialchars($categoryName); ?> Bidhaa</h2>
                        <span>Bidhaa &amp; Nzuri</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section" id="products">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h2>Bidhaa Zetu za Hivi Punde</h2>
                        <span>Angalia bidhaa zetu zote.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-2 mb-4">
                            <div class="item">
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
                                            <ul style="list-style: none; padding: 0; margin: 0; padding: '10px';">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++):
                                                    $starColor = ($i <= round($product['avgRating'])) ? '#FFD700' : '#D3D3D3';
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
                                                    <del>Tsh.<?php echo $originalPrice; ?>/=</del>
                                                    <br>
                                                    Tsh.<?php echo $discountedPrice; ?>/=
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
                    <div class="col-12">
                        <p>Hakuna bidhaa zilizopatikana katika kategoria hii.</p>
                    </div>
                <?php endif; ?>
            </div>
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

    <!-- Modal for Rating -->
    <div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">Kadiria bidhaa hii</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="stars" style="display: flex;" id="modalStars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <li class="star" style="padding-right: 20px;" data-rating="<?php echo $i; ?>"><i style="font-size: 40px;" class="fa fa-star"></i></li>
                        <?php endfor; ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
                    <button type="button" class="btn btn-primary" id="submitRating">Wasilisha Ukadiriaji</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "footer.php" ?>
    <script>
        document.getElementById('productSearch').addEventListener('keyup', function() {
            var searchQuery = this.value;

            if (searchQuery.length >= 2) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'search_products.php?q=' + encodeURIComponent(searchQuery), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var suggestions = JSON.parse(xhr.responseText);
                        var suggestionBox = document.getElementById('searchSuggestions');
                        suggestionBox.innerHTML = '';

                        suggestions.forEach(function(product) {
                            var suggestionItem = document.createElement('div');
                            suggestionItem.style.padding = '10px';
                            suggestionItem.style.cursor = 'pointer';
                            suggestionItem.style.borderBottom = '1px solid #ccc';
                            suggestionItem.style.display = 'flex';
                            suggestionItem.style.alignItems = 'center';

                            // Create image element
                            var productImage = document.createElement('img');
                            productImage.src = 'assets/images/' + product.image; // Assuming the image path is correctly stored in the database
                            productImage.style.width = '50px';
                            productImage.style.height = '50px';
                            productImage.style.objectFit = 'cover';
                            productImage.style.marginRight = '10px';

                            // Create text container
                            var textContainer = document.createElement('div');

                            textContainer.innerHTML = '<strong>' + product.name + '( Tsh.' + product.amount + ')' + '</strong><br><small>' + product.description + '</small>';

                            // Append image and text container to suggestion item
                            suggestionItem.appendChild(productImage);
                            suggestionItem.appendChild(textContainer);

                            suggestionItem.addEventListener('click', function() {
                                window.location.href = 'single-product.php?id=' + product.id;
                            });

                            suggestionBox.appendChild(suggestionItem);
                        });
                    }
                };
                xhr.send();
            }
        });
    </script>
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.rate-product').click(function () {
                var productId = $(this).data('product-id');
                $('#ratingModal').modal('show');

                $('#submitRating').click(function () {
                    var rating = $('#modalStars .star.selected').data('rating');
                    if (rating) {
                        $.ajax({
                            url: 'submit_rating.php',
                            type: 'POST',
                            data: {
                                productId: productId,
                                rating: rating
                            },
                            success: function (response) {
                                alert('Thank you for your rating!');
                                $('#ratingModal').modal('hide');
                            },
                            error: function () {
                                alert('Hitilafu katika kuwasilisha ukadiriaji. Tafadhali jaribu tena.');
                            }
                        });
                    } else {
                        alert('Tafadhali chagua ukadiriaji kabla ya kuwasilisha.');
                    }
                });

                $('#modalStars .star').click(function () {
                    var rating = $(this).data('rating');
                    $('#modalStars .star').removeClass('selected');
                    $(this).prevAll().addBack().addClass('selected');
                });
            });
        });
    </script>
</body>
</html>
