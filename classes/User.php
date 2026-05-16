<?php

require_once __DIR__ . '/Database.php';

/**
 * User management — MySQL backend
 */
class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function getAll() {
        $stmt = $this->pdo->query(
            'SELECT id, name, email, password, role, created_at, status FROM users ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public function getById($id) {
        if ($id === null || $id === '') {
            return null;
        }
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password, role, created_at, status FROM users WHERE id = ?'
        );
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByEmail($email) {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password, role, created_at, status FROM users WHERE email = ?'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find existing user by email or create a customer account with a random password.
     * Returns user id on success, null on failure.
     */
    public function findOrCreateFromCheckout($name, $email) {
        $existing = $this->getByEmail($email);
        if ($existing) {
            return (int) $existing['id'];
        }

        $password = bin2hex(random_bytes(8));
        if ($this->add($name, $email, $password, 'customer')) {
            $created = $this->getByEmail($email);
            return $created ? (int) $created['id'] : null;
        }

        return null;
    }

    public function authenticate($email, $password) {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password, role, created_at, status FROM users WHERE email = ?'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function add($name, $email, $password, $role = 'customer') {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, role, created_at, status)
             VALUES (?, ?, ?, ?, NOW(), ?)'
        );
        return $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            'active',
        ]);
    }

    public function update($id, $name, $email, $role = null, $status = null) {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, (int) $id]);
        if ($stmt->fetch()) {
            return false;
        }

        if ($role !== null && $status !== null) {
            $stmt = $this->pdo->prepare(
                'UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?'
            );
            return $stmt->execute([$name, $email, $role, $status, (int) $id]);
        }
        if ($role !== null) {
            $stmt = $this->pdo->prepare(
                'UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?'
            );
            return $stmt->execute([$name, $email, $role, (int) $id]);
        }
        if ($status !== null) {
            $stmt = $this->pdo->prepare(
                'UPDATE users SET name = ?, email = ?, status = ? WHERE id = ?'
            );
            return $stmt->execute([$name, $email, $status, (int) $id]);
        }

        $stmt = $this->pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        return $stmt->execute([$name, $email, (int) $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET password = ? WHERE id = ?'
        );
        $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            (int) $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getByRole($role) {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password, role, created_at, status FROM users WHERE role = ? ORDER BY id'
        );
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}
