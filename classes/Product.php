<?php

require_once __DIR__ . '/Database.php';

/**
 * Product management — MySQL backend
 */
class Product {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    private function normalizePrice($price) {
        return (int) round(is_numeric($price) ? (float) $price : 0);
    }

    private function fetchThumbnailsForProducts(array $productIds) {
        if (empty($productIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT product_id, path FROM product_thumbnails
             WHERE product_id IN ($placeholders)
             ORDER BY product_id, sort_order, id"
        );
        $stmt->execute($productIds);
        $byProduct = [];
        while ($row = $stmt->fetch()) {
            $pid = (int) $row['product_id'];
            if (!isset($byProduct[$pid])) {
                $byProduct[$pid] = [];
            }
            $byProduct[$pid][] = $row['path'];
        }
        return $byProduct;
    }

    private function hydrateProductRow(array $row, array $thumbnails) {
        return [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'price' => (int) $row['price'],
            'category' => $row['category_name'],
            'category_id' => (int) $row['category_id'],
            'image' => $row['image'],
            'thumbnails' => $thumbnails,
            'description' => $row['description'],
            'isBestSeller' => (bool) $row['is_best_seller'],
            'isFeatured' => (bool) $row['is_featured'],
        ];
    }

    private function baseSelectSql() {
        return 'SELECT p.id, p.category_id, p.name, p.price, p.image, p.description,
                p.is_best_seller, p.is_featured, c.name AS category_name
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id';
    }

    public function getAll() {
        $stmt = $this->pdo->query($this->baseSelectSql() . ' ORDER BY p.id');
        $rows = $stmt->fetchAll();
        $ids = array_column($rows, 'id');
        $thumbMap = $this->fetchThumbnailsForProducts($ids);
        $out = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            $thumbs = $thumbMap[$id] ?? [$row['image']];
            $out[] = $this->hydrateProductRow($row, $thumbs);
        }
        return $out;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare($this->baseSelectSql() . ' WHERE p.id = ?');
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $thumbs = $this->fetchThumbnailsForProducts([(int) $row['id']]);
        $id = (int) $row['id'];
        $thumbList = $thumbs[$id] ?? [$row['image']];
        return $this->hydrateProductRow($row, $thumbList);
    }

    private function resolveCategoryIdByName($categoryName) {
        $stmt = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([$categoryName]);
        $r = $stmt->fetch();
        return $r ? (int) $r['id'] : null;
    }

    private function replaceThumbnails($productId, $mainImage, array $thumbnailPaths) {
        $stmt = $this->pdo->prepare('DELETE FROM product_thumbnails WHERE product_id = ?');
        $stmt->execute([$productId]);
        $paths = !empty($thumbnailPaths) ? $thumbnailPaths : [$mainImage];
        $sort = 0;
        $ins = $this->pdo->prepare(
            'INSERT INTO product_thumbnails (product_id, sort_order, path) VALUES (?, ?, ?)'
        );
        foreach ($paths as $path) {
            $ins->execute([$productId, $sort++, $path]);
        }
    }

    public function add($name, $price, $category, $image, $description, $isBestSeller = false, $isFeatured = false) {
        $categoryId = $this->resolveCategoryIdByName($category);
        if ($categoryId === null) {
            return false;
        }
        $priceInt = $this->normalizePrice($price);
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (category_id, name, price, image, description, is_best_seller, is_featured)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $ok = $stmt->execute([
            $categoryId,
            $name,
            $priceInt,
            $image,
            $description,
            $isBestSeller ? 1 : 0,
            $isFeatured ? 1 : 0,
        ]);
        if (!$ok) {
            return false;
        }
        $newId = (int) $this->pdo->lastInsertId();
        $this->replaceThumbnails($newId, $image, [$image]);
        return true;
    }

    public function update($id, $name, $price, $category, $image, $description, $isBestSeller = false, $isFeatured = false) {
        $stmt = $this->pdo->prepare('SELECT id FROM products WHERE id = ?');
        $stmt->execute([(int) $id]);
        if (!$stmt->fetch()) {
            return false;
        }

        $categoryId = $this->resolveCategoryIdByName($category);
        if ($categoryId === null) {
            return false;
        }
        $priceInt = $this->normalizePrice($price);
        $stmt = $this->pdo->prepare(
            'UPDATE products SET category_id = ?, name = ?, price = ?, image = ?, description = ?,
             is_best_seller = ?, is_featured = ? WHERE id = ?'
        );
        if (!$stmt->execute([
            $categoryId,
            $name,
            $priceInt,
            $image,
            $description,
            $isBestSeller ? 1 : 0,
            $isFeatured ? 1 : 0,
            (int) $id,
        ])) {
            return false;
        }
        $this->replaceThumbnails((int) $id, $image, [$image]);
        return true;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([(int) $id]);
        return $stmt->rowCount() > 0;
    }

    public function getBestSellers() {
        $stmt = $this->pdo->query(
            $this->baseSelectSql() . ' WHERE p.is_best_seller = 1 ORDER BY p.id'
        );
        $rows = $stmt->fetchAll();
        $ids = array_column($rows, 'id');
        $thumbMap = $this->fetchThumbnailsForProducts($ids);
        $out = [];
        foreach ($rows as $row) {
            $pid = (int) $row['id'];
            $thumbs = $thumbMap[$pid] ?? [$row['image']];
            $out[] = $this->hydrateProductRow($row, $thumbs);
        }
        return $out;
    }

    public function getFeatured() {
        $stmt = $this->pdo->query(
            $this->baseSelectSql() . ' WHERE p.is_featured = 1 ORDER BY p.id'
        );
        $rows = $stmt->fetchAll();
        $ids = array_column($rows, 'id');
        $thumbMap = $this->fetchThumbnailsForProducts($ids);
        $out = [];
        foreach ($rows as $row) {
            $pid = (int) $row['id'];
            $thumbs = $thumbMap[$pid] ?? [$row['image']];
            $out[] = $this->hydrateProductRow($row, $thumbs);
        }
        return $out;
    }

    public function search($query) {
        $q = '%' . strtolower($query) . '%';
        $sql = $this->baseSelectSql() . '
            WHERE LOWER(p.name) LIKE ? OR LOWER(IFNULL(p.description, "")) LIKE ?
            ORDER BY p.id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$q, $q]);
        $rows = $stmt->fetchAll();
        $ids = array_column($rows, 'id');
        $thumbMap = $this->fetchThumbnailsForProducts($ids);
        $out = [];
        foreach ($rows as $row) {
            $pid = (int) $row['id'];
            $thumbs = $thumbMap[$pid] ?? [$row['image']];
            $out[] = $this->hydrateProductRow($row, $thumbs);
        }
        return $out;
    }
}
