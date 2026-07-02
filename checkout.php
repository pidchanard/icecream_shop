<?php 
    include 'component/connect.php';

    // Customers must be logged in (with a valid account) to checkout / place an order
    include 'component/user_auth.php';

    if (isset($_POST['place_order'])) {

        $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $number = filter_var($_POST['number'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        $address = filter_var($_POST['flat'] . ',' . $_POST['street'] . ',' . $_POST['city'] . ',' . $_POST['country'] . ',' . $_POST['pin'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $method = filter_var($_POST['method'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Fetch ALL cart items (no LIMIT) so every product in the cart becomes an order
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $verify_cart->execute([$user_id]);

        // One shared group id so all items paid together belong to the same order
        $order_group = uniqid();

        if (isset($_GET['get_id'])) {

            $get_product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
            $get_product->execute([$_GET['get_id']]);

            if ($get_product->rowCount() > 0) {
                while ($fetch_product = $get_product->fetch(PDO::FETCH_ASSOC)) {
                    if ($fetch_product['stock'] == 0) {
                        $warning_msg[] = 'This product is out of stock';
                        break;
                    }
                    $seller_id = $fetch_product['seller_id'];

                    $insert_order = $conn->prepare("INSERT INTO `orders` (id, order_group, user_id, seller_id, name, number, email, address, address_type, method, product_id, price, qty)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert_order->execute([uniqid(), $order_group, $user_id, $seller_id, $name, $number, $email, $address, $_POST['address_type'], $method, $fetch_product['id'], $fetch_product['price'], 1]);

                    header('location:order.php');
                    exit();
                }
            } else {
                $warning_msg[] = 'Something went wrong';
            }
        } elseif ($verify_cart->rowCount() > 0) {
            while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                $s_products = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                $s_products->execute([$f_cart['product_id']]);
                $f_products = $s_products->fetch(PDO::FETCH_ASSOC);

                // Skip products that are out of stock
                if (!$f_products || $f_products['stock'] == 0) {
                    continue;
                }

                $seller_id = $f_products['seller_id'];

                $insert_order = $conn->prepare("INSERT INTO `orders` (id, order_group, user_id, seller_id, name, number, email, address, address_type, method, product_id, price, qty)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_order->execute([uniqid(), $order_group, $user_id, $seller_id, $name, $number, $email, $address, $_POST['address_type'], $method, $f_cart['product_id'], $f_products['price'], $f_cart['qty']]);
            }

            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart->execute([$user_id]);

            header('location:order.php');
            exit();
        } else {
            $warning_msg[] = 'Something went wrong';
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop Shop - Checkout page</title>
    <link rel="stylesheet" type="text/css" href="css/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Checkout</h1>
            <p>Scooping ice cream requires a quick dip of the scoop in warm water to prevent sticking, 
                followed by a gentle <br>press into the ice cream. With a smooth, rotating motion of the wrist, 
                you form a neat ball that easily releases <br>into a bowl or cone for a perfect, creamy scoop.</p>
                <span><a href="home.php">Home</a><i class="bx bx-right-arrow-alt"></i>Checkout</span>
        </div>
    </div>
    <div class="checkout">
        <div class="heading">
            <h1>Checkout Summary</h1>
            <img src="image/separator-img.png">
        </div>
        <div class="row">
            <form action="" method="post" class="register">
                <h3>Billing Details</h3>
                <div class="flex">
                    <div class="box">
                        <div class="input-field">
                            <p>Your Name <span>*</span></p>
                            <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Your Number <span>*</span></p>
                            <input type="text" name="number" required maxlength="10" placeholder="Enter your number" class="input">
                        </div>
                        <div class="input-field">
                            <p>Your Email <span>*</span></p>
                            <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="input">
                        </div>
                        <div class="input-field">
                            <p>Payment Method <span>*</span></p>
                            <select name="method" class="input">
                                <option value="cash on delivery">Cash on Delivery</option>
                                <option value="credit or debit card">Credit or Debit Card</option>
                                <option value="net banking">Net Banking</option>
                                <option value="UPI or PayPal">UPI or PayPal</option>
                                <option value="paytm">Paytm</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <p>Address Type <span>*</span></p>
                            <select name="address_type" class="input">
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="input-field">
                            <p>Address Line 01<span>*</span></p>
                            <input type="text" name="flat" required maxlength="50" placeholder="Flat or building name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Address Line 02<span>*</span></p>
                            <input type="text" name="street" required maxlength="50" placeholder="e.g. street name" class="input">
                        </div>
                        <div class="input-field">
                            <p>City Name <span>*</span></p>
                            <input type="text" name="city" required maxlength="50" placeholder="e.g. city name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Country Name <span>*</span></p>
                            <input type="text" name="country" required maxlength="50" placeholder="e.g. country name" class="input">
                        </div>
                        <div class="input-field">
                            <p>Pincode <span>*</span></p>
                            <input type="number" name="pin" required maxlength="6" min="0" placeholder="e.g. 100111" class="input">
                        </div>
                    </div>
                </div>

                <!-- Dynamic payment details: shown based on the selected payment method -->
                <div class="payment-details" id="payment-details">

                    <!-- Cash on Delivery -->
                    <div class="pay-block active" data-method="cash on delivery">
                        <div class="cod-note">
                            <i class="bx bx-money"></i>
                            <p>Pay with cash when your order is delivered to your doorstep. Please keep the exact amount ready.</p>
                        </div>
                    </div>

                    <!-- Credit or Debit Card -->
                    <div class="pay-block" data-method="credit or debit card">
                        <div class="card-preview" id="card-preview">
                            <div class="card-row top">
                                <span class="chip"></span>
                                <span class="brand" id="card-brand">CARD</span>
                            </div>
                            <div class="card-number" id="preview-number">#### #### #### ####</div>
                            <div class="card-row bottom">
                                <div>
                                    <small>Card Holder</small>
                                    <div id="preview-name">FULL NAME</div>
                                </div>
                                <div>
                                    <small>Expires</small>
                                    <div id="preview-expiry">MM/YY</div>
                                </div>
                            </div>
                        </div>
                        <div class="input-field">
                            <p>Card Number <span>*</span></p>
                            <input type="text" name="card_number" inputmode="numeric" maxlength="19" placeholder="1234 5678 9012 3456" class="input pay-input" data-required>
                        </div>
                        <div class="input-field">
                            <p>Name on Card <span>*</span></p>
                            <input type="text" name="card_name" maxlength="26" placeholder="e.g. John Doe" class="input pay-input" data-required>
                        </div>
                        <div class="flex">
                            <div class="box">
                                <div class="input-field">
                                    <p>Expiry (MM/YY) <span>*</span></p>
                                    <input type="text" name="card_expiry" maxlength="5" placeholder="MM/YY" class="input pay-input" data-required>
                                </div>
                            </div>
                            <div class="box">
                                <div class="input-field">
                                    <p>CVV <span>*</span></p>
                                    <input type="password" name="card_cvv" inputmode="numeric" maxlength="4" placeholder="123" class="input pay-input" data-required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Banking -->
                    <div class="pay-block" data-method="net banking">
                        <div class="input-field">
                            <p>Select Bank <span>*</span></p>
                            <select name="bank_name" class="input pay-input" data-required>
                                <option value="">-- Choose your bank --</option>
                                <option value="Bangkok Bank">Bangkok Bank</option>
                                <option value="Kasikorn Bank">Kasikorn Bank</option>
                                <option value="Siam Commercial Bank">Siam Commercial Bank</option>
                                <option value="Krungthai Bank">Krungthai Bank</option>
                                <option value="TMBThanachart Bank">TMBThanachart Bank</option>
                            </select>
                        </div>
                        <div class="pay-qr">
                            <img alt="Scan to pay" class="qr-img">
                            <p>Scan this QR with your banking app to complete the payment.</p>
                        </div>
                    </div>

                    <!-- UPI or PayPal -->
                    <div class="pay-block" data-method="UPI or PayPal">
                        <div class="input-field">
                            <p>UPI ID / PayPal Email <span>*</span></p>
                            <input type="text" name="upi_id" maxlength="60" placeholder="e.g. name@upi or name@email.com" class="input pay-input" data-required>
                        </div>
                        <div class="pay-qr">
                            <img alt="Scan to pay" class="qr-img">
                            <p>Scan this QR with your UPI / PayPal app to complete the payment.</p>
                        </div>
                    </div>

                    <!-- Paytm -->
                    <div class="pay-block" data-method="paytm">
                        <div class="input-field">
                            <p>Paytm Mobile Number <span>*</span></p>
                            <input type="text" name="paytm_number" inputmode="numeric" maxlength="10" placeholder="Registered 10-digit number" class="input pay-input" data-required>
                        </div>
                        <div class="pay-qr">
                            <img alt="Scan to pay" class="qr-img">
                            <p>Scan this QR with your Paytm app to complete the payment.</p>
                        </div>
                    </div>

                </div>

                <button type="submit" name="place_order" value="btn" class="btn">Place Order</button>
            </form>

            <div class="summary">
                <h3>My Bag</h3>
                <div class="box-container">
                    <?php
                        $grand_total = 0; 
                        if (isset($_GET['get_id'])) {
                            $select_get = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                            $select_get->execute([$_GET['get_id']]);

                            while ($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total = $fetch_get['price'];
                                $grand_total += $sub_total;
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_get['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><?= $fetch_get['name']; ?></h3>
                            <p class="price">$<?= $fetch_get['price']; ?></p>
                        </div>
                    </div>
                    <?php 
                            }
                        } else {
                            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                            $select_cart->execute([$user_id]);

                            if ($select_cart->rowCount() > 0) {
                                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                                    $select_products->execute([$fetch_cart['product_id']]);
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);

                                    $sub_total = $fetch_products['price'] * $fetch_cart['qty'];
                                    $grand_total += $sub_total;
                    ?>
                    <div class="flex">
                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <div>
                            <h3 class="name"><?= $fetch_products['name']; ?></h3>
                            <p class="price">$<?= $fetch_products['price']; ?> <span>x<?= $fetch_cart['qty']; ?></span></p>
                        </div>
                    </div>
                    <?php 
                                }
                            } else {
                                echo '<p class="empty">Your cart is empty!</p>';
                            }
                        }
                    ?>
                </div>
                <div class="grand-total">
                    <h3>Grand Total:</h3>
                    <p>$<?= $grand_total; ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
    (function () {
        const methodSelect = document.querySelector('select[name="method"]');
        const blocks = document.querySelectorAll('.pay-block');

        // Show only the payment block matching the chosen method, and make its
        // fields required (hidden fields must NOT be required, or submit breaks).
        function syncMethod() {
            const method = methodSelect.value;
            blocks.forEach(function (block) {
                const active = block.dataset.method === method;
                block.classList.toggle('active', active);
                block.querySelectorAll('.pay-input').forEach(function (input) {
                    if (active && input.hasAttribute('data-required')) {
                        input.required = true;
                    } else {
                        input.required = false;
                    }
                });
            });
        }
        methodSelect.addEventListener('change', function () {
            syncMethod();
            buildQrCodes();
        });
        syncMethod();
        buildQrCodes();

        // Build a "scan to pay" QR for the QR-based methods. The QR encodes a real
        // URL to pay.php (on the current host), so scanning it actually opens the
        // payment page instead of just showing inert text.
        function buildQrCodes() {
            const totalEl = document.querySelector('.summary .grand-total p');
            const amount = totalEl ? totalEl.textContent.replace(/[^0-9.]/g, '') : '0';
            const ref = 'SCOOP-' + Date.now();
            // Absolute URL based on the page's current directory (works on localhost or LAN IP)
            const baseUrl = window.location.href.replace(/[^/]*$/, '');
            const payUrl = baseUrl + 'pay.php?amount=' + encodeURIComponent(amount)
                + '&ref=' + encodeURIComponent(ref)
                + '&method=' + encodeURIComponent(methodSelect.value);
            const src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(payUrl);
            document.querySelectorAll('.qr-img').forEach(function (img) {
                img.src = src;
            });
        }

        // --- Credit card field formatting + live preview ---
        const cardNumber = document.querySelector('input[name="card_number"]');
        const cardName   = document.querySelector('input[name="card_name"]');
        const cardExpiry = document.querySelector('input[name="card_expiry"]');
        const cardCvv    = document.querySelector('input[name="card_cvv"]');

        const previewNumber = document.querySelector('#preview-number');
        const previewName   = document.querySelector('#preview-name');
        const previewExpiry = document.querySelector('#preview-expiry');
        const cardBrand     = document.querySelector('#card-brand');

        if (cardNumber) {
            cardNumber.addEventListener('input', function () {
                let digits = cardNumber.value.replace(/\D/g, '').slice(0, 16);
                cardNumber.value = digits.replace(/(.{4})/g, '$1 ').trim();

                let display = digits.padEnd(16, '#');
                previewNumber.textContent = display.replace(/(.{4})/g, '$1 ').trim();

                // Basic brand detection
                if (/^4/.test(digits)) cardBrand.textContent = 'VISA';
                else if (/^5[1-5]/.test(digits)) cardBrand.textContent = 'MASTERCARD';
                else if (/^3[47]/.test(digits)) cardBrand.textContent = 'AMEX';
                else cardBrand.textContent = 'CARD';
            });
        }
        if (cardName) {
            cardName.addEventListener('input', function () {
                // Allow letters and spaces only, capped at 26 characters (real card limit)
                cardName.value = cardName.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 26);
                previewName.textContent = cardName.value.trim() === '' ? 'FULL NAME' : cardName.value.toUpperCase();
            });
        }
        if (cardExpiry) {
            cardExpiry.addEventListener('input', function () {
                let v = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
                if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
                cardExpiry.value = v;
                previewExpiry.textContent = v === '' ? 'MM/YY' : v;
            });
        }
        if (cardCvv) {
            cardCvv.addEventListener('input', function () {
                cardCvv.value = cardCvv.value.replace(/\D/g, '').slice(0, 4);
            });
        }

        // Numeric-only for Paytm number
        const paytm = document.querySelector('input[name="paytm_number"]');
        if (paytm) {
            paytm.addEventListener('input', function () {
                paytm.value = paytm.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        // Nicer popups using SweetAlert when available, falling back to alert()
        function notify(message) {
            if (typeof swal === 'function') {
                swal('Payment', message, 'warning');
            } else {
                alert(message);
            }
        }

        // Extra validation for the card method before submitting
        document.querySelector('form.register').addEventListener('submit', function (e) {
            if (methodSelect.value === 'credit or debit card') {
                const digits = cardNumber.value.replace(/\D/g, '');
                if (digits.length < 16) {
                    e.preventDefault();
                    notify('Please enter a valid 16-digit card number.');
                    return;
                }
                if (cardName.value.trim() === '') {
                    e.preventDefault();
                    notify('Please enter the name on the card.');
                    return;
                }
                if (!/^\d{2}\/\d{2}$/.test(cardExpiry.value)) {
                    e.preventDefault();
                    notify('Please enter a valid expiry date (MM/YY).');
                    return;
                }
                if (cardCvv.value.length < 3) {
                    e.preventDefault();
                    notify('Please enter a valid CVV.');
                    return;
                }
            }
        });
    })();
    </script>
</body>
</html>
