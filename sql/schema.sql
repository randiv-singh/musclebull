-- Muscle Bull — MySQL schema
-- Server version: MySQL 8.0+ recommended

CREATE DATABASE IF NOT EXISTS musclebull
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE musclebull;

-- ---------------------------------------------------------------------------
-- Users
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Categories
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  image VARCHAR(512) NOT NULL DEFAULT '',
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Products
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL COMMENT 'Amount in LKR (integer, same as legacy JSON)',
  image VARCHAR(512) NOT NULL,
  description TEXT,
  is_best_seller TINYINT(1) NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_products_category (category_id),
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_thumbnails (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  path VARCHAR(512) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_thumbnails_product (product_id),
  CONSTRAINT fk_thumbnails_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Gift cards
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gift_cards (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  amount INT NOT NULL,
  balance INT NOT NULL,
  recipient_email VARCHAR(255) NOT NULL,
  message TEXT,
  status VARCHAR(32) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expiry_date DATE NOT NULL,
  used_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_gift_cards_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Orders (header + flattened shipping — matches prior JSON shape when hydrated)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  total_amount INT NOT NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(32) NOT NULL DEFAULT 'cod',
  payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
  order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  shipped_date DATETIME NULL,
  delivered_date DATETIME NULL,
  tracking_number VARCHAR(255) NULL,
  notes TEXT,
  ship_first_name VARCHAR(100) NOT NULL,
  ship_last_name VARCHAR(100) NOT NULL,
  ship_email VARCHAR(255) NOT NULL,
  ship_phone VARCHAR(50) NOT NULL,
  ship_address VARCHAR(255) NOT NULL,
  ship_city VARCHAR(100) NOT NULL,
  ship_postal_code VARCHAR(32) NOT NULL,
  shipping_method VARCHAR(32) NOT NULL DEFAULT 'standard',
  shipping_cost INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_orders_user (user_id),
  KEY idx_orders_status (status),
  KEY idx_orders_order_date (order_date),
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  image VARCHAR(512) NOT NULL DEFAULT '',
  size VARCHAR(32) NOT NULL DEFAULT '',
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_product (product_id),
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Contact messages
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  order_number VARCHAR(64) NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'read') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contact_messages_status (status),
  KEY idx_contact_messages_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Product reviews
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS product_reviews (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  reviewer_name VARCHAR(255) NOT NULL,
  reviewer_email VARCHAR(255) NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  title VARCHAR(255) NULL,
  body TEXT NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reviews_product (product_id),
  KEY idx_reviews_status (status),
  KEY idx_reviews_user (user_id),
  CONSTRAINT fk_reviews_product
    FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_reviews_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
