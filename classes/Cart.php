<?php

/**
 * Cart Class
 * Server-side cart management using JSON storage
 */
class Cart {
    private $jsonFile;
    private $carts;
    private $sessionId;
    private $userId;

    public function __construct($userId = null, $sessionId = null) {
        // Support being called from both root and subdirectories
        if (file_exists(__DIR__ . '/../config/carts.json')) {
            $this->jsonFile = __DIR__ . '/../config/carts.json';
        } else {
            $this->jsonFile = 'config/carts.json';
        }
        
        $this->userId = $userId;
        $this->sessionId = $sessionId ?? session_id() ?? uniqid('cart_', true);
        $this->loadCarts();
    }

    private function loadCarts() {
        if (file_exists($this->jsonFile)) {
            $jsonContent = file_get_contents($this->jsonFile);
            $this->carts = json_decode($jsonContent, true) ?: [];
        } else {
            $this->carts = [];
        }
    }

    private function saveCarts() {
        $jsonContent = json_encode($this->carts, JSON_PRETTY_PRINT);
        return file_put_contents($this->jsonFile, $jsonContent) !== false;
    }

    // Get the cart key (user_id if logged in, session_id if not)
    private function getCartKey() {
        return $this->userId ? 'user_' . $this->userId : 'session_' . $this->sessionId;
    }

    // Get or create cart for current user/session
    public function getCart() {
        $key = $this->getCartKey();
        
        if (!isset($this->carts[$key])) {
            $this->carts[$key] = [
                'items' => [],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $this->carts[$key]['items'];
    }

    // Add item to cart
    public function addItem($productId, $name, $price, $image, $size = 'M', $quantity = 1) {
        $key = $this->getCartKey();
        $this->getCart(); // Ensure cart exists
        
        $itemKey = $productId . '_' . $size;
        
        if (isset($this->carts[$key]['items'][$itemKey])) {
            // Update quantity if item already exists
            $this->carts[$key]['items'][$itemKey]['quantity'] += $quantity;
        } else {
            // Add new item
            $this->carts[$key]['items'][$itemKey] = [
                'id' => $productId,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'size' => $size,
                'quantity' => $quantity,
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->carts[$key]['updated_at'] = date('Y-m-d H:i:s');
        return $this->saveCarts();
    }

    // Update item quantity
    public function updateQuantity($productId, $size, $quantity) {
        $key = $this->getCartKey();
        $itemKey = $productId . '_' . $size;
        
        if (!isset($this->carts[$key]['items'][$itemKey])) {
            return false;
        }
        
        if ($quantity <= 0) {
            return $this->removeItem($productId, $size);
        }
        
        $this->carts[$key]['items'][$itemKey]['quantity'] = $quantity;
        $this->carts[$key]['updated_at'] = date('Y-m-d H:i:s');
        return $this->saveCarts();
    }

    // Remove item from cart
    public function removeItem($productId, $size) {
        $key = $this->getCartKey();
        $itemKey = $productId . '_' . $size;
        
        if (!isset($this->carts[$key]['items'][$itemKey])) {
            return false;
        }
        
        unset($this->carts[$key]['items'][$itemKey]);
        $this->carts[$key]['updated_at'] = date('Y-m-d H:i:s');
        return $this->saveCarts();
    }

    // Clear cart
    public function clearCart() {
        $key = $this->getCartKey();
        
        if (isset($this->carts[$key])) {
            $this->carts[$key]['items'] = [];
            $this->carts[$key]['updated_at'] = date('Y-m-d H:i:s');
            return $this->saveCarts();
        }
        
        return true;
    }

    // Get cart items as array (for display)
    public function getItemsArray() {
        $items = $this->getCart();
        return array_values($items);
    }

    // Get cart total
    public function getTotal() {
        $items = $this->getCart();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }

    // Get cart item count
    public function getItemCount() {
        $items = $this->getCart();
        $count = 0;
        
        foreach ($items as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }

    // Merge session cart to user cart (called on login)
    public function mergeSessionCart($sessionId) {
        $sessionKey = 'session_' . $sessionId;
        $userKey = $this->getCartKey();
        
        if (!isset($this->carts[$sessionKey]) || empty($this->carts[$sessionKey]['items'])) {
            return true;
        }
        
        // Get or create user cart
        $this->getCart();
        
        // Merge items
        foreach ($this->carts[$sessionKey]['items'] as $itemKey => $item) {
            if (isset($this->carts[$userKey]['items'][$itemKey])) {
                // Add quantities if item exists
                $this->carts[$userKey]['items'][$itemKey]['quantity'] += $item['quantity'];
            } else {
                // Copy item if doesn't exist
                $this->carts[$userKey]['items'][$itemKey] = $item;
            }
        }
        
        $this->carts[$userKey]['updated_at'] = date('Y-m-d H:i:s');
        
        // Remove session cart
        unset($this->carts[$sessionKey]);
        
        return $this->saveCarts();
    }

    // Replace entire cart (used when syncing from client)
    public function setCart($items) {
        $key = $this->getCartKey();
        
        $cartItems = [];
        foreach ($items as $item) {
            $itemKey = $item['id'] . '_' . $item['size'];
            $cartItems[$itemKey] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'size' => $item['size'],
                'quantity' => $item['quantity'],
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->carts[$key] = [
            'items' => $cartItems,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->saveCarts();
    }
}
?>
