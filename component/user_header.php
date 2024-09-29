<header class="header">
    <section class="flex">
        <a href="home.php" class="logo">
            <img src="image/logoice.png" width="130px">
        </a>

        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="about-us.php">About Us</a>
            <a href="menu.php">Shop</a>
            <a href="order.php">Order</a>
            <a href="contact.php">Contact Us</a>
        </nav>

        <!-- Search Form -->
        <form action="search_product.php" method="post" class="search-form">
            <input type="text" name="search_product" placeholder="Search product..." required maxlength="100">
            <button type="submit" class="bx bx-search-alt-2" id="search_product_btn"></button>
        </form>

        <!-- Icons and Wishlist/Cart Counters -->
        <div class="icons">
            <div class="bx bx-list-plus" id="menu-btn"></div>
            <div class="bx bx-search-alt-2" id="search-btn"></div>

            <?php
                // Wishlist count
                $count_wishlist_item = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
                $count_wishlist_item->execute([$user_id]);
                $total_wishlist_item = $count_wishlist_item->rowCount();
            ?>
            <a href="wishlist.php"><i class="bx bx-heart"></i><sup><?= $total_wishlist_item; ?></sup></a>

            <?php
                // Cart count
                $count_cart_item = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
                $count_cart_item->execute([$user_id]);
                $total_cart_item = $count_cart_item->rowCount();
            ?>
            <a href="cart.php"><i class="bx bx-cart"></i><sup><?= $total_cart_item; ?></sup></a>

            <!-- User Icon -->
            <div class="bx bxs-user" id="user-btn"></div>
        </div>

        <!-- Profile Detail -->
        <div class="profile-detail">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $select_profile->execute([$user_id]);

                if ($select_profile->rowCount() > 0) {
                    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
                <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="User Profile">
                <h3 style="margin-bottom: 1rem;"><?= $fetch_profile['name']; ?></h3>
                <div class="flex-btn">
                    <a href="profile.php" class="btn">View Profile</a>
                    <a href="component/user_logout.php" onclick="return confirm('Logout from this website?');" class="btn">Logout</a>
                </div>
            <?php } else { ?>
                <h3 style="margin-bottom: 1rem;">Please login or register</h3>
                <div class="flex-btn">
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn">Register</a>
                </div>
            <?php } ?>
        </div>
    </section>
</header>
