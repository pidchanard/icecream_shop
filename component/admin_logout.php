<?php
    include 'connect.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['seller_id']);
    setcookie('seller_id','' , time() - 1 ,'/'); // clear any legacy cookie
    header('location: ../admin panel/login.php');
    exit;
?>