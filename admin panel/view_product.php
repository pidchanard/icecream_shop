<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if(isset($_COOKIE['seller_id'])){
    $seller_id = $_COOKIE['seller_id'];
}else{
    $seller_id = '';
    header('location:login.php');
    exit; // เพิ่ม exit เพื่อหยุดการทำงานหากไม่ได้ล็อกอิน
}

// Delete product
if(isset($_POST['delete'])){
    $p_id = $_POST['product_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);

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
    <link src="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="show-post">
        <div class="heading">
            <h1>Your Products</h1>
            <img src="../image/separator-img.png">
        </div>
        <div class="box-container">
            <?php 
                $select_products = $conn->prepare("SELECT * FROM products WHERE seller_id = ?");
                $select_products->execute([$seller_id]);
                
                if($select_products->rowCount() > 0){
                    while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="product_id" value="<?= $fetch_products['id'];?>">
                <?php if($fetch_products['image'] != ''){ ?>
                    <img src="../uploaded_files/<?=$fetch_products['image']; ?>">
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
                        <button type="submit" name="delete" class="btn" onclick="return confirm('Delete this product?');">Delete</button>
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
    </section>
</div>

<!-- SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../component/alert.php'; ?>
</body>
</html>
