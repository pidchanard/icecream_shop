<?php
// Central customer access guard.
// Include this on pages that require a logged-in customer, AFTER connect.php.
//
// It verifies the user_id cookie actually maps to a real account. A stale or
// forged cookie value (e.g. left over after the account was removed) is rejected,
// cleared, and the visitor is sent to the login page with execution stopped, so
// customer-only pages/actions are never available to someone who is not logged in.

$user_id = $_COOKIE['user_id'] ?? '';

$is_valid_user = false;
if ($user_id !== '') {
    $verify_user = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $verify_user->execute([$user_id]);
    $is_valid_user = $verify_user->rowCount() > 0;
}

if (!$is_valid_user) {
    $user_id = '';
    setcookie('user_id', '', time() - 3600, '/'); // clear the invalid cookie
    header('Location: login.php');
    exit;
}
?>
