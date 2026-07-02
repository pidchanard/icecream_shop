<?php
include '../component/connect.php'; // นำเข้าฟังก์ชัน unique_id() และการเชื่อมต่อฐานข้อมูล

include 'admin_auth.php'; // verifies the logged-in seller and exits if not authenticated

    // Resolve every order row that belongs to a group key (order_group, or a single id for legacy rows)
    function rows_in_group($conn, $seller_id, $group_key) {
        $rows = [];
        $byGroup = $conn->prepare("SELECT * FROM orders WHERE order_group = ? AND seller_id = ?");
        $byGroup->execute([$group_key, $seller_id]);
        while ($r = $byGroup->fetch(PDO::FETCH_ASSOC)) { $rows[] = $r; }
        if (empty($rows)) {
            $byId = $conn->prepare("SELECT * FROM orders WHERE id = ? AND seller_id = ?");
            $byId->execute([$group_key, $seller_id]);
            while ($r = $byId->fetch(PDO::FETCH_ASSOC)) { $rows[] = $r; }
        }
        return $rows;
    }

//update payment for the whole order (all items in the group)
    if(isset($_POST['update_order'])){
        $group_key = filter_var($_POST['group_key'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $update_payment = filter_var($_POST['update_payment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $rows = rows_in_group($conn, $seller_id, $group_key);
        foreach ($rows as $r) {
            $update_pay = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            $update_pay->execute([$update_payment, $r['id']]);
        }
        $success_msg[] = 'order payment status updated';
    }
    //delete the whole order (all items in the group)
    if(isset($_POST['delete_order'])){
        $group_key = filter_var($_POST['group_key'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $rows = rows_in_group($conn, $seller_id, $group_key);
        if(!empty($rows)){
            foreach ($rows as $r) {
                $delete_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $delete_order->execute([$r['id']]);
            }
            $success_msg[] = 'order deleted';
        }else{
            $warning_msg[] = 'order already deleted';
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
    <?php
        // Filter orders by the dashboard button that was clicked
        $filter = $_GET['filter'] ?? '';
        if ($filter == 'confirm') {
            $select_order = $conn->prepare("SELECT * FROM orders WHERE seller_id = ? AND status = ?");
            $select_order->execute([$seller_id, 'in progress']);
            $orders_heading = 'confirm orders';
        } elseif ($filter == 'canceled') {
            $select_order = $conn->prepare("SELECT * FROM orders WHERE seller_id = ? AND status = ?");
            $select_order->execute([$seller_id, 'canceled']);
            $orders_heading = 'canceled orders';
        } else {
            $select_order = $conn->prepare("SELECT * FROM orders WHERE seller_id = ?");
            $select_order->execute([$seller_id]);
            $orders_heading = 'total orders placed';
        }

        // Group items paid together (same order_group); legacy rows fall back to their own id
        $groups = [];
        $group_keys = [];
        while ($row = $select_order->fetch(PDO::FETCH_ASSOC)) {
            $g = ($row['order_group'] ?? '') !== '' ? $row['order_group'] : $row['id'];
            if (!isset($groups[$g])) { $groups[$g] = []; $group_keys[] = $g; }
            $groups[$g][] = $row;
        }

        // Pagination: 4 orders per page (one order = one group)
        $per_page = 4;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $total_orders = count($group_keys);
        $total_pages = max(1, (int) ceil($total_orders / $per_page));
        $page = min($page, $total_pages);
        $keys_page = array_slice($group_keys, ($page - 1) * $per_page, $per_page);

        // Keep the current filter in pagination links
        $filter_qs = $filter !== '' ? 'filter=' . urlencode($filter) . '&' : '';
    ?>
    <section class="order-container">
            <div class="heading">
                <h1><?= $orders_heading; ?></h1>
                <img src="../image/separator-img.png">
            </div>
            <div class="box-container">
            <?php
                if($total_orders > 0){
                    foreach($keys_page as $gkey){
                        $items = $groups[$gkey];
                        $first = $items[0];

                        $grp_total = 0; $all_delivered = true; $all_canceled = true;
                        foreach ($items as $it) {
                            $grp_total += $it['price'] * $it['qty'];
                            if (($it['payment_status'] ?? 'pending') != 'order deliverd') $all_delivered = false;
                            if ($it['status'] != 'canceled') $all_canceled = false;
                        }
                        $grp_status = $all_canceled ? 'canceled' : 'in progress';
                        $grp_payment = $all_delivered ? 'order deliverd' : 'pending';
            ?>
            <div class="box">
                <div class="status" style="color: <?php echo $grp_status=='in progress' ? 'limegreen' : 'red'; ?>"><?= $grp_status; ?></div>
            <div class="details">
                <p>user name : <span><?=$first['name'];?></span></p>
                <p>user id : <span><?=$first['user_id'];?></span></p>
                <p>placed on: <span><?=format_order_date($first['dates'] ?? null);?></span></p>
                <p>user number : <span><?=$first['number'];?></span></p>
                <p>user email : <span><?=$first['email'];?></span></p>
                <p>payment method : <span><?=$first['method'];?></span></p>
                <p>user address : <span><?=$first['address'];?></span></p>
                <p>items :</p>
                <?php foreach ($items as $it) {
                    $sp = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
                    $sp->execute([$it['product_id']]);
                    $p = $sp->fetch(PDO::FETCH_ASSOC);
                    $pname = $p ? $p['name'] : 'product';
                ?>
                <p style="padding-left:1.5rem;">&bull; <span><?= $pname; ?> x<?= $it['qty']; ?> ($<?= $it['price'] * $it['qty']; ?>)</span></p>
                <?php } ?>
                <p>total price : <span>$<?= $grp_total; ?></span></p>
            </div>
            <form action="" method="post">
                <input type="hidden" name="group_key" value="<?= htmlspecialchars($gkey); ?>" >
                <select name="update_payment" class="box" style="width:90%; ">
                    <option value="pending" <?= $grp_payment == 'pending' ? 'selected' : ''; ?>>pending</option>
                    <option value="order deliverd" <?= $grp_payment == 'order deliverd' ? 'selected' : ''; ?>>order deliverd</option>
                </select>
                <div class="flex-btn">
                    <input type="submit" name="update_order" value="update payment" class="btn">
                    <input type="submit" name="delete_order" value="delete order" class="btn"
                    data-confirm="Delete this order?">
                </div>
            </form>
            </div>
            <?php
                    }
                }else{
                    echo'<div class="empty">
                    <p>no order placed yet! </p>
                </div>';
                }
            ?>
        </div>

        <?php if ($total_pages > 1) { ?>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?<?= $filter_qs; ?>page=<?= $page - 1; ?>" class="btn">&laquo; Prev</a>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?<?= $filter_qs; ?>page=<?= $i; ?>" class="btn <?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <a href="?<?= $filter_qs; ?>page=<?= $page + 1; ?>" class="btn">Next &raquo;</a>
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
