<!DOCTYPE html>
<html lang="en">
<?php
include "head.php"; ?>

<body>
    <?php include "header.php"; ?>
    <?php
    session_start();
    include "connect.php";
    $is_logged_in = isset($_SESSION['userId']);
    $full_name = $_SESSION['userFullName'];
    $emailData = $_SESSION['userEmail'];
    $user_id = $_SESSION['userId'];
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($product_id > 0) {
        $product_query = "SELECT p.*, AVG(r.rating) AS avgRating FROM products p LEFT JOIN ratings r ON p.id = r.productId WHERE p.id = ? AND p.status = '1'";
        $product_stmt = mysqli_prepare($conn, $product_query);
        mysqli_stmt_bind_param($product_stmt, "i", $product_id);
        mysqli_stmt_execute($product_stmt);
        $product_result = mysqli_stmt_get_result($product_stmt);
        if ($product_result && mysqli_num_rows($product_result) > 0) {
            $product = mysqli_fetch_assoc($product_result);
            $seller_id = $product['sellerId'];
            $profile_query = "SELECT * FROM profile WHERE userId = $seller_id";
            $profile_result = mysqli_query($conn, $profile_query);
            if ($profile_result && mysqli_num_rows($profile_result) > 0) {
                $seller_profile = mysqli_fetch_assoc($profile_result);
            }
            $gallery_query = "SELECT * FROM gallery WHERE productId = $product_id";
            $gallery_result = mysqli_query($conn, $gallery_query);
            $gallery_images = [];
            if ($gallery_result) {
                while ($gallery_row = mysqli_fetch_assoc($gallery_result)) {
                    $gallery_images[] = $gallery_row;
                }
            }
            mysqli_free_result($gallery_result);

            $avgRating = $product['avgRating'];
        } else {
            echo "Product not found.";
        }

        mysqli_stmt_close($product_stmt);
        mysqli_free_result($product_result);
    } else {
        echo "Invalid product ID.";
    }
    mysqli_close($conn);
    ?>
    <div class="page-heading" id="top">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="inner-content">
                        <h2><?php echo $product['name']; ?></h2>
                        <span><?php echo htmlspecialchars($product['caption']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section" id="product">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="left-images">
                        <div class="owl-carousel owl-theme">
                            <?php if (!empty($gallery_images)) : ?>
                                <?php foreach ($gallery_images as $image) : ?>
                                    <div class="item">
                                        <img class="col-lg-8" src="assets/images/<?php echo htmlspecialchars($image['image']); ?>" alt="Gallery">
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="item">
                                    <img src="assets/images/default-product.jpg" alt="Default Image">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="right-content">
                        <?php if (isset($product)) : ?>
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
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
                            <br>
                            <ul class="stars" data-product-id="<?php echo $product['id']; ?>">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <li class="star <?php echo ($i <= $product['avgRating']) ? 'filled' : ''; ?>" data-rating="<?php echo $i; ?>"><i class="fa fa-star"></i></li>
                                <?php endfor; ?>
                            </ul>
                            <div class="quote">
                                <i class="fa fa-quote-left"></i>
                                <p><?php echo $product['caption']; ?></p>
                            </div>
                            <div class="description">
                                <span id="short-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</span>
                                <span id="full-description" style="display: none;"><?php echo nl2br($product['description']); ?></span>
                                <a href="javascript:void(0);" id="read-more">Soma Zaidi</a>
                            </div>
                            <div class="quantity-content">
                                <div class="left-content">
                                    <h6>Nambari ya Maagizo</h6>
                                </div>
                                <div class="right-content">
                                    <div class="quantity buttons_added">
                                        <input type="button" value="-" class="minus">
                                        <input type="number" step="1" min="1" max="" name="quantity" value="1" title="Qty" class="input-text qty text" size="4" pattern="" inputmode="" id="quantity">
                                        <input type="button" value="+" class="plus">
                                    </div>
                                </div>
                            </div>
                            <div class="total">
                                <h4>Total: Tsh.</h4>

                                <?php if ($product['isOffered'] == 1): ?>
                                    <?php
                                    $originalPrice = $product['amount'];
                                    $discount = $product['offer'];
                                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                                    ?>
                                    <h4 class="price" id="total">
                                        <del>Tsh.<?php echo $originalPrice; ?>/=</del>
                                        <br>
                                        Tsh.<?php echo $discountedPrice; ?>/=
                                    </h4>
                                <?php else: ?>
                                    <h4 class="price" id="total">Tsh.<?php echo $product['amount']; ?>/=</h4>
                                <?php endif; ?>
                                <div class="main-border-button">
                                    <a href="#" id="purchase-button">Nunua Bidhaa</a>
                                </div>
                            </div>
                            <hr>
                            <button type="button" class="btn btn-success col-lg-12"><i class="bi bi-telephone-fill"></i> Fanya mawasiliano</button>
                            <hr>
                            <button type="button" class="btn btn-outline-success col-lg-12" id="start-chat-button"><i class="bi bi-chat-dots-fill"></i> Anzisha gumzo</button>
                            <hr>
                            <button type="button" class="btn btn-outline-success col-lg-4" data-toggle="modal" data-target="#unavailableModal">
                                Haipatikani
                            </button>
                            <button type="button" class="btn btn-outline-success col-lg-6" data-toggle="modal" data-target="#reportAbuseModal">
                                <i style="color:red;" class="bi bi-flag-fill"></i> Ripoti Matumizi Mabaya
                            </button>
                            <hr>
                    </div>
                </div>
                <div class="col-lg-8">
                    <br>
                    <?php if (isset($seller_profile)) : ?>
                        <div class="seller-details">
                            <h5>Seller Information</h5>
                            <img src="assets/images/<?php echo htmlspecialchars($seller_profile['image']); ?>" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            <p><strong>Jina:</strong> <?php echo htmlspecialchars($seller_profile['name']); ?></p>
                            <p><strong>Maelezo:</strong> <?php echo htmlspecialchars($seller_profile['description']); ?></p>
                            <p><strong>Wasiliana nasi:</strong> <?php echo htmlspecialchars($seller_profile['email']); ?> <br> +255<?php echo htmlspecialchars($seller_profile['phone']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p>Maelezo ya bidhaa hayajapatikana.</p>
                <?php endif; ?>
                <hr>
                <button type="button" class="btn btn-success col-lg-12"><i class="bi bi-telephone-fill"></i> Fanya mawasiliano</button>
                </div>
                <div class="col-lg-4">
                    <h5>Vidokezo vya usalama</h5>
                    <ol>
                        <li>1. Epuka kulipa mapema, hata kwa utoaji</li>
                        <li>2. Kutana na muuzaji mahali salama pa umma</li>
                        <li>3. Kagua kipengee na uhakikishe ndicho unachotaka</li>
                        <li>4. Hakikisha kuwa kipengee kilichopakiwa ndicho ambacho umekikagua</li>
                        <li>5. Lipa tu ikiwa umeridhika</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <?php include "footer.php" ?>
    <!-- Chat Drawer -->
    <div id="chatDrawer" class="drawer">
        <div class="drawer-header">
            <h4>Chati</h4>
            <button id="closeDrawer">Close</button>
        </div>
        <div class="drawer-body" id="chatMessages">
            <!-- Chat messages will be appended here -->
        </div>
        <div class="drawer-footer">
            <input type="text" id="chatInput" placeholder="Type your message...">
            <button id="sendMessage">Tuma</button>
        </div>
    </div>
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

    <!-- Unavailable Modal -->
    <div class="modal fade" id="unavailableModal" tabindex="-1" role="dialog" aria-labelledby="unavailableModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unavailableModalLabel">Hali ya Bidhaa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3>Je, haipatikani tena? 😢</h3>
                    <form id="unavailableForm" method="POST" action="update_status.php">
                        <input type="hidden" name="productId" value="<?php echo $product_id; ?>">
                        <input type="hidden" id="status" name="status">
                        <div class="text-center">
                            <button type="button" class="btn btn-primary" id="confirmUnavailable">Thibitisha</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Ghairi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Abuse Modal -->
    <div class="modal fade" id="reportAbuseModal" tabindex="-1" role="dialog" aria-labelledby="reportAbuseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportAbuseModalLabel"> Ripoti Unyanyasaji</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reportAbuseForm" method="POST" action="report_abuse.php">
                        <div class="form-group">
                            <label for="abuseReason">Sababu:</label>
                            <select id="abuseReason" name="reason" class="form-control" required>
                                <option value="This is illegal/fraudulent">Hii ni kinyume cha sheria/udanganyifu</option>
                                <option value="This ad is spam">Tangazo hili ni taka</option>
                                <option value="The price is wrong">Bei si sahihi</option>
                                <option value="Wrong category">Kategoria isiyo sahihi</option>
                                <option value="Seller asked for prepayment">Muuzaji aliuliza malipo ya mapema</option>
                                <option value="It is sold">Inauzwa</option>
                                <option value="User is unreachable">Mtumiaji hapatikani</option>
                                <option value="Other">Nyingine</option>
                            </select>
                        </div>
                        <input name="productId" hidden value="<?php echo $product_id ?>" />
                        <div class="form-group">
                            <label for="description">Maelezo:</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
                            <button type="submit" class="btn btn-primary">Wasilisha</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include 'process_order.php';
    }
    ?>
    <div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="purchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseModalLabel">Kamilisha Ununuzi Wako</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div>Kamilisha agizo lako la <?php echo $product['name']; ?></div>
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                        <input type="hidden" name="currency" value="TZS">
                        <input type="hidden" name="payment_options" value="mobilemoney">
                        <input type="hidden" name="redirect_url" value="http://localhost/product.php?id=<?php echo $product_id; ?>">
                        <input type="hidden" name="customer[email]" value="<?php echo $emailData; ?>">
                        <input type="hidden" name="customer[name]" value="<?php echo $full_name; ?>">
                        <input type="hidden" name="customization[title]" value="My store">
                        <input type="hidden" name="customization[description]" value="Payment for items in cart">
                        <div class="form-group" id="custom_amount_field">
                            <label for="custom_amount">Bei:</label>
                            <input readonly id="total_amount" name="price" class="form-control" value="<?php echo htmlspecialchars($product['amount']); ?>">
                        </div>
                        <div class="form-group" id="custom_amount_field">
                            <label for="quantity">Weka Idadi:</label>
                            <input type="number" id="custom_amount" name="quantity" class="form-control" min="1">
                        </div>
                        <select value="mpesa" id="mobile_type" name="mobile_type" class="form-control">
                            <!-- <option value="mpesa">MPESA</option> -->
                            <option value="Tigo">Tigo</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Halopesa">Halopesa</option>
                            <option value="Azampesa">Azampesa</option>
                        </select>
                        <div class="form-group">
                            <label for="phone">Namba ya simu:</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
                            <button type="submit" class="btn btn-primary">Nunua Bidhaa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login/Register</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php include "login_form.php" ?>
                    <?php include "registering_form.php" ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('reportAbuseForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);

            fetch('report_abuse.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Report submitted successfully.');
                        $('#reportAbuseModal').modal('hide'); // Close the modal
                        // Optionally, you can clear the form fields here
                        document.getElementById('reportAbuseForm').reset();
                    } else {
                        alert('Failed to submit report.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <script>
        document.getElementById('confirmUnavailable').addEventListener('click', function() {
            // Set status to 1 for unavailable
            document.getElementById('status').value = 1;

            const form = document.getElementById('unavailableForm');
            const formData = new FormData(form);

            fetch('update_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product status updated to unavailable.');
                        $('#unavailableModal').modal('hide'); // Close the modal
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        document.querySelector('[data-dismiss="modal"]').addEventListener('click', function() {
            // Set status to 2 for cancelled
            document.getElementById('status').value = 2;
        });
    </script>


    <script>
        document.getElementById('start-chat-button').addEventListener('click', function() {
            const userId = "<?php echo $user_id; ?>";
            const productId = "<?php echo $product_id; ?>";

            // Open chat drawer
            document.getElementById('chatDrawer').style.display = 'block';

            // Check or create chat session
            fetch('check_or_create_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId,
                        productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const chatId = data.chatId;
                        const chatMessagesElement = document.querySelector('#chatMessages');
                        chatMessagesElement.setAttribute('data-chat-id', chatId);
                        loadChatMessages(data.chatId);
                    } else {
                        alert('Failed to start chat');
                    }
                });
        });

        function loadChatMessages(chatId) {
            fetch('load_chat_messages.php?chatId=' + chatId)
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        const chatMessages = document.getElementById('chatMessages');
                        chatMessages.innerHTML = ''; // Clear existing messages

                        data.messages.forEach(message => {
                            const messageElement = document.createElement('div');
                            messageElement.classList.add('chat-message');

                            if (message.type === '1') {
                                messageElement.classList.add('customer-message');
                            } else if (message.type === '2') {
                                messageElement.classList.add('vendor-message');
                            }

                            messageElement.textContent = message.content;
                            chatMessages.appendChild(messageElement);
                        });

                        // Scroll to the latest message
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        console.error('No messages found in the response.');
                    }
                })
                .catch(error => console.error('Error loading chat messages:', error));
        }


        document.getElementById('sendMessage').addEventListener('click', function() {
            const chatInput = document.getElementById('chatInput');
            const messageContent = chatInput.value;
            const chatMessagesElement = document.querySelector('#chatMessages');

            const chatId = chatMessagesElement?.getAttribute('data-chat-id');

            if (messageContent.trim() !== '' && chatId) {
                fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            chatId,
                            messageContent
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('sent data:', chatId,
                            messageContent);
                        if (data.success) {
                            loadChatMessages(chatId);
                            chatInput.value = '';
                        } else {
                            alert('Failed to send message');
                        }
                    });
            } else {
                console.error('Message content is empty or chatId is missing.');
            }
        });


        document.getElementById('closeDrawer').addEventListener('click', function() {
            document.getElementById('chatDrawer').style.display = 'none';
        });
    </script>
    <!-- Styles -->
    <style>
        #loginModal {
            z-index: 1060 !important;
        }

        .drawer {
            display: none;
            position: fixed;
            right: 0;
            top: 0;
            width: 400px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
            padding: 10px;
        }

        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-body {
            padding: 10px;
            max-height: 90%;
            overflow-y: auto;
        }

        .drawer-footer {
            display: flex;
            align-items: center;
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .drawer-footer input {
            flex: 1;
            padding: 5px;
            margin-right: 5px;
        }

        .drawer-footer button {
            flex: 0 0 20%;
            padding: 5px 10px;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 15px;
            max-width: 60%;
            width: fit-content;
            word-wrap: break-word;
            color: #333;
        }

        .vendor-message {
            background-color: #d1e7dd;
            align-self: flex-start;
            text-align: left;
            margin-left: 0;
            margin-right: auto;
        }

        .customer-message {
            background-color: #efefef;
            align-self: flex-end;
            text-align: right;
            margin-left: auto;
            margin-right: 0;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }
    </style>
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
                            var productImage = document.createElement('img');
                            productImage.src = 'assets/images/' + product.image;
                            productImage.style.width = '50px';
                            productImage.style.height = '50px';
                            productImage.style.objectFit = 'cover';
                            productImage.style.marginRight = '10px';
                            var textContainer = document.createElement('div');
                            textContainer.innerHTML = '<strong>' + product.name + '( Tsh.' + product.amount + ')' + '</strong><br><small>' + product.description + '</small>';
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
        function updateAmountField() {
            var amountSelection = document.getElementById("amount_selection");
            var customAmountField = document.getElementById("custom_amount_field");
            var totalAmountField = document.getElementById("total_amount");

            if (amountSelection.value === "custom") {
                customAmountField.style.display = "block";
                totalAmountField.value = "";
            } else {
                customAmountField.style.display = "none";
                totalAmountField.value = amountSelection.value;
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                items: 1,
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                nav: true,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#read-more').click(function() {
                $('#short-description').toggle();
                $('#full-description').toggle();
                $(this).text($(this).text() === 'Read More' ? 'Read Less' : 'Read More');
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#purchase-button').click(function(e) {
                e.preventDefault();
                <?php if ($is_logged_in) : ?>
                    $('#purchaseModal').modal('show');
                <?php else : ?>
                    $('#loginModal').modal('show');
                <?php endif; ?>
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#quantity').change(function() {
                var quantity = $(this).val();
                var unitPrice = <?php echo $product['amount']; ?>;
                var total = unitPrice * quantity;
                $('#total').text('Tsh.' + total + '/=');
            });
        });
    </script>
    <script>
        document.getElementById('show-register-form').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });
    </script>
    <script>
        document.getElementById('show-login-form').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
        });
    </script>
</body>

</html>