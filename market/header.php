<?php
include "connect.php";
session_start();
$isSeller = isset($_SESSION['isSeller']) ? $_SESSION['isSeller'] : false;
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;
$query = "SELECT * FROM categories";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$salesQuery = "
    SELECT 
        sales.id, 
        sales.date, 
        sales.amount, 
        sales.status AS saleStatus,
        products.name AS productName,
        CONCAT(users.first_name, ' ', users.last_name) AS sellerName,
        users.phone AS sellerPhone
    FROM sales
    JOIN products ON sales.productId = products.id
    JOIN users ON products.sellerId = users.id
    WHERE sales.buyersId = ? AND products.status = 1;
";
$salesStmt = $conn->prepare($salesQuery);
$salesStmt->bind_param("i", $userId);
$salesStmt->execute();
$salesResult = $salesStmt->get_result();
?>
<header class="header-area header-sticky">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-2">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <img height="150px" src="assets/images/market1.png">
                </a>
            </div>

            <!-- Search bar -->
            <div class="col-4">
                <form action="search.php" method="GET" class="search-bar">
                    <input id="productSearch" type="text" name="query" placeholder="Tafuta bidhaa...">
                    <div id="searchSuggestions" style="width: 100%; background-color: #fff; z-index: 1000;"></div>
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <!-- Navigation -->
            <div class="col-6">
                <nav class="main-nav">
                    <ul class="nav">
                        <!-- Home -->
                        <li class="scroll-to-section">
                            <a href="index.php" class="active">
                                <i class="fa fa-home"></i> Nyumbani <!-- Home -->
                            </a>
                        </li>

                        <!-- User Profile / Login -->
                        <li class="scroll-to-section desktopView">
                            <div class="user-dropdown">
                                <a href="#" id="userIcon" class="dropdownIcon">
                                    <i class="fa fa-user"></i>
                                    <?php echo isset($_SESSION['userFullName']) ? $_SESSION['userFullName'] : 'Ingia / Jisajili'; ?><!-- Login/Register -->
                                </a>

                                <!-- Dropdown Menu -->
                                <div class="dropdown-menu">
                                    <?php if (isset($_SESSION['userId'])): ?>
                                        <a href="#"><i class="fa fa-user"></i> Wasifu</a><!-- Profile -->
                                        <a href="#" id="trackPurchasesBtn"><i class="fa fa-truck"></i> Kufuatilia maagizo</a><!-- Track Orders -->
                                        <a href="#" id="wishlistBtn"><i class="fa fa-heart"></i> Orodha ya matamanio</a><!-- WishList -->
                                        <a href="#" id="rewardsBtn"><i class="fa fa-star"></i> Zawadi</a><!-- Rewards -->
                                        <a href="#" id="giftCardsBtn"><i class="fa fa-gift"></i> Kadi za Zawadi</a><!-- Gift Cards -->
                                        <a href="#" id="becomeSeller1"><i class="fa fa-store"></i> Kua muuzaji</a>
                                        <a href="#" id="logoutBtn1"><i class="fa fa-sign-out"></i> Jitoe</a>
                                    <?php else: ?>
                                        <a href="#" id="loginBtn"><i class="fa fa-sign-in"></i> Ingia</a> <!-- Login -->
                                        <a href="#" id="registerBtn"><i class="fa fa-user-plus"></i> Jisajili</a> <!-- Register -->
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>

                        <li class="scroll-to-section mobileView">
                            <div class="user-dropdown">

                                <?php if (isset($_SESSION['userId'])): ?>
                                    <a href="#"><i class="fa fa-user"></i> Wasifu</a><!-- Profile -->
                                    <a href="#" id="trackPurchasesBtn1"><i class="fa fa-truck"></i> Maagizo</a><!-- Track Orders -->
                                    <a href="#" id="wishlistBtn1"><i class="fa fa-heart"></i> Matamanio</a><!-- WishList -->
                                    <a href="#" id="rewardsBtn1"><i class="fa fa-star"></i> Zawadi</a><!-- Rewards -->
                                    <a href="#" id="giftCardsBtn1"><i class="fa fa-gift"></i> Kadi za Zawadi</a><!-- Gift Cards -->
                                    <a href="#" id="becomeSeller1"><i class="fa fa-store"></i> Kua muuzaji</a>
                                    <a href="#" id="logoutBtn1"><i class="fa fa-sign-out"></i> Jitoe</a>
                                <?php else: ?>
                                    <a href="#" id="loginBtn1"><i class="fa fa-sign-in"></i> Ingia</a> <!-- Login -->
                                    <a href="#" id="registerBtn1"><i class="fa fa-user-plus"></i> Jisajili</a> <!-- Register -->
                                <?php endif; ?>
                            </div>
                        </li>

                        <!-- Seller Panel -->
                        <?php if ($isSeller): ?>
                            <li class="scroll-to-section">
                                <a href="admin/home.php">
                                    <i class="fa fa-briefcase"></i> Jopo la Muuzaji <!-- Seller Panel -->
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <a class="menu-trigger">
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>

