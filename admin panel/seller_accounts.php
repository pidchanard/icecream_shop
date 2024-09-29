<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if(isset($_COOKIE['seller_id'])){
    $seller_id = $_COOKIE['seller_id'];
}else{
    $seller_id = '';
    header('location:login.php');
}
//update order from database
    if(isset($_POST['update_order'])){
        $order_id = $_POST['order_id'];
        $order_id = filter_var($order_id, FILTER_SANITIZE_STRING);

        $update_payment = $_POST['update_payment'];
        $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);

        $update_pay = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id=?");
        $update_pay->execute([$update_payment, $order_id]);
        $success_msg[] = 'order payment status updated';
    }
    //delete order
    if(isset($_POST['delete_order'])){
        $delete_id = $_POST['order_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT *FROM orders WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if($verify_delete->rowCount() > 0){
            $delete_order=$conn->prepare("DELETE FROM orders WHERE id = ?");
            $delete_order->execute([$delete_id]);

            $success_msg[] = 'order deleted';
        }else{
            $warning_msg[] = 'order already deleted';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Registered sellers Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link src="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="user-container">
            <div class="heading">
                <h1>registered sellers</h1>
                <img src="../image/separator-img.png">
            </div>
            <div class="box-container">
                <?php
                 $select_sellers =$conn->prepare("SELECT * FROM sellers ");
                 $select_sellers->execute();

                 if($select_sellers->rowCount() > 0){
                    while($fetch_sellers =$select_sellers->fetch(PDO::FETCH_ASSOC)){
                        $user_id = $fetch_sellers['id'];
           
                ?>
                <div class="box">
                    <img src="../uploaded_files/<?= $fetch_sellers['image']; ?>"
                    <p>user id :<span><?=$user_id; ?></span></p>
                    <p>user name :<span><?=$fetch_sellers['name']; ?></span></p>
                    <p>user email :<span><?=$fetch_sellers['email']; ?></span></p>
                </div>
                <?php
                 }
                }else{
                    echo'<div class="empty">
                    <p>no user registered yet! </p>
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
