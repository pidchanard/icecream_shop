<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

include 'admin_auth.php'; // verifies the logged-in seller and exits if not authenticated

// Delete product
if(isset($_POST['delete'])){
    $p_id = $_POST['product_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $delete_product = $conn->prepare('DELETE FROM `products` WHERE id = ?');
    $delete_product->execute([$p_id]); // เปลี่ยนเป็นอาร์เรย์

    $success_msg[] = 'Product deleted';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Show products Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <?php
        // Optional status filter coming from the dashboard (active / deactive)
        $status_filter = $_GET['status'] ?? '';
        if ($status_filter == 'active' || $status_filter == 'deactive') {
            $select_products = $conn->prepare("SELECT * FROM products WHERE seller_id = ? AND status = ?");
            $select_products->execute([$seller_id, $status_filter]);
            $products_heading = ucfirst($status_filter) . ' Products';
        } else {
            $select_products = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
            $select_products->execute([$seller_id]);
            $products_heading = 'Your Products';
        }

        // Pagination: 6 products per page
        $per_page = 6;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $all_products = [];
        while ($row = $select_products->fetch(PDO::FETCH_ASSOC)) {
            $all_products[] = $row;
        }

        $total_products = count($all_products);
        $total_pages = max(1, (int) ceil($total_products / $per_page));
        $page = min($page, $total_pages);
        $products_page = array_slice($all_products, ($page - 1) * $per_page, $per_page);

        // Keep the status filter in pagination links
        $status_qs = $status_filter !== '' ? 'status=' . urlencode($status_filter) . '&' : '';
    ?>
    <section class="show-post">
        <div class="heading">
            <h1><?= $products_heading; ?></h1>
            <img src="../image/separator-img.png">
        </div>
        <div class="box-container">
            <?php
                if($total_products > 0){
                    foreach($products_page as $fetch_products){
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="product_id" value="<?= $fetch_products['id'];?>">
                <?php if($fetch_products['image'] != ''){ ?>
                    <img src="../uploaded_files/<?=$fetch_products['image']; ?>" class="image">
                <?php } ?>
                <div class="status" style="color: <?= $fetch_products['status'] == 'active' ? 'limegreen' : 'coral'; ?>">
                    <?=$fetch_products['status']; ?>
                </div>
                <div class="price">$<?= $fetch_products['price']; ?>/-</div>
                <div class="content">
                    <img src="../image/shape-19.png" class="shap">
                    <div class="title"><?=$fetch_products['name']; ?></div>
                    <div class="flex-btn">
                        <a href="edit_product.php?id=<?=$fetch_products['id']; ?>" class="btn"><span class="textedit">Edit</span></a>
                        <button type="submit" name="delete" class="btn" data-confirm="Delete this product?">Delete</button>
                        <a href="read_product.php?post_id=<?=$fetch_products['id']; ?>" class="btn">Read </a>
                    </div>
                </div>
            </form>
            <?php
                    }
                }else{
                    echo '<div class="empty">
                            <p>No products added yet! <br><a href="add_products.php" class="btn" style="margin-top: 1.5rem;">Add Products</a></p>
                        </div>';
                }
            ?>
        </div>

        <?php if ($total_pages > 1) { ?>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $page - 1; ?>" class="btn">&laquo; Prev</a>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $i; ?>" class="btn <?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $page + 1; ?>" class="btn">Next &raquo;</a>
            <?php } ?>
        </div>
        <?php } ?>
    </section>
</div>

<!-- SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../component/alert.php'; ?>
</body>
</html>
