<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('Location: login.php');
    
}

$get_id = $_GET['post_id'];
//delete products
    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];
        $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
        $delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ? AND seller_id = ? ");
        $delete_image->execute([$p_id, $seller_id]);
        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
        if ($fetch_delete_image[''] != '') {
            unlink('../uploaded_files/'.$fetch_delete_image['image']);
        }
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ? AND seller_id = ?");
        $delete_product->execute([$p_id, $seller_id]);
        header("location:view_product.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Show Products Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="read-post">
        <div class="heading">
            <h1>Product Detail</h1>
            <img src="../image/separator-img.png" alt="Separator">
        </div>
        <div class="box-container">
        <?php
            $select_products = $conn->prepare("SELECT * FROM products WHERE id=? AND seller_id=?");
            $select_products->execute([$get_id, $seller_id]);
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="post" class="box">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($fetch_products['id']); ?>">
            <div class="status" style="color: <?= ($fetch_products['status'] == 'active') ? 'limegreen' : 'coral'; ?>"><?= htmlspecialchars($fetch_products['status']); ?></div>
            <?php if ($fetch_products['image'] != '') { ?>
                <img src="../uploaded_files/<?= htmlspecialchars($fetch_products['image']); ?>" alt="Product Image" class="image">
            <?php } ?>
            <div class="price">$<?= htmlspecialchars($fetch_products['price']); ?>/-</div>
                <div class="title"><?= htmlspecialchars($fetch_products['name']); ?></div>
                <div class="content"><?= htmlspecialchars($fetch_products['product_detail']); ?></div>
                <div class="flex-btn">
                    <a href="edit_product.php?id=<?=$fetch_products['id']; ?>" class="btn">edit</a>
                    <button type="submit" name="delete" class="btn" onclick="return confirm('delete this product');">delete</button>
                    <a href="view_product.php?post_id=<?=$fetch_products['id']; ?>" class="btn">go back</a>
                </div>
        </form>
        <?php
                }
            } else {
                echo '<div class="empty">
                        <p>No products added yet! <br><a href="add_products.php" class="btn" style="margin-top: 1.5rem;">Add products</a></p>
                      </div>';
            }
        ?>
        </div>
    </section>
</div>

<!-- SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../component/alert.php'; ?>
</body>
</html>
