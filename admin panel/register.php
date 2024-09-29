<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

if (isset($_POST['submit'])) {
    $id = unique_id(); // ใช้ฟังก์ชัน unique_id() ที่ประกาศใน connect.php
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = sha1($_POST['pass']); // แปลงรหัสผ่านเป็น sha1
    $cpass = sha1($_POST['cpass']); // แปลงรหัสผ่านยืนยันเป็น sha1

    $image = $_FILES['image']['name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext; // ตั้งชื่อไฟล์ใหม่
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/' . $rename;

    // ตรวจสอบอีเมลในฐานข้อมูล
    $select_seller = $conn->prepare("SELECT * FROM sellers WHERE email = ?");
    $select_seller->execute([$email]);

    if ($select_seller->rowCount() > 0) {
        $warning_msg[] = 'Email already exists!';
    } else {
        if ($pass != $cpass) {
            $warning_msg[] = 'Confirm password does not match';
        } else {
            // เพิ่มข้อมูลผู้ขายใหม่ในฐานข้อมูล
            $insert_seller = $conn->prepare("INSERT INTO sellers (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
            $insert_seller->execute([$id, $name, $email, $pass, $rename]);
            move_uploaded_file($image_tmp_name, $image_folder); // ย้ายไฟล์อัปโหลดไปยังโฟลเดอร์ที่กำหนด
            $success_msg[] = 'New seller registered! Please log in now';
        }
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
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Register Now</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>@Name <span>*</span></p>
                        <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
                    </div>
                    <div class="input-field">
                        <p>@Email <span>*</span></p>
                        <input type="email" name="email" placeholder="Enter your email" maxlength="50" required class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>@Password <span>*</span></p>
                        <input type="password" name="pass" placeholder="Enter your password" maxlength="50" required class="box">
                    </div>
                    <div class="input-field">
                        <p>@Confirm Password <span>*</span></p>
                        <input type="password" name="cpass" placeholder="Confirm your password" maxlength="50" required class="box">
                    </div>
                </div>
            </div>
            <div class="input-field">
                <p>Your Profile <span>*</span></p>
                <input type="file" name="image" accept="image/*" required class="box">
            </div>
            <p class="link">Already have an account? <a href="login.php">Login now</a></p>
            <input type="submit" name="submit" value="Register Now" class="btn">
        </form>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/admin_script.js"></script>

    <?php include '../component/alert.php'; ?>
</body>
</html>
