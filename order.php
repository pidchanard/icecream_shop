<?php 
    include 'component/connect.php';

    // Only logged-in customers can view their orders
    include 'component/user_auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - User Order page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Our Order</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">Home</a><i class="bx bx-right-arrow-alt"></i>My Orders</span>
        </div>
    </div>
    <div class="orders">
        <div class="heading">
            <h1>My Order detail</h1>
            <img src="image/separator-img.png">
        </div>
        <?php $sf = $_GET['status'] ?? ''; ?>
        <div class="order-filters">
            <a href="?" class="btn <?= $sf === '' ? 'active' : ''; ?>">All</a>
            <a href="?status=progress" class="btn <?= $sf === 'progress' ? 'active' : ''; ?>"><i class='bx bxs-truck'></i> In Progress</a>
            <a href="?status=delivered" class="btn <?= $sf === 'delivered' ? 'active' : ''; ?>"><i class='bx bxs-check-circle'></i> Delivered</a>
            <a href="?status=canceled" class="btn <?= $sf === 'canceled' ? 'active' : ''; ?>"><i class='bx bx-x-circle'></i> Canceled</a>
        </div>
        <div class="box-container">
            <?php
                // Pagination: 8 orders per page
                $per_page = 8;
                $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

                $select_order = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY dates DESC");
                $select_order->execute([$user_id]);

                // Group rows paid together (same order_group); legacy rows fall back to their own id
                $groups = [];
                $group_keys = [];
                while ($row = $select_order->fetch(PDO::FETCH_ASSOC)) {
                    $g = ($row['order_group'] ?? '') !== '' ? $row['order_group'] : $row['id'];
                    if (!isset($groups[$g])) { $groups[$g] = []; $group_keys[] = $g; }
                    $groups[$g][] = $row;
                }

                // Filter by overall status: progress / delivered / canceled (empty = all)
                $status_filter = $_GET['status'] ?? '';
                $filtered_keys = [];
                foreach ($group_keys as $g) {
                    $all_delivered = true; $all_canceled = true;
                    foreach ($groups[$g] as $it) {
                        if (($it['payment_status'] ?? 'pending') != 'order deliverd') $all_delivered = false;
                        if ($it['status'] != 'canceled') $all_canceled = false;
                    }
                    $gstatus = $all_canceled ? 'canceled' : ($all_delivered ? 'delivered' : 'progress');
                    if ($status_filter === '' || $status_filter === $gstatus) {
                        $filtered_keys[] = $g;
                    }
                }
                $group_keys = $filtered_keys;

                $status_qs = $status_filter !== '' ? 'status=' . urlencode($status_filter) . '&' : '';

                $total_orders = count($group_keys);
                $total_pages = max(1, (int) ceil($total_orders / $per_page));
                $page = min($page, $total_pages);
                $keys_page = array_slice($group_keys, ($page - 1) * $per_page, $per_page);

                if ($total_orders > 0){
                    foreach ($keys_page as $gkey){
                        $items = $groups[$gkey];
                        $first = $items[0];

                        // Aggregate the whole group
                        $grp_total = 0; $all_delivered = true; $all_canceled = true;
                        foreach ($items as $it) {
                            $grp_total += $it['price'] * $it['qty'];
                            if (($it['payment_status'] ?? 'pending') != 'order deliverd') $all_delivered = false;
                            if ($it['status'] != 'canceled') $all_canceled = false;
                        }
                        $item_count = count($items);

                        // Representative product (first item) for the card image/name
                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_products->execute([$first['product_id']]);
                        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                        if (!$fetch_products) { continue; }

                        $order_date = format_order_date($first['dates'] ?? null);

                        if ($all_canceled) {
                            $st_text = 'Canceled'; $st_color = 'red'; $st_icon = 'bx-x-circle';
                        } elseif ($all_delivered) {
                            $st_text = 'Delivered'; $st_color = 'green'; $st_icon = 'bxs-check-circle';
                        } else {
                            $st_text = 'In Progress'; $st_color = '#e67e22'; $st_icon = 'bxs-truck';
                        }
                ?>
                <div class="box" data-group="<?= htmlspecialchars($gkey); ?>"<?php if($all_canceled) {echo ' style="border:2px solid red"';} ?>>
                    <a href="view_order.php?group=<?= urlencode($gkey); ?>">
                        <img src="uploaded_files/<?= $fetch_products['image'] ?>" class="image">
                        <p class="date"><i class="bx bxs-calendar-alt"></i> <?= $order_date; ?></p>
                        <div class="content">
                            <img src="image/shape-19.png" class="shap">
                            <div class="row">
                                <h3 class="name">
                                    <?= $fetch_products['name'] ?><?php if ($item_count > 1) { ?> <span style="color:gray; font-size:1rem;">+<?= $item_count - 1; ?> more</span><?php } ?>
                                </h3>
                                <p class="price">Total: $<?= $grp_total; ?>/-</p>
                                <p class="status" data-group="<?= htmlspecialchars($gkey); ?>" style="color: <?= $st_color; ?>;">
                                    <i class='bx <?= $st_icon; ?>'></i> <?= $st_text; ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                    }
                } else {
                    echo '<p class="empty">' . ($status_filter !== '' ? 'No orders in this category' : 'No orders placed yet') . '</p>';
                }
            ?>
        </div>

        <?php if ($total_pages > 1) { ?>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $page - 1; ?>" class="btn">&laquo; Prev</a>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $i; ?>" class="btn <?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <a href="?<?= $status_qs; ?>page=<?= $page + 1; ?>" class="btn">Next &raquo;</a>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>

    <script>
    // Live status: poll the server so admin's status changes appear without a refresh
    (function () {
        function refreshOrderStatuses() {
            fetch('order_status.php', { cache: 'no-store' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    document.querySelectorAll('.status[data-group]').forEach(function (el) {
                        const info = data[el.getAttribute('data-group')];
                        if (!info) return;
                        el.style.color = info.color;
                        el.innerHTML = "<i class='bx " + info.icon + "'></i> " + info.text;

                        // update the red border on the matching card when canceled
                        const card = document.querySelector('.box[data-group="' + el.getAttribute('data-group') + '"]');
                        if (card) {
                            card.style.border = info.text === 'Canceled' ? '2px solid red' : '';
                        }
                    });
                })
                .catch(function () {});
        }

        setInterval(refreshOrderStatuses, 4000); // check every 4 seconds
    })();
    </script>

    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>
