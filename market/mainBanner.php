<?php
include "connect.php";

$query = "SELECT * FROM categories ORDER BY id";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
mysqli_free_result($result);
mysqli_close($conn);
?>

<div class="main-banner" id="top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="right-content">
                    <div class="d-flex flex-row overflow-auto">
                        <?php foreach ($categories as $category): ?>
                            <a href="productsByCategories.php?categoryId=<?php echo $category['id']; ?>">
                            <div class="align-items-center text-center p-3 me-3 border rounded shadow-sm">
                                <div class="thumb">
                                    <img src="assets/images/<?php echo htmlspecialchars($category['image']); ?>" alt=""
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </div>
                                <h4 style="font-weight: bolder; margin-top: 15px; color: #000000"><?php echo htmlspecialchars($category['name']); ?></h4>
                                <div class="main-border-button mt-1">

                                    <p>Gundua Zaidi</p>

                                </div>
                            </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>