<div id="loginModal" class="popup">
    <div class="popup-content2">
        <?php include "login_form.php" ?>
    </div>
</div>

<div id="registerModal" class="popup">
    <div class="popup-content2">
        <?php include "registering_form2.php" ?>
    </div>
</div>

<div id="purchasesPopup" class="popup">
    <div class="popup-content">
        <span class="close-btn">&times;</span>
        <h2>Manunuzi Yako</h2><!--Your purchase -->
        <table class="table table-borderless fullTable">
            <thead>
                <tr>
                    <!-- <th scope="col">ID</th> -->
                    <th scope="col" class="mobileRemove">Tarehe</th>
                    <th scope="col">Bidhaa</th>
                    <th scope="col" class="mobileRemove">Muuzaji</th>
                    <th scope="col" class="mobileRemove">Simu ya Muuzaji</th>
                    <th scope="col">Kiasi</th>
                    <th scope="col" class="mobileRemove">Hali</th>
                    <th scope="col">Kitendo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $salesResult->fetch_assoc()) : ?>
                    <tr>
                        <!-- <td><?php echo htmlspecialchars($row['id']); ?></td> -->
                        <td class="mobileRemove"><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['productName']); ?> <br>
                            <a href="tel: <?php echo htmlspecialchars($row['sellerPhone']); ?>" class="webRemove">
                                (simu ya muuzaji <?php echo htmlspecialchars($row['sellerPhone']); ?>)
                            </a href="">
                        </td>
                        <td class="mobileRemove"><?php echo htmlspecialchars($row['sellerName']); ?></td>
                        <td class="mobileRemove"><?php echo htmlspecialchars($row['sellerPhone']); ?></td>
                        <td><?php echo htmlspecialchars($row['amount']); ?></td>
                        <td class="mobileRemove">
                            <?php echo $row['saleStatus'] == 0 ? 'Not Received' : 'Received'; ?>
                        </td>
                        <td>
                            <?php if ($row['saleStatus'] == 0) : ?>
                                <button class="btn btn-primary" onclick="confirmReceived(<?php echo $row['id']; ?>)">Thibitisha Kupokea Bidhaa</button>
                            <?php else : ?>
                                Bidhaa Iliyopokelewa
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    var loginModal = document.getElementById('loginModal');
    var registerModal = document.getElementById('registerModal');
    var loginBtn = document.getElementById('loginBtn');
    var loginBtn1 = document.getElementById('loginBtn1');
    var registerBtn = document.getElementById('registerBtn');
    var registerBtn1 = document.getElementById('registerBtn1');
    var closeButtons = document.getElementsByClassName('close-btn');
    loginBtn.onclick = function() {
        loginModal.style.display = 'block';
    }
    loginBtn1.onclick = function() {
        loginModal.style.display = 'block';
    }
    registerBtn.onclick = function() {
        registerModal.style.display = 'block';
    }
    registerBtn1.onclick = function() {
        registerModal.style.display = 'block';
    }
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].onclick = function() {
            loginModal.style.display = 'none';
            registerModal.style.display = 'none';
        }
    }
    window.onclick = function(event) {
        if (event.target == loginModal) {
            loginModal.style.display = 'none';
        }
        if (event.target == registerModal) {
            registerModal.style.display = 'none';
        }
    }
