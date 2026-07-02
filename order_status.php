<?php
// Live order-status endpoint: returns the current status of each of the user's
// orders (grouped by order_group) as JSON, so the My Orders page can update
// without a full refresh.
include 'component/connect.php';

header('Content-Type: application/json');

$user_id = $_COOKIE['user_id'] ?? '';
if ($user_id === '') {
    echo json_encode([]);
    exit;
}

$select_order = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$select_order->execute([$user_id]);

// Group rows paid together (same order_group); legacy rows fall back to their own id
$groups = [];
while ($row = $select_order->fetch(PDO::FETCH_ASSOC)) {
    $g = ($row['order_group'] ?? '') !== '' ? $row['order_group'] : $row['id'];
    $groups[$g][] = $row;
}

$result = [];
foreach ($groups as $g => $items) {
    $all_delivered = true;
    $all_canceled = true;
    foreach ($items as $it) {
        if (($it['payment_status'] ?? 'pending') != 'order deliverd') $all_delivered = false;
        if ($it['status'] != 'canceled') $all_canceled = false;
    }

    if ($all_canceled) {
        $result[$g] = ['text' => 'Canceled', 'color' => 'red', 'icon' => 'bx-x-circle'];
    } elseif ($all_delivered) {
        $result[$g] = ['text' => 'Delivered', 'color' => 'green', 'icon' => 'bxs-check-circle'];
    } else {
        $result[$g] = ['text' => 'In Progress', 'color' => '#e67e22', 'icon' => 'bxs-truck'];
    }
}

echo json_encode($result);
