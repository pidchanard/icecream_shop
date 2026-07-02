<?php 
    include 'component/connect.php';

    // Only logged-in customers can view an order's detail
    include 'component/user_auth.php';

    // View by order group (new) or a single order id (legacy / buy-now links)
    $group_id = $_GET['group'] ?? '';
    $single_id = $_GET['get_id'] ?? '';

    if ($group_id === '' && $single_id === '') {
        header('location:order.php');
        exit();
    }

    // Load every row of this order (a group can contain several products)
    if ($group_id !== '') {
        $select_order = $conn->prepare("SELECT * FROM orders WHERE order_group = ? AND user_id = ?");
        $select_order->execute([$group_id, $user_id]);
    } else {
        $select_order = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $select_order->execute([$single_id, $user_id]);
    }

    $items = [];
    while ($r = $select_order->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $r;
    }

    if (empty($items)) {
        header('location:order.php');
        exit();
    }

    // Cancel the whole order (only items that are not delivered yet)
    if (isset($_POST['cancle'])) {
        foreach ($items as $it) {
            if (($it['payment_status'] ?? 'pending') != 'order deliverd') {
                $update_order = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $update_order->execute(['canceled', $it['id']]);
            }
        }
        header('location:order.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Order Detail page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Order Detail</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Order Detail</span>
        </div>
    </div>

    <div class="order-detail">
        <div class="heading">
            <h1>My Order Detail</h1>
            <p> Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            <img src="image/separator-img.png">
        </div>
        <div class="box-container">
            <?php
                $first = $items[0];
                $grand_total = 0; $all_delivered = true; $all_canceled = true;
                foreach ($items as $it) {
                    $grand_total += $it['price'] * $it['qty'];
                    if (($it['payment_status'] ?? 'pending') != 'order deliverd') $all_delivered = false;
                    if ($it['status'] != 'canceled') $all_canceled = false;
                }

                if ($all_canceled) {
                    $st_text = 'Canceled'; $st_color = 'red'; $st_icon = 'bx-x-circle';
                } elseif ($all_delivered) {
                    $st_text = 'Delivered'; $st_color = 'green'; $st_icon = 'bxs-check-circle';
                } else {
                    $st_text = 'In Progress'; $st_color = '#e67e22'; $st_icon = 'bxs-truck';
                }
            ?>
            <div class="box">
                <div class="col">
                    <p class="title"><i class="bx bxs-calendar-alt"></i><?= format_order_date($first['dates'] ?? null); ?></p>
                    <?php foreach ($items as $it) {
                        $select_product = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
                        $select_product->execute([$it['product_id']]);
                        $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
                        if (!$fetch_product) { continue; }
                    ?>
                    <div class="order-item">
                        <img src="uploaded_files/<?= $fetch_product['image']; ?>">
                        <div class="info">
                            <h3 class="name"><?= $fetch_product['name']; ?></h3>
                            <span class="qty">$<?= $it['price']; ?> &times; <?= $it['qty']; ?></span>
                        </div>
                        <span class="line-total">$<?= $it['price'] * $it['qty']; ?></span>
                    </div>
                    <?php } ?>
                    <p class="grand-total">Total amount payable: <span><?= $grand_total; ?>$</span></p>
                </div>
                <div class="col">
                    <p class="title">Billing Address</p>
                    <p class="user"><i class="bi bi-person-bounding-box"></i><?= $first['name']; ?></p>
                    <p class="user"><i class="bi bi-phone"></i><?= $first['number']; ?></p>
                    <p class="user"><i class="bi bi-envelope"></i><?= $first['email']; ?></p>
                    <p class="user"><i class="bi bi-pin-map-fill"></i><?= $first['address']; ?></p>
                    <p class="user"><i class='bx bxs-credit-card'></i><?= $first['method']; ?></p>
                    <?php $gkey = ($first['order_group'] ?? '') !== '' ? $first['order_group'] : $first['id']; ?>
                    <p class="status" data-group="<?= htmlspecialchars($gkey); ?>" style="color: <?= $st_color; ?>;">
                        <i class='bx <?= $st_icon; ?>'></i> <?= $st_text; ?>
                    </p>
                    <?php if ($all_canceled || $all_delivered) { ?>
                        <a href="<?= count($items) > 1 ? 'menu.php' : 'checkout.php?get_id=' . urlencode($first['product_id']); ?>" class="btn" style="line-height: 3;">
                            Order Again
                        </a>
                    <?php } else { ?>
                    <form action="" method="post">
                        <button type="submit" name="cancle" class="btn" onclick="return confirm('Do you want to cancel this order?');">Cancel</button>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/user_script.js"></script>

    <script>
    // Live status: reflect admin's delivery-status changes without a refresh
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
                    });
                })
                .catch(function () {});
        }
        setInterval(refreshOrderStatuses, 4000);
    })();
    </script>

    <?php include 'component/alert.php'; ?>
    <?php include 'component/footer.php'; ?>
</body>
</html>
