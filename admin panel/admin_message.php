<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

include 'admin_auth.php'; // verifies the logged-in seller and exits if not authenticated
//delete message from database
if(isset($_POST['delete_msg'])){
    $delete_id = $_POST['delete_id'];
    $delete_id =filter_var($delete_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $verify_delete = $conn->prepare("SELECT *FROM message WHERE id = ?");
    $verify_delete->execute([$delete_id]);

    if($verify_delete->rowCount() > 0){
        $delete_msg = $conn->prepare("DELETE FROM message WHERE id = ?");
        $delete_msg->execute([$delete_id]);

        $success_msg[]='message deleted successfully';
    }else{
        $warning_msg[]= 'message already deleted';
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Seller dashboard Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css">
    <!-- Font -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
</head>
<body>
<div class="main-container">
    <?php include '../component/admin_header.php'; ?>
    <section class="message-container">
            <div class="heading">
                <h1>unread messages</h1>
                <img src="../image/separator-img.png">
            </div>
            <div class="box-container">
            <?php
    $select_message = $conn->prepare("SELECT * FROM message");
    $select_message->execute();

    // Pagination: 8 messages per page
    $per_page = 8;
    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

    $all_messages = [];
    while ($row = $select_message->fetch(PDO::FETCH_ASSOC)) { $all_messages[] = $row; }

    $total_messages = count($all_messages);
    $total_pages = max(1, (int) ceil($total_messages / $per_page));
    $page = min($page, $total_pages);
    $messages_page = array_slice($all_messages, ($page - 1) * $per_page, $per_page);

    if($total_messages > 0){
        foreach ($messages_page as $fetch_message){

?>
            <div class="box">
                <h3 class="name"><?=$fetch_message['name'];?></h3>
                <h4><?=$fetch_message['subject'];?></h4>
                <p><?= $fetch_message['message'];?></p>
                <form action="" method="post">
                    <input type="hidden" name="delete_id" value="<?=$fetch_message['id'];?>">
                    <input type="submit" name="delete_msg" value="delete message" class="btn" data-confirm="Delete this message?">
                </form>
            </div>
<?php
        }
    }else{
        echo'<div class="empty">
        <p>no unread message yet! </p>
        </div>';
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
        </section>
    </div>
   

    <!-- SweetAlert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="../js/admin_script.js"></script>

    <?php include '../component/alert.php'; ?>
</body>
</html>
