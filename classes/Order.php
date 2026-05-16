<?php

require_once __DIR__ . '/Database.php';

/**
 * Order management — MySQL backend
 */
class Order {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    private function hydrateShipping(array $row) {
        $addr = $row['ship_address'];
        $cost = (int) $row['shipping_cost'];
        return [
            'first_name' => $row['ship_first_name'],
            'last_name' => $row['ship_last_name'],
            'email' => $row['ship_email'],
            'phone' => $row['ship_phone'],
            'address' => $addr,
            'street' => $addr,
            'city' => $row['ship_city'],
            'postal_code' => $row['ship_postal_code'],
            'country' => 'Sri Lanka',
            'shipping_method' => $row['shipping_method'],
            'shipping_cost' => $cost,
        ];
    }

    private function hydrateItem(array $row) {
        $price = (int) $row['price'];
        $qty = (int) $row['quantity'];
        $productId = isset($row['product_id']) && $row['product_id'] !== null
            ? (int) $row['product_id']
            : null;
        return [
            'id' => $productId ?? (int) $row['id'],
            'name' => $row['name'],
            'price' => $price,
            'image' => $row['image'] ?? '',
            'size' => $row['size'] ?? '',
            'quantity' => $qty,
            'subtotal' => $price * $qty,
        ];
    }

    private function hydrateOrder(array $orderRow, array $items) {
        $out = [
            'id' => (int) $orderRow['id'],
            'user_id' => $orderRow['user_id'] !== null && $orderRow['user_id'] !== ''
                ? (int) $orderRow['user_id']
                : null,
            'items' => $items,
            'total_amount' => (int) $orderRow['total_amount'],
            'status' => $orderRow['status'],
            'payment_method' => $orderRow['payment_method'],
            'payment_status' => $orderRow['payment_status'],
            'shipping_address' => $this->hydrateShipping($orderRow),
            'order_date' => $orderRow['order_date'],
            'shipped_date' => $orderRow['shipped_date'],
            'delivered_date' => $orderRow['delivered_date'],
            'tracking_number' => $orderRow['tracking_number'],
            'notes' => $orderRow['notes'] ?? '',
        ];
        return $out;
    }

