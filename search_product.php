<?php 
    include 'component/connect.php';

    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        $user_id = '';
    }
    include 'component/add_wishlist.php';
    include 'component/add_cart.php';

    // Accept the query from a live-search navigation (GET q) or a normal submit (POST)
    if (isset($_GET['q'])) {
        $search_query = $_GET['q'];
    } elseif (isset($_POST['search_product'])) {
        $search_query = $_POST['search_product'];
    } else {
        $search_query = '';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Search Products Page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Search Products</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
            <span><a href="home.php">Home</a><i class="bx bx-right-arrow-alt"></i>Search Products</span>
        </div>
    </div>
    <div class="products">
        <div class="heading">
            <h1>Search Result</h1>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container" id="box-container">
            <?php
                include 'component/search_cards.php';
            ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>

    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>