</script>
<script>
    document.getElementById('logoutBtn1').onclick = function(e) {
        e.preventDefault();
        if (confirm('Je, una uhakika unataka kujitoa?')) { // Are you sure you want to logout?
            // Make an AJAX call to logout.php
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'logout.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Redirect to home page after successful logout
                    window.location.href = 'https://pandadigital.co.tz/market/';
                }
            };
            xhr.send();
        }
    }
</script>
<script>
    document.getElementById('productSearch').addEventListener('keyup', function() {
        var searchQuery = this.value;
        var suggestionBox = document.getElementById('searchSuggestions');

        if (searchQuery.length >= 2) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_products.php?q=' + encodeURIComponent(searchQuery), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var suggestions = JSON.parse(xhr.responseText);
                    suggestionBox.innerHTML = ''; // Clear previous suggestions

                    suggestions.forEach(function(product) {
                        var suggestionItem = document.createElement('div');
                        suggestionItem.style.padding = '10px';
                        suggestionItem.style.cursor = 'pointer';
                        suggestionItem.style.borderBottom = '1px solid #ccc';
                        suggestionItem.style.display = 'flex';
                        suggestionItem.style.alignItems = 'center';

                        // Create image element
                        var productImage = document.createElement('img');
                        productImage.src = 'assets/images/' + product.image;
                        productImage.style.width = '50px';
                        productImage.style.height = '50px';
                        productImage.style.objectFit = 'cover';
                        productImage.style.marginRight = '10px';

                        // Create text container
                        var textContainer = document.createElement('div');
                        textContainer.innerHTML = '<strong>' + product.name + ' (Tsh.' + product.amount + ')</strong><br><small>' + product.description + '</small>';

                        suggestionItem.appendChild(productImage);
                        suggestionItem.appendChild(textContainer);

                        suggestionItem.addEventListener('click', function() {
                            window.location.href = 'single-product.php?id=' + product.id;
                        });

                        suggestionBox.appendChild(suggestionItem);
                    });
                    suggestionBox.style.display = 'block'; // Show suggestions
                }
            };
            xhr.send();
        } else {
            suggestionBox.innerHTML = ''; // Clear suggestions
            suggestionBox.style.display = 'none'; // Hide suggestions
        }
    });
</script>
<script>
    document.getElementById('trackPurchasesBtn').onclick = function() {
        document.getElementById('purchasesPopup').style.display = 'block';
    };
    document.getElementById('trackPurchasesBtn1').onclick = function() {
        document.getElementById('purchasesPopup').style.display = 'block';
    };
    document.querySelector('.close-btn').onclick = function() {
        document.getElementById('purchasesPopup').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('purchasesPopup')) {
            document.getElementById('purchasesPopup').style.display = 'none';
        }
    };

    function confirmReceived(saleId) {
        if (confirm('Je, una uhakika unataka kuthibitisha kupokea bidhaa hii?')) {
            window.location.href = 'confirm_receipt.php?saleId=' + saleId;
        }
    }
</script>
<script>
    document.getElementById('becomeSeller1').onclick = function(e) {
        e.preventDefault();
        if (confirm('Je, una uhakika unataka kuwa muuzaji?')) { // Are you sure you want to become a seller?
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'become_seller.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show success message
                            alert('Hongera! Sasa wewe ni muuzaji.'); // Congratulations! You are now a seller.
                            // Refresh the page to update the UI
                            window.location.reload();
                        } else {
                            alert('Kuna hitilafu imetokea: ' + (response.error || 'Unknown error')); // An error occurred
                        }
                    } catch (e) {
                        alert('Kuna hitilafu imetokea'); // An error occurred
                    }
                }
            };
            xhr.send();
        }
    }
