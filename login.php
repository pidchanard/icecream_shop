<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }
    if (isset($_POST['submit'])) {   
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = sha1($_POST['pass']); // แปลงรหัสผ่านเป็น sha1


    // ตรวจสอบอีเมลในฐานข้อมูล
    $select_user = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $select_user ->execute([$email,$pass]);
    $row =$select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user ->rowCount() > 0) {
        setcookie('user_id',$row['id'], time()+ 60*60*24*30,'/');
        header('location:home.php');
    }else{
        $warning_msg[]='incorrect email or password';
}
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - user login page</title>
    <link rel="stylesheet" type="text/css"href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">;

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>login</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>register</span>
        </div>
    </div>
    <div class="form-container">
    <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>Login Now</h3>
            <div class="input-field">
                        <p>@Email <span>*</span></p>
                        <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
                    </div>

                    <div class="input-field">
                        <p>@Password <span>*</span></p>
                        <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
                    </div>
        
            <p class="link">Do not have an account? <a href="register.php">register now</a></p>
            <input type="submit" name="submit" value="login Now" class="btn">
        </form>
    </div>

    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>



    <?php include 'component/alert.php'; ?>

    <?php include 'component/footer.php'; ?>
</body>
</html>