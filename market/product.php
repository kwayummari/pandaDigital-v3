<!-- ***** Most Highly Rated Products Starts ***** -->
<?php include "topRated.php" ?>
<!-- ***** Most Sold Products Starts ***** -->
<?php include "mostSold.php" ?>
<?php include "productsWithCategories.php" ?>

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
    <script>
        $(document).ready(function() {
            var selectedRating = 0;
            var selectedProductId = 0;
            $('.rate-product').click(function() {
                selectedProductId = $(this).data('product-id');
                $('#ratingModal').modal('show');
            });
            $('#modalStars .star').click(function() {
                selectedRating = $(this).data('rating');
                $('#modalStars .star').removeClass('filled');
                for (var i = 1; i <= selectedRating; i++) {
                    $('#modalStars .star[data-rating="' + i + '"]').addClass('filled');
                }
            });
            $('#submitRating').click(function() {
                if (selectedRating > 0 && selectedProductId > 0) {
                    $.ajax({
                        url: 'rate_product.php',
                        type: 'POST',
                        data: {
                            productId: selectedProductId,
                            rating: selectedRating
                        },
                        success: function(response) {
                            alert("Asante kwa ukadiriaji wako!");
                            location.reload();
                        },
                        error: function() {
                            alert("Hitilafu katika kuwasilisha ukadiriaji wako. Tafadhali jaribu tena.");
                        }
                    });
                } else {
                    alert("Tafadhali chagua ukadiriaji.");
                }
            });
        });
    </script>