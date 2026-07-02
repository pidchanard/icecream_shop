<?php
// Central admin access guard.
// Include this on every admin page AFTER connect.php.
//
// It rejects any visitor who is not a logged-in seller and STOPS execution
// with exit, so admin markup/data is never rendered for unauthenticated users.
// Identity lives in the server-side session (not a client cookie), so it
// cannot be forged by simply setting a cookie value in the browser.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$seller_id = $_SESSION['seller_id'] ?? '';

$is_valid_seller = false;
if ($seller_id !== '') {
    // Confirm the session still maps to a real seller account.
    $verify_seller = $conn->prepare("SELECT id FROM sellers WHERE id = ? LIMIT 1");
    $verify_seller->execute([$seller_id]);
    $is_valid_seller = $verify_seller->rowCount() > 0;
}

if (!$is_valid_seller) {
    $seller_id = '';
    unset($_SESSION['seller_id']);
    setcookie('seller_id', '', time() - 3600, '/'); // clear any legacy cookie
    header('Location: login.php');
    exit;
}
?>