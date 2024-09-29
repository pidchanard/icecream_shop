<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if(isset($_COOKIE['seller_id'])){
    $seller_id = $_COOKIE['seller_id'];
}else{
    $seller_id = '';
    header('location:login.php');
}
    $select_products =$conn->prepare("SELECT * FROM products WHERE seller_id =? ");
    $select_products->execute([$seller_id]);
    $total_products = $select_products->rowCount();

    $select_orders =$conn->prepare("SELECT * FROM orders WHERE seller_id =? ");
    $select_orders->execute([$seller_id]);
    $total_orders = $select_orders->rowCount()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Seller profile Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link src="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="seller-profile">
            <div class="heading">
                <h1>profile details</h1>
                <img src="../image/separator-img.png">
            </div>
          <div class="details">
            <div class="seller">
                <img src="../uploaded_files/<?=$fetch_profile['image'];?>">
                <h3 class="name"><?=$fetch_profile['name'];?></h3>
                <span>seller</span>
                <a href="update.php" class="btn">update profile</a>
            </div>
            <div class="flex">
                <div class="box">
                    <span><?=$total_products; ?></span>
                    <p>total orders placed</p>
                    <a href="view_product.php" class="btn">view products</a>
                </div>
                <div class="box">
                    <span><?=$total_products; ?></span>
                    <p>total orders placed</p>
                    <a href="admin_order.php" class="btn">view orders</a>
                </div>
            </div>
          </div>
        </section>

    </div>
   

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/admin_script.js"></script>

    <?php include '../component/alert.php'; ?>
</body>
</html>
