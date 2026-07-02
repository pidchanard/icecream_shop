<?php
    // Mock payment page opened when a customer scans the checkout QR code.
    $amount = isset($_GET['amount']) ? preg_replace('/[^0-9.]/', '', $_GET['amount']) : '0';
    $ref    = isset($_GET['ref']) ? preg_replace('/[^A-Za-z0-9\-]/', '', $_GET['ref']) : '';
    $method = isset($_GET['method']) ? htmlspecialchars($_GET['method']) : 'QR Payment';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Payment</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .pay-page{
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('image/bg1.webp');
            background-size: cover;
            padding: 2rem;
        }
        .pay-card{
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            max-width: 26rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .pay-card .check{
            font-size: 4.5rem;
            color: #2ecc71;
        }
        .pay-card h1{ font-size: 1.8rem; color: var(--main-color); margin: .5rem 0 1.5rem; }
        .pay-card .amount{ font-size: 2.6rem; font-weight: bold; color: #000; }
        .pay-card .row{
            display: flex; justify-content: space-between;
            font-size: 1.1rem; color: gray;
            padding: .7rem 0; border-bottom: 1px solid #eee;
        }
        .pay-card .row span{ color: #000; font-weight: bold; text-transform: capitalize; }
        .pay-card .note{ font-size: 1rem; color: gray; margin: 1.2rem 0; }
        .pay-card .btn{ display: inline-block; width: 100%; margin-top: .5rem; }
    </style>
</head>
<body>
    <div class="pay-page">
        <div class="pay-card">
            <i class='bx bxs-check-circle check'></i>
            <h1>Confirm Your Payment</h1>
            <p class="amount">$<?= htmlspecialchars($amount); ?></p>
            <div style="margin-top:1.5rem;">
                <div class="row">Payee <span>Scoop Shop</span></div>
                <div class="row">Method <span><?= $method; ?></span></div>
                <?php if ($ref !== '') { ?>
                    <div class="row">Reference <span><?= htmlspecialchars($ref); ?></span></div>
                <?php } ?>
            </div>
            <p class="note">Scan successful! Review the details above and confirm to complete your payment.</p>
            <a href="order.php" class="btn">Pay Now</a>
        </div>
    </div>
</body>
</html>
