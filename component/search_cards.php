<?php
// Renders product result cards for a search query.
// Expects $conn, $user_id and $search_query to be in scope.
$search_query = isset($search_query) ? trim($search_query) : '';

if ($search_query === '') {
    echo '<div class="empty"><p>Please search something</p></div>';
    return;
}

$select_products = $conn->prepare("SELECT * FROM products WHERE name LIKE ? AND status = ?");
$select_products->execute(["%{$search_query}%", 'active']);

if ($select_products->rowCount() > 0) {
    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
        $product_id = $fetch_products['id'];
?>
        <form action="search_product.php" method="post" class="box <?php if ($fetch_products['stock'] == 0) { echo "disabled"; } ?>">
            <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
            <?php if ($fetch_products['stock'] > 9) { ?>
                <span class="stock" style="color: green;">In stock</span>
            <?php } elseif ($fetch_products['stock'] == 0) { ?>
                <span class="stock" style="color: red;">Out of stock</span>
            <?php } else { ?>
                <span class="stock" style="color: red;">Hurry, only <?= $fetch_products['stock']; ?> left</span>
            <?php } ?>
            <p class="price">$<?= $fetch_products['price']; ?>/-</p>
            <div class="content">
                <img src="image/shape-19.png" alt="" class="shap">
                <div class="button">
                    <div><h3 class="name"><?= $fetch_products['name']; ?></h3></div>
                    <div>
                        <button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
                        <button type="submit" name="add_to_wishlist"><i class="bx bx-heart"></i></button>
                        <a href="view_page.php?pid=<?= $fetch_products['id'] ?>" class="bx bxs-show"></a>
                    </div>
                </div>
                <input type="hidden" name="product_id" value="<?= $fetch_products['id'] ?>">
                <div class="flex-btn">
                    <a href="checkout.php?get_id=<?= $fetch_products['id'] ?>" class="btn" onclick="this.href='checkout.php?get_id=<?= $fetch_products['id'] ?>&qty='+this.parentNode.querySelector('.qty').value">Buy Now</a>
                    <input type="number" name="qty" required min="1" value="1" max="<?= $fetch_products['stock']; ?>" class="qty">
                </div>
            </div>
        </form>
<?php
    }
} else {
    echo '<div class="empty"><p>No products found</p></div>';
}
