<?php

require_once __DIR__ . '/Database.php';

/**
 * Product reviews — MySQL backend
 */
class Review {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->pdo();
    }

    private function baseSelectSql() {
        return 'SELECT r.id, r.product_id, r.user_id, r.reviewer_name, r.reviewer_email,
                r.rating, r.title, r.body, r.status, r.created_at,
                p.name AS product_name,
                u.name AS user_name
                FROM product_reviews r
                INNER JOIN products p ON p.id = r.product_id
                LEFT JOIN users u ON u.id = r.user_id';
    }

    public function getApprovedByProductId($productId) {
        $stmt = $this->pdo->prepare(
            $this->baseSelectSql() . "
             WHERE r.product_id = ? AND r.status = 'approved'
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([(int) $productId]);
        return $stmt->fetchAll();
    }

    public function getSummaryByProductId($productId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS review_count,
                    COALESCE(AVG(rating), 0) AS avg_rating
             FROM product_reviews
             WHERE product_id = ? AND status = 'approved'"
        );
        $stmt->execute([(int) $productId]);
        $row = $stmt->fetch();
        return [
            'count' => (int) ($row['review_count'] ?? 0),
            'average' => round((float) ($row['avg_rating'] ?? 0), 1),
        ];
    }

    public function getAllForAdmin($status = null) {
        $sql = $this->baseSelectSql() . ' WHERE 1=1';
        $params = [];
        if ($status !== null && $status !== '') {
            $sql .= ' AND r.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY r.created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare($this->baseSelectSql() . ' WHERE r.id = ?');
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function add($productId, $rating, $body, $title = null, $userId = null, $reviewerName = null, $reviewerEmail = null) {
        $productId = (int) $productId;
        $rating = (int) $rating;
        $body = trim($body);
        $title = $title !== null && trim($title) !== '' ? trim($title) : null;

        if ($rating < 1 || $rating > 5 || $body === '') {
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT id FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            return false;
        }

        if ($userId !== null) {
            $stmt = $this->pdo->prepare('SELECT name, email FROM users WHERE id = ?');
            $stmt->execute([(int) $userId]);
            $user = $stmt->fetch();
            if (!$user) {
                return false;
            }
            $reviewerName = $user['name'];
            $reviewerEmail = $user['email'];
        } else {
            $reviewerName = trim((string) $reviewerName);
            $reviewerEmail = trim((string) $reviewerEmail);
            if ($reviewerName === '' || $reviewerEmail === '' || !filter_var($reviewerEmail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO product_reviews
             (product_id, user_id, reviewer_name, reviewer_email, rating, title, body, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        return $stmt->execute([
            $productId,
            $userId !== null ? (int) $userId : null,
            $reviewerName,
            $reviewerEmail,
            $rating,
            $title,
            $body,
            'pending',
        ]);
    }

    public function updateStatus($id, $status) {
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return false;
        }
        $stmt = $this->pdo->prepare('UPDATE product_reviews SET status = ? WHERE id = ?');
        return $stmt->execute([$status, (int) $id]);
    }

    public function approve($id) {
        return $this->updateStatus($id, 'approved');
    }

    public function reject($id) {
        return $this->updateStatus($id, 'rejected');
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM product_reviews WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }

    public function getPendingCount() {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM product_reviews WHERE status = 'pending'"
        );
        return (int) $stmt->fetchColumn();
    }

    /**
     * Render star icons for a rating (1–5).
     */
    public static function renderStars($rating, $extraClass = '') {
        $rating = max(0, min(5, (float) $rating));
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.25 && ($rating - $full) < 0.75;
        if (($rating - $full) >= 0.75) {
            $full++;
            $half = false;
        }
        $empty = 5 - $full - ($half ? 1 : 0);

        $class = trim('review-stars ' . $extraClass);
        $html = '<div class="' . htmlspecialchars($class) . '">';
        for ($i = 0; $i < $full; $i++) {
            $html .= '<i class="fa-solid fa-star"></i>';
        }
        if ($half) {
            $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
        }
        for ($i = 0; $i < $empty; $i++) {
            $html .= '<i class="fa-regular fa-star"></i>';
        }
        $html .= '</div>';
        return $html;
    }

    public static function timeAgo($datetime) {
        $ts = strtotime($datetime);
        $diff = time() - $ts;
        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return $m . ' minute' . ($m === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return $h . ' hour' . ($h === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return $d . ' day' . ($d === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 2592000) {
            $w = (int) floor($diff / 604800);
            return $w . ' week' . ($w === 1 ? '' : 's') . ' ago';
        }
        return date('M j, Y', $ts);
    }
}