</script>
<script>
    document.getElementById('trackPurchasesBtn').onclick = function() {
        document.getElementById('purchasesPopup').style.display = 'block';
    };
    document.querySelector('.close-btn').onclick = function() {
        document.getElementById('purchasesPopup').style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == document.getElementById('purchasesPopup')) {
            document.getElementById('purchasesPopup').style.display = 'none';
        }
    };

    // "Coming Soon" Popups for Wishlist, Rewards, and Gift Cards
    document.getElementById('wishlistBtn').onclick = function() {
        alert("Kipengele cha orodha ya matamanio kinakuja hivi karibuni!");
    };
    document.getElementById('rewardsBtn').onclick = function() {
        alert("Kipengele cha Zawadi kinakuja hivi karibuni!");
    };
    document.getElementById('giftCardsBtn').onclick = function() {
        alert("Kipengele cha Kadi za Zawadi kinakuja hivi karibuni!");
    };
    document.getElementById('wishlistBtn1').onclick = function() {
        alert("Kipengele cha orodha ya matamanio kinakuja hivi karibuni!");
    };
    document.getElementById('rewardsBtn1').onclick = function() {
        alert("Kipengele cha Zawadi kinakuja hivi karibuni!");
    };
    document.getElementById('giftCardsBtn1').onclick = function() {
        alert("Kipengele cha Kadi za Zawadi kinakuja hivi karibuni!");
    };
</script>
<style>
    .webRemove {
        display: none;
    }

    .popup {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .popup-content,
    .popup-content2 {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .popup-content2 {
        width: 40%;
    }

    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-btn:hover,
    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table th {
        background-color: #f4f4f4;
    }

    .table tr:hover {
        background-color: #f1f1f1;
    }

    .table button {
        cursor: pointer;
    }

    .search-bar {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-bar input[type="text"] {
        width: 80%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .search-bar button {
        padding: 8px;
        background-color: #4CAF50;
        border: none;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    .search-bar button:hover {
        background-color: #45a049;
    }

    .fa {
        margin-right: 5px;
    }

    .nav li a {
        display: flex;
        align-items: center;
        padding: 8px 15px;
    }

    .nav li a i {
        margin-right: 5px;
    }

    .user-dropdown {
        position: relative;
        display: inline-block;
    }

    .mobileView {
        display: none;
    }

    .desktopView {
        display: block;
    }

    .user-dropdown .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #000;
        color: #000000;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        min-width: 300px;
        z-index: 1;
        border-radius: 4px;
    }

    .user-dropdown .dropdown-menu a {
        color: #000000;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .user-dropdown .dropdown-menu a:hover {
        background-color: #662d91;
    }

    .user-dropdown:hover .dropdown-menu {
        display: block;
    }

    .nav li a {
        display: flex;
        align-items: center;
    }

    .nav li a i {
        margin-right: 5px;
    }

    #searchSuggestions {
        display: none;
        /* Hide by default */
        position: absolute;
        top: 100%;
        left: 0;
        background-color: white;
        border: 1px solid #ccc;
        width: 100%;
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }


    @media screen and (max-width: 768px) {
        .webRemove {
            display: block;
        }

        .popup-content,
        .popup-content2 {
            width: auto;
            margin: 10% auto;
        }

        .fullTable {
            width: 90%;
        }

        .mobileRemove {
            display: none;
        }

        .search-bar {
            display: none;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .mobileView {
            display: block;
        }

        .desktopView {
            display: none;
        }

        .dropdownIcon {
            display: none;
        }

        .user-dropdown .dropdown-menu {
            display: inline-block;
            position: absolute;
            background-color: #000;
            color: #000000;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 300px;
            z-index: 1;
            border-radius: 4px;
        }

        .user-dropdown .dropdown-menu a {
            color: #000000;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .user-dropdown .dropdown-menu a:hover {
            background-color: #662d91;
        }

        .user-dropdown:hover .dropdown-menu {
            display: block;
        }
    }
</style>
<?php
mysqli_free_result($result);
mysqli_free_result($salesResult);
mysqli_close($conn);
?>