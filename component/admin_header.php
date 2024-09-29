
<header>
    <div class="logo">
        <img src="../image/logoice.png" width="150">
    </div>
    <div class="right">
        <div class="fa-solid fa-user" id="user-btn"></div> 
        <div class="toggle-btn"><i class="bx bx-menu"></i></div>

    </div>
    <div class="profile-detail">
        <?php
        $select_profile =$conn->prepare("SELECT * FROM sellers WHERE id=? ");
        $select_profile->execute([$seller_id]);

        if($select_profile->rowCount()> 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="profile">
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" class="logo-img" width="100">
            <p><?= $fetch_profile['name']; ?></p>
            <div class="flex-btn">
                <a href="profile.php" class="btn">profile</a>
                <a href="../component/admin_logout.php" onclick="return confirm('logout from this website?');" class="btn">logout</a>
            </div>
        </div>
        <?php } ?>
    </div>
</header>
<div class="sidebar-container">
    <div class="sidebar">
    <?php
        $select_profile =$conn->prepare("SELECT * FROM sellers WHERE id=? ");
        $select_profile->execute([$seller_id]);

        if($select_profile->rowCount()> 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="profile">
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" class="logo-img" width="100">
            <p><?= $fetch_profile['name']; ?></p>
            
        </div>
        <?php } ?>
        <h5>menu</h5>
        <div class="navbar">
            <ul>
                <li> <a href="dashboard.php"><i class="fa-sharp fa-solid fa-house-user"></i>dashboard</a></li>
                <li> <a href="add_products.php"><i class="fa-solid fa-bag-shopping"></i>add products</a></li>
                <li> <a href="view_product.php"><i class="fa-solid fa-folder-minus"></i>view product</a></li>
                <li> <a href="user_accounts.php"><i class="fa-regular fa-id-card"></i>accounts</a></li>
                <li> <a href="../component/admin_logout.php" onclick="return confirm('logout from this website ')"><i class="fa-solid fa-right-from-bracket"></i>logout</a></li>
            </ul>
        </div>
        <h5>find us</h5>
        <div class="social-links">
        <i class="fa-brands fa-facebook"></i>
        <i class="fa-brands fa-instagram"></i>
        <i class="fa-brands fa-linkedin"></i>
        <i class="fa-brands fa-twitter"></i>
        </div>
    </div>
</div>