    private function fetchItemsByOrderIds(array $orderIds) {
        if (empty($orderIds)) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT id, order_id, product_id, name, price, image, size, quantity
             FROM order_items WHERE order_id IN ($ph) ORDER BY order_id, id"
        );
        $stmt->execute($orderIds);
        $byOrder = [];
        while ($row = $stmt->fetch()) {
            $oid = (int) $row['order_id'];
            if (!isset($byOrder[$oid])) {
                $byOrder[$oid] = [];
            }
            $byOrder[$oid][] = $this->hydrateItem($row);
        }
        return $byOrder;
    }

    private function orderSelectColumns() {
        return 'id, user_id, total_amount, status, payment_method, payment_status, order_date,
            shipped_date, delivered_date, tracking_number, notes,
            ship_first_name, ship_last_name, ship_email, ship_phone, ship_address, ship_city,
            ship_postal_code, shipping_method, shipping_cost';
    }

    public function getAll() {
        $stmt = $this->pdo->query(
            'SELECT ' . $this->orderSelectColumns() . ' FROM orders ORDER BY order_date DESC'
        );
        $orders = $stmt->fetchAll();
        $ids = array_column($orders, 'id');
        $itemsMap = $this->fetchItemsByOrderIds($ids);
        $out = [];
        foreach ($orders as $row) {
            $oid = (int) $row['id'];
            $out[] = $this->hydrateOrder($row, $itemsMap[$oid] ?? []);
        }
        return $out;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare(
            'SELECT ' . $this->orderSelectColumns() . ' FROM orders WHERE id = ?'
        );
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $itemsMap = $this->fetchItemsByOrderIds([(int) $row['id']]);
        $oid = (int) $row['id'];
        return $this->hydrateOrder($row, $itemsMap[$oid] ?? []);
    }

    public function add($userId, $items, $totalAmount, $shippingAddress, $paymentMethod = 'cash') {
        $a = $shippingAddress;
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (
                    user_id, total_amount, status, payment_method, payment_status,
                    order_date, shipped_date, delivered_date, tracking_number, notes,
                    ship_first_name, ship_last_name, ship_email, ship_phone, ship_address,
                    ship_city, ship_postal_code, shipping_method, shipping_cost
                ) VALUES (
                    ?, ?, ?, ?, ?, NOW(), NULL, NULL, NULL, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )'
            );
            $stmt->execute([
                $userId !== null && $userId !== '' ? (int) $userId : null,
                (int) $totalAmount,
                'pending',
                $paymentMethod,
                'pending',
                '',
                $a['first_name'],
                $a['last_name'],
                $a['email'],
                $a['phone'],
                $a['address'],
                $a['city'],
                $a['postal_code'],
                $a['shipping_method'],
                (int) $a['shipping_cost'],
            ]);
            $orderId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, name, price, image, size, quantity)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($items as $item) {
                $pid = isset($item['id']) ? (int) $item['id'] : null;
                $exists = false;
                if ($pid) {
                    $chk = $this->pdo->prepare('SELECT id FROM products WHERE id = ?');
                    $chk->execute([$pid]);
                    $exists = (bool) $chk->fetch();
                }
                $itemStmt->execute([
                    $orderId,
                    $exists ? $pid : null,
                    $item['name'] ?? 'Product',
                    (int) ($item['price'] ?? 0),
                    $item['image'] ?? '',
                    $item['size'] ?? '',
                    max(1, (int) ($item['quantity'] ?? 1)),
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function updateStatus($id, $status, $notes = '') {
        $stmt = $this->pdo->prepare(
            'UPDATE orders SET
                status = ?,
                notes = ?,
                shipped_date = IF(? = \'shipped\' AND shipped_date IS NULL, NOW(), shipped_date),
                delivered_date = IF(? = \'delivered\' AND delivered_date IS NULL, NOW(), delivered_date)
             WHERE id = ?'
        );
        $stmt->execute([$status, $notes, $status, $status, (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function updatePaymentStatus($id, $paymentStatus) {
        $stmt = $this->pdo->prepare(
            'UPDATE orders SET payment_status = ? WHERE id = ?'
        );
        $stmt->execute([$paymentStatus, (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateTracking($id, $trackingNumber) {
        $stmt = $this->pdo->prepare(
            'UPDATE orders SET tracking_number = ? WHERE id = ?'
        );
        $stmt->execute([$trackingNumber, (int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM orders WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function getByStatus($status) {
        $stmt = $this->pdo->prepare(
            'SELECT ' . $this->orderSelectColumns() . ' FROM orders WHERE status = ? ORDER BY order_date DESC'
        );
        $stmt->execute([$status]);
        $orders = $stmt->fetchAll();
        $ids = array_column($orders, 'id');
        $itemsMap = $this->fetchItemsByOrderIds($ids);
        $out = [];
        foreach ($orders as $row) {
            $oid = (int) $row['id'];
            $out[] = $this->hydrateOrder($row, $itemsMap[$oid] ?? []);
        }
        return $out;
    }

    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare(
            'SELECT ' . $this->orderSelectColumns() . ' FROM orders WHERE user_id = ? ORDER BY order_date DESC'
        );
        $stmt->execute([(int) $userId]);
        $orders = $stmt->fetchAll();
        $ids = array_column($orders, 'id');
        $itemsMap = $this->fetchItemsByOrderIds($ids);
        $out = [];
        foreach ($orders as $row) {
            $oid = (int) $row['id'];
            $out[] = $this->hydrateOrder($row, $itemsMap[$oid] ?? []);
        }
        return $out;
    }

    public function getPending() {
        return $this->getByStatus('pending');
    }

    public function getShipped() {
        return $this->getByStatus('shipped');
    }

    public function getDelivered() {
        return $this->getByStatus('delivered');
    }

    public function getCancelled() {
        return $this->getByStatus('cancelled');
    }

    public function getByDateRange($startDate, $endDate) {
        $stmt = $this->pdo->prepare(
            'SELECT ' . $this->orderSelectColumns() . ' FROM orders
             WHERE DATE(order_date) >= ? AND DATE(order_date) <= ? ORDER BY order_date DESC'
        );
        $stmt->execute([$startDate, $endDate]);
        $orders = $stmt->fetchAll();
        $ids = array_column($orders, 'id');
        $itemsMap = $this->fetchItemsByOrderIds($ids);
        $out = [];
        foreach ($orders as $row) {
            $oid = (int) $row['id'];
            $out[] = $this->hydrateOrder($row, $itemsMap[$oid] ?? []);
        }
        return $out;
    }

    public function getTotalRevenue($status = null) {
        if ($status === null) {
            $stmt = $this->pdo->query(
                "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = ? AND payment_status = 'paid'"
            );
            $stmt->execute([$status]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Link guest orders (null user_id) to users using shipping email.
     */
    public function backfillMissingUserIds() {
        require_once __DIR__ . '/User.php';

        $stmt = $this->pdo->query(
            'SELECT id, ship_first_name, ship_last_name, ship_email
             FROM orders WHERE user_id IS NULL'
        );
        $user = new User();
        $updated = 0;

        while ($row = $stmt->fetch()) {
            $name = trim($row['ship_first_name'] . ' ' . $row['ship_last_name']);
            $userId = $user->findOrCreateFromCheckout($name, $row['ship_email']);
            if ($userId) {
                $upd = $this->pdo->prepare('UPDATE orders SET user_id = ? WHERE id = ?');
                $upd->execute([$userId, (int) $row['id']]);
                $updated++;
            }
        }

        return $updated;
    }

    public function getStatistics() {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $pending = (int) $this->pdo->query(
            "SELECT COUNT(*) FROM orders WHERE status = 'pending'"
        )->fetchColumn();
        $shipped = (int) $this->pdo->query(
            "SELECT COUNT(*) FROM orders WHERE status = 'shipped'"
        )->fetchColumn();
        $delivered = (int) $this->pdo->query(
            "SELECT COUNT(*) FROM orders WHERE status = 'delivered'"
        )->fetchColumn();
        $cancelled = (int) $this->pdo->query(
            "SELECT COUNT(*) FROM orders WHERE status = 'cancelled'"
        )->fetchColumn();

        return [
            'total_orders' => $total,
            'pending' => $pending,
            'shipped' => $shipped,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }
}
