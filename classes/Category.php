<?php

require_once __DIR__ . '/Database.php';

/**
 * Category management — MySQL backend
 */
class Category {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function getAll() {
        $stmt = $this->pdo->query(
            'SELECT id, name, description, image, status, created_at FROM categories ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, description, image, status, created_at FROM categories WHERE id = ?'
        );
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByName($name) {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, description, image, status, created_at FROM categories WHERE name = ?'
        );
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function add($name, $description = '', $image = '') {
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, description, image, status, created_at)
             VALUES (?, ?, ?, ?, NOW())'
        );
        return $stmt->execute([$name, $description, $image, 'active']);
    }

    public function update($id, $name, $description = '', $image = '', $status = null) {
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
        $stmt->execute([$name, (int) $id]);
        if ($stmt->fetch()) {
            return false;
        }

        if ($status !== null) {
            $stmt = $this->pdo->prepare(
                'UPDATE categories SET name = ?, description = ?, image = ?, status = ? WHERE id = ?'
            );
            return $stmt->execute([$name, $description, $image, $status, (int) $id]);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?'
        );
        return $stmt->execute([$name, $description, $image, (int) $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM products WHERE category_id = ?'
        );
        $stmt->execute([(int) $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function getActive() {
        $stmt = $this->pdo->query(
            "SELECT id, name, description, image, status, created_at FROM categories WHERE status = 'active' ORDER BY name"
        );
        return $stmt->fetchAll();
    }

    public function updateProductCount($categoryId, $count) {
        return true;
    }

    /**
     * Categories with live product counts (legacy parameter ignored).
     *
     * @param mixed $productClass Unused; kept for admin/categories.php compatibility
     */
    public function getWithProductCounts($productClass = null) {
        $sql = 'SELECT c.id, c.name, c.description, c.image, c.status, c.created_at,
                COUNT(p.id) AS product_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id
                GROUP BY c.id, c.name, c.description, c.image, c.status, c.created_at
                ORDER BY c.id';
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['product_count'] = (int) $r['product_count'];
        }
        return $rows;
    }
}
