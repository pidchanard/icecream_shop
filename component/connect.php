<?php
// Mock data connection. This file replaces MySQL with a tiny PDO-like
// session store so the existing pages can run without starting MySQL.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$appTimezone = new DateTimeZone('Asia/Bangkok');
date_default_timezone_set($appTimezone->getName());

if (isset($_GET['reset_mock'])) {
    // Shared file store is reset by deleting the data file (re-seeded on next load)
    @unlink(dirname(__DIR__) . '/data/mock_db.json');
    unset($_SESSION['mock_db']);
    unset($_SESSION['mock_db_version']);
}

if (!class_exists('MockPDOConnection')) {
    class MockPDOConnection
    {
        private const MOCK_DB_VERSION = '2026-06-30-customer-id-c0001';

        // Data is kept in a single shared file so admin changes (add/edit/delete
        // products, stock, orders) are visible to every visitor, not just the
        // session that made them.
        private $data = [];
        private $storageFile;

        public function __construct()
        {
            $this->storageFile = dirname(__DIR__) . '/data/mock_db.json';
            $this->load();
        }

        private function load()
        {
            $dir = dirname($this->storageFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            if (is_file($this->storageFile)) {
                $decoded = json_decode((string) file_get_contents($this->storageFile), true);
                if (is_array($decoded)
                    && ($decoded['version'] ?? null) === self::MOCK_DB_VERSION
                    && isset($decoded['tables']) && is_array($decoded['tables'])) {
                    $this->data = $decoded['tables'];
                    return;
                }
            }

            // Missing / outdated file: seed fresh and persist for everyone.
            $this->data = $this->seed();
            $this->save();
        }

        public function save()
        {
            $payload = json_encode(
                ['version' => self::MOCK_DB_VERSION, 'tables' => $this->data],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            if ($payload !== false) {
                file_put_contents($this->storageFile, $payload, LOCK_EX);
            }
        }

        public function setAttribute($attribute, $value)
        {
            return true;
        }

        public function prepare($sql)
        {
            return new MockPDOStatement($this, $sql);
        }

        public function currentDateTime()
        {
            return (new DateTimeImmutable('now', new DateTimeZone('Asia/Bangkok')))->format('Y-m-d H:i:s');
        }

        public function &table($name)
        {
            if (!isset($this->data[$name])) {
                $this->data[$name] = [];
            }

            return $this->data[$name];
        }

        private function seed()
        {
            $userPassword = sha1('123456');
            $adminPassword = sha1('123456');

            return [
                'users' => [
                    [
                        'id' => 'c0001',
                        'name' => 'Demo Customer',
                        'email' => 'customer@example.com',
                        'password' => $userPassword,
                        'image' => '66dbbdc0cb771.jpg',
                    ],
                ],
                'sellers' => [
                    [
                        'id' => 'seller-demo-001',
                        'name' => 'Scoop Admin',
                        'email' => 'testAdmin@gmail.com',
                        'password' => $adminPassword,
                        'image' => '66dbbd76b3722.jpg',
                    ],
                ],
                'products' => [
                    [
                        'id' => 'prod-001',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Blueberry Bliss',
                        'price' => 79,
                        'image' => 'blue berry.png',
                        'stock' => 24,
                        'product_detail' => 'Creamy blueberry ice cream with a bright berry swirl.',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'prod-002',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Chocolate Fudge',
                        'price' => 89,
                        'image' => 'product1.jpg',
                        'stock' => 15,
                        'product_detail' => 'Rich chocolate scoop with ribbons of fudge.',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'prod-003',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Strawberry Sundae',
                        'price' => 75,
                        'image' => 'product8.jpg',
                        'stock' => 8,
                        'product_detail' => 'Fresh strawberry flavor with a smooth vanilla finish.',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'prod-004',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Vanilla Dream',
                        'price' => 69,
                        'image' => 'product9.jpg',
                        'stock' => 32,
                        'product_detail' => 'Classic vanilla bean ice cream made for everyday cravings.',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'prod-005',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Mango Sorbet',
                        'price' => 65,
                        'image' => 'product10.jpg',
                        'stock' => 0,
                        'product_detail' => 'Tropical mango sorbet, currently sold out in the mock shop.',
                        'status' => 'active',
                    ],
                    [
                        'id' => 'prod-006',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Pistachio Soft Serve',
                        'price' => 95,
                        'image' => 'product12.jpg',
                        'stock' => 12,
                        'product_detail' => 'Nutty pistachio soft serve with a silky texture.',
                        'status' => 'deactive',
                    ],
                ],
                'cart' => [
                    [
                        'id' => 'cart-demo-001',
                        'user_id' => 'c0001',
                        'product_id' => 'prod-001',
                        'price' => 79,
                        'qty' => 2,
                    ],
                ],
                'wishlist' => [
                    [
                        'id' => 'wish-demo-001',
                        'user_id' => 'c0001',
                        'product_id' => 'prod-002',
                        'price' => 89,
                    ],
                ],
                'orders' => [
                    [
                        'id' => 'order-demo-001',
                        'user_id' => 'c0001',
                        'seller_id' => 'seller-demo-001',
                        'name' => 'Demo Customer',
                        'number' => '0812345678',
                        'email' => 'customer@example.com',
                        'address' => 'Bangkok, Thailand',
                        'address_type' => 'home',
                        'method' => 'cash on delivery',
                        'product_id' => 'prod-003',
                        'price' => 75,
                        'qty' => 1,
                        'status' => 'in progress',
                        'payment_status' => 'pending',
                        'dates' => $this->currentDateTime(),
                    ],
                ],
                'message' => [
                    [
                        'id' => 'msg-demo-001',
                        'user_id' => 'c0001',
                        'name' => 'Demo Customer',
                        'email' => 'customer@example.com',
                        'subject' => 'Mock message',
                        'message' => 'This message is loaded from mock data.',
                    ],
                ],
            ];
        }

    }

    class MockPDOStatement
    {
        private $connection;
        private $sql;
        private $rows = [];
        private $affectedRows = 0;

        public function __construct($connection, $sql)
        {
            $this->connection = $connection;
            $this->sql = trim(preg_replace('/\s+/', ' ', $sql));
        }

        public function execute($params = [])
        {
            $params = array_values($params ?: []);
            $this->rows = [];
            $this->affectedRows = 0;

            if (stripos($this->sql, 'select') === 0) {
                $this->rows = $this->selectRows($params);
                $this->affectedRows = count($this->rows);
                return true;
            }

            if (stripos($this->sql, 'insert') === 0) {
                $this->insertRow($params);
                $this->affectedRows = 1;
                $this->connection->save();
                return true;
            }

            if (stripos($this->sql, 'update') === 0) {
                $this->affectedRows = $this->updateRows($params);
                $this->connection->save();
                return true;
            }

            if (stripos($this->sql, 'delete') === 0) {
                $this->affectedRows = $this->deleteRows($params);
                $this->connection->save();
                return true;
            }

            return true;
        }

        public function fetch($mode = null)
        {
            $row = array_shift($this->rows);
            return $row === null ? false : $row;
        }

        public function rowCount()
        {
            return $this->affectedRows;
        }

        private function selectRows($params)
        {
            $table = $this->extractTable('/from\s+`?([a-z_]+)`?/i');
            $rows = array_values($this->connection->table($table));
            $rows = $this->filterRows($rows, $params);

            if (stripos($this->sql, 'order by dates desc') !== false) {
                usort($rows, function ($a, $b) {
                    return strcmp($b['dates'] ?? '', $a['dates'] ?? '');
                });
            }

            if (preg_match('/limit\s+(\d+)/i', $this->sql, $match)) {
                $rows = array_slice($rows, 0, (int) $match[1]);
            }

            return $rows;
        }

        private function insertRow($params)
        {
            $table = $this->extractTable('/insert\s+into\s+`?([a-z_]+)`?/i');
            preg_match('/\((.*?)\)\s*values/i', $this->sql, $match);
            $columns = array_map(function ($column) {
                return trim(str_replace('`', '', $column));
            }, explode(',', $match[1] ?? ''));

            $row = [];
            foreach ($columns as $index => $column) {
                $row[$column] = $params[$index] ?? null;
            }

            if ($table === 'orders') {
                $row += [
                    'status' => 'in progress',
                    'payment_status' => 'pending',
                    'dates' => $this->connection->currentDateTime(),
                ];
            }

            $data =& $this->connection->table($table);
            $data[] = $row;
        }

        private function updateRows($params)
        {
            $table = $this->extractTable('/update\s+`?([a-z_]+)`?/i');
            preg_match('/set\s+(.*?)\s+where/i', $this->sql, $setMatch);
            $setColumns = [];

            foreach (explode(',', $setMatch[1] ?? '') as $assignment) {
                if (preg_match('/`?([a-z_]+)`?\s*=/i', trim($assignment), $columnMatch)) {
                    $setColumns[] = $columnMatch[1];
                }
            }

            $setValues = array_slice($params, 0, count($setColumns));
            $whereValues = array_slice($params, count($setColumns));
            $updated = 0;
            $data =& $this->connection->table($table);

            foreach ($data as &$row) {
                if (!$this->rowMatches($row, $whereValues)) {
                    continue;
                }

                foreach ($setColumns as $index => $column) {
                    $row[$column] = $setValues[$index] ?? null;
                }
                $updated++;
            }
            unset($row); // avoid a dangling reference to the last matched row

            return $updated;
        }

        private function deleteRows($params)
        {
            $table = $this->extractTable('/delete\s+from\s+`?([a-z_]+)`?/i');
            $data =& $this->connection->table($table);
            $before = count($data);

            $data = array_values(array_filter($data, function ($row) use ($params) {
                return !$this->rowMatches($row, $params);
            }));

            return $before - count($data);
        }

        private function filterRows($rows, $params)
        {
            if (stripos($this->sql, ' where ') === false) {
                return $rows;
            }

            return array_values(array_filter($rows, function ($row) use ($params) {
                return $this->rowMatches($row, $params);
            }));
        }

        private function rowMatches($row, $params)
        {
            $conditions = $this->conditions();

            if (!$conditions && stripos($this->sql, 'where user_id') !== false && count($params) > 0) {
                return (string) ($row['user_id'] ?? '') === (string) $params[0];
            }

            foreach ($conditions as $index => $condition) {
                $value = $params[$index] ?? null;
                $rowValue = (string) ($row[$condition['column']] ?? '');

                if ($condition['operator'] === '!=') {
                    if ($rowValue === (string) $value) {
                        return false;
                    }
                    continue;
                }

                if (strtolower($condition['operator']) === 'like') {
                    $needle = strtolower(str_replace('%', '', (string) $value));
                    if (strpos(strtolower($rowValue), $needle) === false) {
                        return false;
                    }
                    continue;
                }

                if ($rowValue !== (string) $value) {
                    return false;
                }
            }

            return true;
        }

        private function conditions()
        {
            if (!preg_match('/where\s+(.+?)(?:\s+order\s+by|\s+limit|$)/i', $this->sql, $match)) {
                return [];
            }

            $parts = preg_split('/\s+and\s+/i', trim($match[1]));
            $conditions = [];

            foreach ($parts as $part) {
                if (preg_match('/`?([a-z_]+)`?\s*(=|!=|like)\s*\?/i', trim($part), $conditionMatch)) {
                    $conditions[] = [
                        'column' => $conditionMatch[1],
                        'operator' => strtolower($conditionMatch[2]),
                    ];
                }
            }

            return $conditions;
        }

        private function extractTable($pattern)
        {
            if (preg_match($pattern, $this->sql, $match)) {
                return $match[1];
            }

            return '';
        }
    }
}

$conn = new MockPDOConnection();

if (!function_exists('unique_id')) {
    function unique_id()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < 20; $i++) {
            $randomString .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('next_customer_id')) {
    // Generates the next sequential customer id: c0001, c0002, c0003, ...
    // Looks at existing users and continues from the highest cNNNN value.
    function next_customer_id($conn)
    {
        $select_users = $conn->prepare("SELECT id FROM users");
        $select_users->execute([]);

        $max = 0;
        while ($row = $select_users->fetch(PDO::FETCH_ASSOC)) {
            if (preg_match('/^c(\d+)$/i', $row['id'] ?? '', $match)) {
                $max = max($max, (int) $match[1]);
            }
        }

        return 'c' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('format_order_date')) {
    function format_order_date($date)
    {
        if (empty($date)) {
            return 'No date';
        }

        $timezone = new DateTimeZone('Asia/Bangkok');
        $orderDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date, $timezone);

        if (!$orderDate) {
            $orderDate = new DateTimeImmutable($date, $timezone);
        }

        return $orderDate->setTimezone($timezone)->format('Y-m-d H:i');
    }
}
?>
