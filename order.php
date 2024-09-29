<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
        header('location:login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - User Order page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Our Order</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">Home</a><i class="bx bx-right-arrow-alt"></i>My Orders</span>
        </div>
    </div>
    <div class="orders">
        <div class="heading">
            <h1>My Order detail</h1>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container">
            <?php 
                $select_order = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY dates DESC");
                $select_order->execute([$user_id]);

                if ($select_order->rowCount() > 0){
                    while($fetch_orders = $select_order->fetch(PDO::FETCH_ASSOC)){
                        $product_id = $fetch_orders['product_id'];

                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_products->execute([$product_id]);

                        if ($select_products->rowCount() > 0 ) {
                            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                                // ตรวจสอบว่า 'dates' มีอยู่ในอาร์เรย์หรือไม่
                                $order_date = isset($fetch_orders['dates']) ? $fetch_orders['dates'] : 'วันที่ไม่ระบุ';
                ?>
                <div class="box"<?php if($fetch_orders['status'] == 'canceled') {echo ' style="border:2px solid red"';} ?>>
                    <a href="view_order.php?get_id=<?= $fetch_orders['id']; ?>">
                        <img src="uploaded_files/<?= $fetch_products['image'] ?>" class="image">
                        <p class="date"><i class="bx bxs-calendar-alt"></i> <?= $order_date; ?></p>
                        <div class="content">
                            <img src="image/shape-19.png" class="shap">
                            <div class="row">
                                <h3 class="name"><?= $fetch_products['name'] ?></h3>
                                <p class="price">Price: $<?= $fetch_products['price'] ?>/-</p>
                                <p class="status" style="color: <?php if($fetch_orders['status'] == 'delivered') {echo "green";}
                                elseif($fetch_orders['status'] == 'canceled'){echo "red";}
                                else{echo "orange";} ?>"> <?= $fetch_orders['status'];?> </p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php    
                            }
                        }
                    }
                } else { 
                    echo '<p class="empty">No orders placed yet</p>';
                }
            ?>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>

    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>
