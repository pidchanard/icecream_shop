<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }
   $pid = $_GET['pid'];
   include 'component/add_wishlist.php';
   include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - product detail page</title>
    <link rel="stylesheet" type="text/css"href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">;

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>product detail</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>register</span>
        </div>
    </div>
   <section class="view_page">
        <div class="heading">
            <h1>product detail</h1>
            <img src="image/separator-img.png">
        </div>
        <?php
            if(isset($_GET['pid'])) {
                $pid = $_GET['pid'];
                $select_products = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $select_products->execute([$pid]);

                if($select_products->rowCount() > 0) {
                    while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {

        ?>
        <form action="" method="post" class="box">
            <div class="img-box">
                <img src="uploaded_files/<?=$fetch_products['image'];?>">
            </div>         
            <div class="detail">
            <?php if ($fetch_products['stock'] > 9) { ?>
                <span class="stock" style="color: green;">In stock</span>
            <?php } elseif ($fetch_products['stock'] == 0) { ?>
                <span class="stock" style="color: red;">Out of stock</span>
            <?php } else { ?>
                <span class="stock" style="color: red;">Hurry, only <?= $fetch_products['stock']; ?> left</span>
            <?php } ?>                    
            <p class="price">$<?=$fetch_products['price']; ?>/-</p>
            <div class="name"><?=$fetch_products['name']; ?></div>   
            <p class="product-detail"><?=$fetch_products['product_detail']; ?></p>
            <input type="hidden" name="product_id" value="<?=$fetch_products['id']; ?>">
            <div class="button">
                <button type="submit" name="add_to_wishlist" class="btn">add to wishlist<i class="bx bx-heart"></i></button>
                <input type="hidden" name="qty" value="1" min="0" class="quantity">
                <button type="submit" name="add_to_cart" class="btn">add to cart<i class="bx bx-cart"></i></button>
            </div>                
            </div>  
        </form>
        <?php
                    }
                }
            }
        ?>
   </section>
    <div class="products">
        <div class="heading">
            <h1>similar products</h1>
            <p>Explore a variety of ice cream flavors, from classic vanilla to rich chocolate fudge, all crafted to perfection. 
                Indulge in fruity favorites like mango sorbet and strawberry swirl for a refreshing treat.</p>
            <img src="image/separator-img.png">    
        </div>
        <?php include 'component/shop.php';?>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>



    <?php include 'component/alert.php'; ?>

    <?php include 'component/footer.php'; ?>
</body>
</html>