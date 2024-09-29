<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - home page</title>
    <link rel="stylesheet" type="text/css"href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">;

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="slider-container">
        <div class="slider">
            <div class="slideBox active">
                <div class="textBox">
                    <h1>we prider ourself on <br> exceptional flavors</h1>
                    <a href="menu.php" class="btn">shop now</a>
                </div>
                <div class="imgBox">
                    <img src="image/slider.jpg">
                </div>
            </div>
            <div class="slideBox">
                <div class="textBox">
                    <h1>cold treats are my kind <br> of comfort food</h1>
                    <a href="menu.php"class="btn">shop now</a>
                </div>
                <div class="imgBox">
                    <img src="image/slider0.jpg">
                </div>
            </div>
        </div>
        <ul class="controls">
            <li onclick="nextSlide()"class="next"> <i class="bx bx-right-arrow-alt"></i></li>
            <li onclick="prevSlide()"class="prev"> <i class="bx bx-left-arrow-alt"></i></li>
        </ul>
    </div>
    <!-- slider section end -->
     <div class="service">
        <div class="box-container">
            <!-- service item box -->
             <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="image/services.png"class="img1">
                        <img src="image/services (1).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>delivery</h4>
                    <span>100% secure</span>
                </div>
             </div>
             <!-- service item box -->
             <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="image/services (2).png"class="img1">
                        <img src="image/services (3).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>payment</h4>
                    <span>100% secure</span>
                </div>
             </div>
              <!-- service item box -->
              <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="image/services (5).png"class="img1">
                        <img src="image/services (6).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>support</h4>
                    <span>24 * 7</span>
                </div>
             </div>
                <!-- service item box -->
                 <!-- service item box -->
              <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="image/services (7).png"class="img1">
                        <img src="image/services (8).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>gift service</h4>
                    <span>support gift service</span>
                </div>
             </div>
                <!-- service item box -->
                 <!-- service item box -->
              <div class="box">
                <div class="icon">
                    <div class="icon-box">
                        <img src="image/service.png"class="img1">
                        <img src="image/service (1).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>returns</h4>
                    <span>24*7 free returns</span>
                </div>
             </div>
                <!-- service item box -->
                 <!-- service item box -->
              <div class="box">
                <div class="icon">
                    <div class="icon-box">
                    <img src="image/services.png"class="img1">
                    <img src="image/services (1).png"class="img2">
                    </div>
                </div>
                <div class="detail">
                    <h4>deliver</h4>
                    <span>100% secure</span>
                </div>
             </div>
                <!-- service item box -->
            </div>
        </div>
        <div class="categories">
            <div class="heading">
                <h1>categories features</h1>
                <img src="image/separator-img.png">
            </div>
            <div class="box-container">
                <div class="box">
                    <img src="image/categories.jpg">
                    <a href="menu.php"class="btn">coconut</a>
                </div>         
                <div class="box">
                    <img src="image/categories0.jpg">
                    <a href="menu.php"class="btn">chocolate</a>
                </div>     
                <div class="box">
                    <img src="image/categories2.jpg">
                    <a href="menu.php"class="btn">strawberry</a>
                </div>
                <div class="box">
                    <img src="image/categories1.jpg">
                    <a href="menu.php"class="btn">corn</a>
                </div>
            </div>
        </div>
        <img src="image/menu-banner.jpg"class="menu-banner">
        <div class="taste">
            <div class="heading">
                <span>Taste</span>
                <h1>buy any ice cream @ get one free</h1>
                <img src="image/separator-img.png">
            </div>
            <div class="box-container">
                <div class="box">
                    <img src="image/taste.webp">
                    <div class="detail">
                        <h2>natural sweetness</h2>
                        <h1>vanila</h1>
                    </div>
                </div>
                <div class="box">
                    <img src="image/taste0.webp">
                    <div class="detail">
                        <h2>natural sweetness</h2>
                        <h1>matcha</h1>
                    </div>
                </div>
                <div class="box">
                    <img src="image/taste1.webp">
                    <div class="detail">
                        <h2>natural sweetness</h2>
                        <h1>blueberry</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="ice-container">
            <div class="overlay"></div>
            <div class="detail">
                <h1>Ice cream is cheaper than <br> therapy for stress</h1>
                <p>Enjoying a favorite flavor can lift your mood and provide <br>
                     a brief escape, helping many people relax, even <br>
                     if just for a moment.</p>
                    <a href="menu.php"class="btn">shop now</a>
            </div>
        </div>

        <div class="taste2">
    <div class="t-banner">
        <div class="overlay"></div>
        <div class="detail">
            <h1>Find your taste of desserts</h1>
            <p>Treat them to a delicious treat and send them some Luck 'o Irish too!</p>
            <a href="menu.php" class="btn">Shop now</a>
        </div>
    </div>
    <div class="box-container">
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type4.jpg" alt="Strawberry">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type.avif" alt="Dessert">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type1.png" alt="Dessert">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type2.png" alt="Dessert">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type0.avif" alt="Dessert">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
        <div class="box">
            <div class="box-overlay"></div>
            <img src="image/type1.png" alt="Dessert">
            <div class="box-details fadeIn-bottom">
                <h1>Strawberry</h1>
                <p>Find your taste of desserts</p>
                <a href="menu.php" class="btn">Explore more</a>
            </div>
        </div>
    </div>
</div>
<!---flavor section--->
<div class="flavor">
    <div class="box-container">
        <img src="image/left-banner2.webp">
        <div class="detail">
            <h1>Hot Deal ! Sale Up To <span> 20% off</span></h1>
            <p>expired</p><br>
            <a href="menu.php" class="btn">Shop now</a>
        </div>
    </div>
</div>
<!---flavor section--->
<div class="usage">
    <div class="heading">
        <h1>how it works</h1>
        <img src="image/separator-img.png">
    </div>
    <div class="row">
        <!-- กล่องด้านซ้าย -->
        <div class="box-container">
            <div class="box">
                <img src="image/icon.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
            <div class="box">
                <img src="image/icon0.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
            <div class="box">
                <img src="image/icon1.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
        </div>

        <!-- รูปตรงกลาง -->
        <img src="image/sub-banner.png" class="divider">

        <!-- กล่องด้านขวา -->
        <div class="box-container">
            <div class="box">
                <img src="image/icon2.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
            <div class="box">
                <img src="image/icon3.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
            <div class="box">
                <img src="image/icon4.avif">
                <div class="detail">
                    <h3>scoop ice-cream</h3>
                    <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                    followed by a gentle press into the ice cream. With a smooth, rotating motion of the wrist, 
                    you form a neat ball that easily releases into a bowl or cone for a perfect, creamy scoop.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!---usage section end--->
<div class="pride">
    <div class="detail">
        <h1>We pride Ourselves On <br> Exceptional Flovers. </h1>
        <p>and our ice cream is a testament to that. Each scoop combines high-quality <br>ingredients 
            with a touch of creativity, delivering a creamy,<br>delicious experience that’s sure to delight.</p>
            <a href="menu.php" class="btn">shop now</a>
    </div>    
</div>
<!---pride section end--->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>



    <?php include 'component/alert.php'; ?>

    <?php include 'component/footer.php'; ?>
</body>
</html>