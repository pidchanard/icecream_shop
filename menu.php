<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }
    include 'component/add_wishlist.php';
    include 'component/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - our shop page</title>
    <link rel="stylesheet" type="text/css"href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>our shop</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>register</span>
        </div>
    </div>
    <div class="products">
        <div class="heading">
            <h1>our latest flavoure</h1>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container">
            <?php
                $select_products =$conn->prepare("SELECT *FROM products WHERE status = ? ");
                $select_products->execute(['active']);

                // Pagination: 8 products per page
                $per_page = 8;
                $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

                $all_products = [];
                while ($row = $select_products->fetch(PDO::FETCH_ASSOC)) { $all_products[] = $row; }

                $total_products = count($all_products);
                $total_pages = max(1, (int) ceil($total_products / $per_page));
                $page = min($page, $total_pages);
                $products_page = array_slice($all_products, ($page - 1) * $per_page, $per_page);

                if($total_products > 0) {
                    foreach($products_page as $fetch_products) {

            ?>
            <form action="" method="post" class="box <?php if($fetch_products['stock'] ==0 ){echo "
                disabled"; } ?>">
                
                <img src="uploaded_files/<?=$fetch_products['image'];?>" class="image">
                <?php if($fetch_products['stock'] >9){?>
                    <span class="stock" style="color: green;">In stock</span>
                <?php }elseif($fetch_products['stock'] == 0) { ?>
                    <span class="stock" style="color: red;">Out of stock</span>
                <?php }else{ ?>
                    <span class="stock" style="color: red;">Hurry, only <?= $fetch_products['stock']
                    ;?></span>
                <?php } ?>
                <p class="price">$<?= $fetch_products['price'];?>/-</p>
                <div class="content">
                    <img src="image/shape-19.png" alt="" class="shap">
                    <div class="button">
                        <div><h3 class="name"><?= $fetch_products['name'];?></h3></div>
                        <div>
                            <button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
                            <button type="submit" name="add_to_wishlist"><i class="bx bx-heart"></i></button>
                            <a href="view_page.php?pid=<?= $fetch_products['id']?>" class="bx bxs-show"></a>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="<?=$fetch_products['id']?>">
                    <div class="flex-btn">
                        <a href="checkout.php?get_id=<?=$fetch_products['id']?>" class="btn">buy now</a>
                        <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                    </div>
                </div>
            </form>
            <?php
                    }
                    }else{
                        echo '
                        <div class="empty">
                            <p>no products added yet!</p>
                        </div>
                        ';
                    }
            ?>
        </div>

        <?php if ($total_pages > 1) { ?>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?= $page - 1; ?>" class="btn">&laquo; Prev</a>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?page=<?= $i; ?>" class="btn <?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <a href="?page=<?= $page + 1; ?>" class="btn">Next &raquo;</a>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>



    <?php include 'component/alert.php'; ?>

    <?php include 'component/footer.php'; ?>
</body>
</html>