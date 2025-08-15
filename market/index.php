<!DOCTYPE html>
<html lang="en">
<?php
include "connect.php";
include "head.php" ?>

<body>
    <!-- <?php include "preloader.php" ?> -->
    <?php include "header.php" ?>
    <?php include "mainBanner.php" ?>
    <?php include "product.php" ?>
    <?php include "footer.php" ?>
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/accordions.js"></script>
    <script src="assets/js/datepicker.js"></script>
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/lightbox.js"></script>
    <script src="assets/js/isotope.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        $(function() {
            var selectedClass = "";
            $("p").click(function() {
                selectedClass = $(this).attr("data-rel");
                $("#portfolio").fadeTo(50, 0.1);
                $("#portfolio div").not("." + selectedClass).fadeOut();
                setTimeout(function() {
                    $("." + selectedClass).fadeIn();
                    $("#portfolio").fadeTo(50, 1);
                }, 500);

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var selectedRating = 0;
            var selectedProductId = 0;

            // Handle rate product click
            $('.rate-product').click(function() {
                selectedProductId = $(this).data('product-id');
                $('#ratingModal').modal('show');
            });

            // Handle star click inside modal
            $('#modalStars .star').click(function() {
                selectedRating = $(this).data('rating');
                $('#modalStars .star').removeClass('filled');
                for (var i = 1; i <= selectedRating; i++) {
                    $('#modalStars .star[data-rating="' + i + '"]').addClass('filled');
                }
            });

            // Handle rating submission
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
                            alert("Kosa katika kutuma ukadiriaji wako. Tafadhali jaribu tena.");
                        }
                    });
                } else {
                    alert("Tafadhali toa ukadiriaji wako");
                }
            });
        });
    </script>
</body>

</html>