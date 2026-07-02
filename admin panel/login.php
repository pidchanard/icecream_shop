<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if (isset($_POST['submit'])) {
   
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = sha1($_POST['pass']); // แปลงรหัสผ่านเป็น sha1


    // ตรวจสอบอีเมลในฐานข้อมูล
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ? AND password = ?");
    $select_seller->execute([$email,$pass]);
    $row =$select_seller->fetch(PDO::FETCH_ASSOC);

    if ($select_seller->rowCount() > 0) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true); // prevent session fixation on login
        $_SESSION['seller_id'] = $row['id']; // identity kept server-side, not in a client cookie
        header('location:dashboard.php');
        exit;
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
    <title>Scoop Shop - Seller Registration Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
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
        
            <input type="submit" name="submit" value="login Now" class="btn">
        </form>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/admin_script.js"></script>

    <?php include '../component/alert.php'; ?>
</body>
</html>
