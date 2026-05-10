<?php

require_once __DIR__ . '/Database.php';

/**
 * Gift card management — MySQL backend
 */
class GiftCard {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function getAll() {
        $stmt = $this->pdo->query(
            'SELECT id, code, amount, balance, recipient_email, message, status,
                    created_at, expiry_date, used_at
             FROM gift_cards ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, amount, balance, recipient_email, message, status,
                    created_at, expiry_date, used_at
             FROM gift_cards WHERE id = ?'
        );
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCode($code) {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, amount, balance, recipient_email, message, status,
                    created_at, expiry_date, used_at
             FROM gift_cards WHERE code = ?'
        );
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function add($code, $amount, $recipientEmail, $message = '', $expiryDate = null) {
        $stmt = $this->pdo->prepare('SELECT id FROM gift_cards WHERE code = ?');
        $stmt->execute([$code]);
        if ($stmt->fetch()) {
            return false;
        }

        if ($expiryDate === null) {
            $expiryDate = date('Y-m-d', strtotime('+1 year'));
        }

        $amountInt = (int) round((float) $amount);
        $stmt = $this->pdo->prepare(
            'INSERT INTO gift_cards (code, amount, balance, recipient_email, message, status, created_at, expiry_date, used_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, NULL)'
        );
        return $stmt->execute([
            $code,
            $amountInt,
            $amountInt,
            $recipientEmail,
            $message,
            'active',
            $expiryDate,
        ]);
    }

    public function update($id, $code, $amount, $recipientEmail, $message = '', $expiryDate = null, $status = null) {
        $stmt = $this->pdo->prepare('SELECT id FROM gift_cards WHERE code = ? AND id != ?');
        $stmt->execute([$code, (int) $id]);
        if ($stmt->fetch()) {
            return false;
        }

        $amountInt = (int) round((float) $amount);
        $sql = 'UPDATE gift_cards SET code = ?, amount = ?, recipient_email = ?, message = ?';
        $params = [$code, $amountInt, $recipientEmail, $message];

        if ($expiryDate !== null && $expiryDate !== '') {
            $sql .= ', expiry_date = ?';
            $params[] = $expiryDate;
        }
        if ($status !== null && $status !== '') {
            $sql .= ', status = ?';
            $params[] = $status;
        }

        $sql .= ' WHERE id = ?';
        $params[] = (int) $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM gift_cards WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function use($code, $amount) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, balance, status FROM gift_cards WHERE code = ? FOR UPDATE'
            );
            $stmt->execute([$code]);
            $row = $stmt->fetch();
            if (!$row || $row['status'] !== 'active' || (int) $row['balance'] < (int) $amount) {
                $this->pdo->rollBack();
                return false;
            }

            $newBalance = (int) $row['balance'] - (int) $amount;
            if ($newBalance <= 0) {
                $stmt = $this->pdo->prepare(
                    'UPDATE gift_cards SET balance = 0, status = \'used\', used_at = NOW() WHERE id = ?'
                );
                $stmt->execute([(int) $row['id']]);
            } else {
                $stmt = $this->pdo->prepare('UPDATE gift_cards SET balance = ? WHERE id = ?');
                $stmt->execute([$newBalance, (int) $row['id']]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getActive() {
        $stmt = $this->pdo->query(
            "SELECT id, code, amount, balance, recipient_email, message, status,
                    created_at, expiry_date, used_at
             FROM gift_cards WHERE status = 'active' ORDER BY id"
        );
        return $stmt->fetchAll();
    }

    public function getExpired() {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare(
            "SELECT id, code, amount, balance, recipient_email, message, status,
                    created_at, expiry_date, used_at
             FROM gift_cards WHERE expiry_date < ? AND status = 'active' ORDER BY id"
        );
        $stmt->execute([$today]);
        return $stmt->fetchAll();
    }

    public function generateCode() {
        do {
            $code = 'GC' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
            $stmt = $this->pdo->prepare('SELECT id FROM gift_cards WHERE code = ?');
            $stmt->execute([$code]);
        } while ($stmt->fetch());

        return $code;
    }
}
