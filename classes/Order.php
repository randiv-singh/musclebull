<?php

/**
 * Order Class
 * Simple order management for school project
 */
class Order {
    private $jsonFile;
    private $orders;

    public function __construct() {
        // Support being called from both root and subdirectories
        if (file_exists(__DIR__ . '/../config/orders.json')) {
            $this->jsonFile = __DIR__ . '/../config/orders.json';
        } else {
            $this->jsonFile = 'config/orders.json';
        }
        $this->loadOrders();
    }

    private function loadOrders() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->orders = json_decode($jsonContent, true) ?: [];
        } else {
            $this->orders = [];
        }
    }

    private function saveOrders() {
        $jsonContent = json_encode($this->orders, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get all orders
    public function getAll() {
        return $this->orders;
    }

    // Get one order by ID
    public function getById($id) {
        foreach ($this->orders as $order) {
            if ($order['id'] == $id) {
                return $order;
            }
        }
        return null;
    }

    // Add new order
    public function add($userId, $items, $totalAmount, $shippingAddress, $paymentMethod = 'cash') {
        // Get next ID
        $maxId = 0;
        foreach ($this->orders as $order) {
            if ($order['id'] > $maxId) {
                $maxId = $order['id'];
            }
        }
        
        $newOrder = [
            'id' => $maxId + 1,
            'user_id' => $userId,
            'items' => $items,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'shipping_address' => $shippingAddress,
            'order_date' => date('Y-m-d H:i:s'),
            'shipped_date' => null,
            'delivered_date' => null,
            'tracking_number' => null,
            'notes' => ''
        ];

        $this->orders[] = $newOrder;
        if ($this->saveOrders()) {
            return $newOrder['id'];
        }
        return false;
    }

    // Update order status
    public function updateStatus($id, $status, $notes = '') {
        for ($i = 0; $i < count($this->orders); $i++) {
            if ($this->orders[$i]['id'] == $id) {
                $this->orders[$i]['status'] = $status;
                $this->orders[$i]['notes'] = $notes;
                
                // Update dates based on status
                if ($status === 'shipped' && $this->orders[$i]['shipped_date'] === null) {
                    $this->orders[$i]['shipped_date'] = date('Y-m-d H:i:s');
                } elseif ($status === 'delivered' && $this->orders[$i]['delivered_date'] === null) {
                    $this->orders[$i]['delivered_date'] = date('Y-m-d H:i:s');
                }
                
                return $this->saveOrders();
            }
        }
        return false;
    }

    // Update payment status
    public function updatePaymentStatus($id, $paymentStatus) {
        for ($i = 0; $i < count($this->orders); $i++) {
            if ($this->orders[$i]['id'] == $id) {
                $this->orders[$i]['payment_status'] = $paymentStatus;
                return $this->saveOrders();
            }
        }
        return false;
    }

    // Update tracking number
    public function updateTracking($id, $trackingNumber) {
        for ($i = 0; $i < count($this->orders); $i++) {
            if ($this->orders[$i]['id'] == $id) {
                $this->orders[$i]['tracking_number'] = $trackingNumber;
                return $this->saveOrders();
            }
        }
        return false;
    }

    // Delete order
    public function delete($id) {
        for ($i = 0; $i < count($this->orders); $i++) {
            if ($this->orders[$i]['id'] == $id) {
                array_splice($this->orders, $i, 1);
                return $this->saveOrders();
            }
        }
        return false;
    }

    // Get orders by status
    public function getByStatus($status) {
        $result = [];
        foreach ($this->orders as $order) {
            if ($order['status'] === $status) {
                $result[] = $order;
            }
        }
        return $result;
    }

    // Get orders by user ID
    public function getByUserId($userId) {
        $result = [];
        foreach ($this->orders as $order) {
            if ($order['user_id'] == $userId) {
                $result[] = $order;
            }
        }
        return $result;
    }

    // Get pending orders
    public function getPending() {
        return $this->getByStatus('pending');
    }

    // Get shipped orders
    public function getShipped() {
        return $this->getByStatus('shipped');
    }

    // Get delivered orders
    public function getDelivered() {
        return $this->getByStatus('delivered');
    }

    // Get cancelled orders
    public function getCancelled() {
        return $this->getByStatus('cancelled');
    }

    // Get orders by date range
    public function getByDateRange($startDate, $endDate) {
        $result = [];
        foreach ($this->orders as $order) {
            $orderDate = date('Y-m-d', strtotime($order['order_date']));
            if ($orderDate >= $startDate && $orderDate <= $endDate) {
                $result[] = $order;
            }
        }
        return $result;
    }

    // Get total revenue
    public function getTotalRevenue($status = null) {
        $total = 0;
        foreach ($this->orders as $order) {
            if ($status === null || $order['status'] === $status) {
                if ($order['payment_status'] === 'paid') {
                    $total += $order['total_amount'];
                }
            }
        }
        return $total;
    }

    // Get order statistics
    public function getStatistics() {
        $stats = [
            'total_orders' => count($this->orders),
            'pending' => count($this->getPending()),
            'shipped' => count($this->getShipped()),
            'delivered' => count($this->getDelivered()),
            'cancelled' => count($this->getCancelled()),
            'total_revenue' => $this->getTotalRevenue()
        ];
        return $stats;
    }
}
?>
