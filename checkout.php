<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        header('location:login.php');
        exit();
    }

    if (isset($_POST['place_order'])) {

        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        $address = filter_var($_POST['flat'] . ',' . $_POST['street'] . ',' . $_POST['city'] . ',' . $_POST['country'] . ',' . $_POST['pin'], FILTER_SANITIZE_STRING);
        $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);

        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? LIMIT 1");
        $verify_cart->execute([$user_id]);

        if (isset($_GET['get_id'])) {

            $get_product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
            $get_product->execute([$_GET['get_id']]);

            if ($get_product->rowCount() > 0) {
                while ($fetch_product = $get_product->fetch(PDO::FETCH_ASSOC)) {
                    $seller_id = $fetch_product['seller_id'];

                    $insert_order = $conn->prepare("INSERT INTO `orders` (id, user_id, seller_id, name, number, email, address, address_type, method, product_id, price, qty) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert_order->execute([uniqid(), $user_id, $seller_id, $name, $number, $email, $address, $_POST['address_type'], $method, $fetch_product['id'], $fetch_product['price'], 1]);

                    header('location:order.php');
                    exit();
                }
            } else {
                $warning_msg[] = 'Something went wrong';
            }
        } elseif ($verify_cart->rowCount() > 0) {
            while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                $s_products = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                $s_products->execute([$f_cart['product_id']]);
                $f_products = $s_products->fetch(PDO::FETCH_ASSOC);

                $seller_id = $f_products['seller_id'];

                $insert_order = $conn->prepare("INSERT INTO `orders` (id, user_id, seller_id, name, number, email, address, address_type, method, product_id, price, qty) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_order->execute([uniqid(), $user_id, $seller_id, $name, $number, $email, $address, $_POST['address_type'], $method, $f_cart['product_id'], $f_products['price'], $f_cart['qty']]);
            }

            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart->execute([$user_id]);

            header('location:order.php');
            exit();
        } else {
            $warning_msg[] = 'Something went wrong';
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Checkout page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Checkout</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">Home</a><i class="bx bx-right-arrow-alt"></i>Checkout</span>
        </div>
    </div>
    <div class="checkout">
        <div class="heading">
            <h1>Checkout Summary</h1>
            <img src="image/separator-img.png">
        </div>
        <div class="row">
            <form action="" method="post" class="register">
                <h3>Billing Details</h3>
                <div class="flex">
                    <div class="box">
                        <div class="input-field">
                            <p>Your Name <span>*</span></p>
                            <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Your Number <span>*</span></p>
                            <input type="text" name="number" required maxlength="10" placeholder="Enter your number" class="input">
                        </div>
                        <div class="input-field">
                            <p>Your Email <span>*</span></p>
                            <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="input">
                        </div>
                        <div class="input-field">
                            <p>Payment Method <span>*</span></p>
                            <select name="method" class="input">
                                <option value="cash on delivery">Cash on Delivery</option>
                                <option value="credit or debit card">Credit or Debit Card</option>
                                <option value="net banking">Net Banking</option>
                                <option value="UPI or PayPal">UPI or PayPal</option>
                                <option value="paytm">Paytm</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Address Type <span>*</span></p>
                            <select name="address_type" class="input">
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="input-field">
                            <p>Address Line 01<span>*</span></p>
                            <input type="text" name="flat" required maxlength="50" placeholder="Flat or building name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Address Line 02<span>*</span></p>
                            <input type="text" name="street" required maxlength="50" placeholder="e.g. street name" class="input">
                        </div>
                        <div class="input-field">
                            <p>City Name <span>*</span></p>
                            <input type="text" name="city" required maxlength="50" placeholder="e.g. city name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Country Name <span>*</span></p>
                            <input type="text" name="country" required maxlength="50" placeholder="e.g. country name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Pincode <span>*</span></p>
                            <input type="number" name="pin" required maxlength="6" min="0" placeholder="e.g. 100111" class="input">
                        </div>
                    </div>
                </div>
                <button type="submit" name="place_order" value="btn">Place Order</button>
            </form>

            <div class="summary">
                <h3>My Bag</h3>
                <div class="box-container">
                    <?php
                        $grand_total = 0; 
                        if (isset($_GET['get_id'])) {
                            $select_get = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                            $select_get->execute([$_GET['get_id']]);

                            while ($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total = $fetch_get['price'];
                                $grand_total += $sub_total;
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_get['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><?= $fetch_get['name']; ?></h3>
                            <p class="price">$<?= $fetch_get['price']; ?></p>
                        </div>
                    </div>
                    <?php 
                            }
                        } else {
                            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                            $select_cart->execute([$user_id]);

                            if ($select_cart->rowCount() > 0) {
                                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                                    $select_products->execute([$fetch_cart['product_id']]);
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);

                                    $sub_total = $fetch_products['price'] * $fetch_cart['qty'];
                                    $grand_total += $sub_total;
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><?= $fetch_products['name']; ?></h3>
                            <p class="price">$<?= $fetch_products['price']; ?> <span>x<?= $fetch_cart['qty']; ?></span></p>
                        </div>
                    </div>
                    <?php 
                                }
                            } else {
                                echo '<p class="empty">Your cart is empty!</p>';
                            }
                        }
                    ?>
                </div>
                <div class="total">
                    <h3>Grand Total:</h3>
                    <p>$<?= $grand_total; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
