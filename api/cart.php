<?php
session_start();
header('Content-Type: application/json');
require_once '../classes/Cart.php';

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();
$cart = new Cart($userId, $sessionId);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get cart items
        echo json_encode([
            'success' => true,
            'items' => $cart->getItemsArray(),
            'total' => $cart->getTotal(),
            'count' => $cart->getItemCount()
        ]);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        
        switch ($action) {
            case 'add':
                $result = $cart->addItem(
                    $data['id'],
                    $data['name'],
                    $data['price'],
                    $data['image'],
                    $data['size'] ?? 'M',
                    $data['quantity'] ?? 1
                );
                echo json_encode([
                    'success' => $result,
                    'items' => $cart->getItemsArray(),
                    'total' => $cart->getTotal(),
                    'count' => $cart->getItemCount()
                ]);
                break;
                
            case 'update':
                $result = $cart->updateQuantity(
                    $data['id'],
                    $data['size'],
                    $data['quantity']
                );
                echo json_encode([
                    'success' => $result,
                    'items' => $cart->getItemsArray(),
                    'total' => $cart->getTotal(),
                    'count' => $cart->getItemCount()
                ]);
                break;
                
            case 'remove':
                $result = $cart->removeItem($data['id'], $data['size']);
                echo json_encode([
                    'success' => $result,
                    'items' => $cart->getItemsArray(),
                    'total' => $cart->getTotal(),
                    'count' => $cart->getItemCount()
                ]);
                break;
                
            case 'clear':
                $result = $cart->clearCart();
                echo json_encode([
                    'success' => $result,
                    'items' => [],
                    'total' => 0,
                    'count' => 0
                ]);
                break;
                
            case 'sync':
                // Sync entire cart from client
                $result = $cart->setCart($data['items'] ?? []);
                echo json_encode([
                    'success' => $result,
                    'items' => $cart->getItemsArray(),
                    'total' => $cart->getTotal(),
                    'count' => $cart->getItemCount()
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid method']);
}
?>
