<?php

require_once __DIR__ . '/Database.php';

/**
 * Contact form messages — MySQL backend
 */
class ContactMessage {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function getAll() {
        $stmt = $this->pdo->query(
            'SELECT id, first_name, last_name, email, order_number, message, status, created_at
             FROM contact_messages
             ORDER BY created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare(
            'SELECT id, first_name, last_name, email, order_number, message, status, created_at
             FROM contact_messages WHERE id = ?'
        );
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function add($firstName, $lastName, $email, $message, $orderNumber = null) {
        $orderNumber = $orderNumber !== null && trim($orderNumber) !== '' ? trim($orderNumber) : null;

        $stmt = $this->pdo->prepare(
            'INSERT INTO contact_messages (first_name, last_name, email, order_number, message, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())'
        );
        return $stmt->execute([
            trim($firstName),
            trim($lastName),
            trim($email),
            $orderNumber,
            trim($message),
            'new',
        ]);
    }

    public function markAsRead($id) {
        $stmt = $this->pdo->prepare(
            'UPDATE contact_messages SET status = ? WHERE id = ?'
        );
        return $stmt->execute(['read', (int) $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }

    public function getUnreadCount() {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"
        );
        return (int) $stmt->fetchColumn();
    }
}
