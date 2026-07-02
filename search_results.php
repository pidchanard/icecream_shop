<?php
// Live-search endpoint: returns the product result cards (HTML) for a query.
include 'component/connect.php';

$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : '';
$search_query = isset($_GET['q']) ? $_GET['q'] : '';

include 'component/search_cards.php';
