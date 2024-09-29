<?php 
    include 'component/connect.php';

    if(isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }
   //sending message
    if(isset($_POST['send_message'])) {
        if($user_id != ''){
            $id = unique_id();
            $name = $_POST['name'];

            $email = $_POST['email'];
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            $subject = $_POST['subject'];
            $subject =filter_var($subject, FILTER_SANITIZE_STRING);

            $message = $_POST['message'];
            $message = filter_var($email, FILTER_SANITIZE_STRING);

            $verify_message = $conn->prepare("SELECT * FROM message WHERE user_id = ? AND name = ? AND email=? AND subject= ? AND message = ?");
            $verify_message->execute([$user_id, $name, $email, $subject, $message]);

            if($verify_message->rowCount() > 0) {  
                $warning_msg[]='message already exist';
            }else{
                $insert_message = $conn->prepare("INSERT INTO message(id ,user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_message->execute([$id, $user_id, $name, $email, $subject, $message]);

                $warning_msg[] = 'comment inserted successfully';
            }
        }else{
            $warning_msg[]= 'please login first';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - contact us page</title>
    <link rel="stylesheet" type="text/css"href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">;

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>contact us</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>contact us</span>
        </div>
    </div>
    <div class="services">
        <div class="heading">
            <h1>our services</h1>
            <p>Just A Few Click To Make The Reservation Online For Saving Your Time And Money</p>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/0.png">
                <div>
                <h1>free shipping fast</h1>
                <p>
                "Enjoy free shipping on all orders with fast, reliable delivery. 
                Get your favorite treats delivered straight to your door in no time!"</p>
                </div>
            </div>
            <div class="box">
                <img src="image/1.png">
                <div>
                <h1>money back & guarantee</h1>
                <p>
                "Enjoy free shipping on all orders with fast, reliable delivery. 
                Get your favorite treats delivered straight to your door in no time!"</p>
                </div>
            </div>
            <div class="box">
                <img src="image/2.png">
                <div>
                <h1>online support 24/7</h1>
                <p>
                "Enjoy free shipping on all orders with fast, reliable delivery. 
                Get your favorite treats delivered straight to your door in no time!"</p>
                </div>
            </div>
        </div>
    </div>
    <div class="form-container">
        <div class="heading">
            <h1>drop us a line</h1>
            <p>Just a few click to make the reservation online for saving your time and money</p>
            <img src="image/separator-img.png">
        </div>
    </div>
    <div class="form-container">
        <form action="" method="post" class="register">
            <div class="input-field">
                <label>name <sup>*</sup></label>
                <input type="text" name="name" required placeholder="enter your name" class="box">
            </div>
            <div class="input-field">
                <label>email <sup>*</sup></label>
                <input type="email" name="email" required placeholder="enter your email" class="box">
            </div>
            <div class="input-field">
                <label>subject <sup>*</sup></label>
                <input type="text" name="subject" required placeholder="reason..." class="box">
            </div>
            <div class="input-field">
                <label>comment<sup>*</sup></label>
               <textarea name="message" cols="30" rows="10" required placeholder="" class="box"></textarea>
            </div>
            <button type="submit" name="send_message" class="btn">send message</button>
        </form>
    </div>
    <div class="address">
        <div class="heading">
            <h1>our contact details</h1>
            <p>Just A Few Click To Make The Reservation Online For Saving Your Time And Money</p>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container">
            <div class="box">
                <i class="bx bxs-map-alt"></i>&nbsp;
                <div>
                    <h4>address</h4>
                    <p>Kasetsart University, Sriracha Campus, 199 Moo 6,<br>
                        Tungsukla Subdistrict, Chonburi 20230,Thailand</p>
                </div>
            </div>
            <div class="box">
                <i class="bx bxs-phone-incoming"></i>
                <div>
                    <h4>phone number</h4>
                    <p>012-345-6789</p>
                    <p>012-345-6789</p>
                </div>
            </div>  
            <div class="box">
                <i class="bx bxs-envelope"></i>
                <div>
                    <h4>email</h4>
                    <p>pidchanard.m@ku.th</p>
                    <p>gidjung120347@gmail.com</p>
                </div>
            </div>  
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>



    <?php include 'component/alert.php'; ?>

    <?php include 'component/footer.php'; ?>
</body>
</html>