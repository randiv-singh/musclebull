
SET NAMES utf8mb4;


USE musclebull;

SET FOREIGN_KEY_CHECKS = 0;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Categories
-- -----------------------------------------------------------------------------
INSERT INTO categories (id, name, description, image, status, created_at) VALUES
(1, 'T-Shirts', 'Comfortable and stylish t-shirts for workouts and casual wear', './assets/images/categories/t-shirts.jpg', 'active', '2024-01-01 00:00:00'),
(2, 'Hoodies', 'Warm and cozy hoodies perfect for pre and post-workout', './assets/images/categories/hoodies.jpg', 'active', '2024-01-01 00:00:00'),
(3, 'Pants', 'Flexible and durable pants for maximum mobility', './assets/images/categories/pants.jpg', 'active', '2024-01-01 00:00:00'),
(4, 'Tank Tops', 'Breathable tank tops for intense upper body workouts', './assets/images/categories/tank-tops.jpg', 'active', '2024-01-01 00:00:00'),
(5, 'Accessories', 'Gym accessories and equipment', './assets/images/categories/accessories.jpg', 'inactive', '2024-01-01 00:00:00');

-- -----------------------------------------------------------------------------
-- Users (password hashes unchanged from bcrypt in JSON exports)
-- -----------------------------------------------------------------------------
INSERT INTO users (id, name, email, password, role, created_at, status) VALUES
(1, 'Admin User', 'admin@musclebull.com', '$2y$12$EzjP0VGGo4lU3iDUvxjV5uMV/CuA3jNvtyF77FZ87C9ZSjXoEqlna', 'admin', '2026-05-02 17:59:45', 'active'),
(2, 'Test Test', 'test@gmail.com', '$2y$12$B2a8UGdvHN9Fl0S5/rIEh.vYiAAuws0OvtZel3w1uzhSlx7TOpSyG', 'customer', '2026-05-02 19:03:00', 'active');

-- -----------------------------------------------------------------------------
-- Products (category_id: T-Shirts=1, Hoodies=2, Pants=3, Tank Tops=4)
-- -----------------------------------------------------------------------------
INSERT INTO products (id, category_id, name, price, image, description, is_best_seller, is_featured) VALUES
(1, 1, 'Black Oversize Tee', 3500, '/assets/images/products/black oversize 1.jpg', 'Premium oversized t-shirt crafted from 100% cotton for maximum comfort during your workouts. Features the iconic Muscle Bull logo and a relaxed fit perfect for training or casual wear.', 1, 0),
(2, 2, 'White Hoodie', 5200, '/assets/images/products/white hoodie 1.jpg', 'Stay warm and stylish with our premium white hoodie. Perfect for pre and post-workout.', 1, 1),
(3, 3, 'Blue Skinny Pants', 4800, '/assets/images/products/blue skinny 2.jpg', 'Flexible and durable skinny pants designed for maximum mobility during leg days.', 1, 0),
(5, 2, 'Gray Hoodie', 5000, '/assets/images/products/gray hoodie 1.jpg', 'A classic gray hoodie that fits perfectly into any fitness wardrobe.', 1, 1),
(6, 3, 'Green Skinny Pants', 4500, '/assets/images/products/green skinny 1.jpg', 'High-performance green skinny pants for intense training sessions.', 0, 1),
(7, 2, 'Blue Hoodie', 5300, '/assets/images/products/blue hoodie 1.jpg', 'Premium blue hoodie with a comfortable fit and stylish design.', 0, 1),
(8, 3, 'White Skinny Pants', 4200, '/assets/images/products/white skinny 1.jpg', 'Clean and crisp white skinny pants for a sharp gym look.', 0, 0),
(9, 4, 'Black Tank', 3000, '/assets/images/products/black tank 1.jpg', 'Breathable black tank top for those intense upper body workouts.', 0, 0),
(10, 2, 'Red Hoodie', 5400, '/assets/images/products/red hoodie 1.jpg', 'Bold red hoodie to fuel your passion and energy.', 0, 0);

-- -----------------------------------------------------------------------------
-- Product thumbnails (order preserved from JSON thumbnail arrays)
-- -----------------------------------------------------------------------------
INSERT INTO product_thumbnails (product_id, sort_order, path) VALUES
(1, 0, '/assets/images/products/black oversize 1.jpg'),
(1, 1, '/assets/images/products/black oversize 2.jpg'),
(1, 2, '/assets/images/products/black oversize 3.jpg'),
(1, 3, '/assets/images/products/black oversize 4.jpg'),
(2, 0, '/assets/images/products/white hoodie 1.jpg'),
(2, 1, '/assets/images/products/white hoodie 2 .jpg'),
(2, 2, '/assets/images/products/white hoodie 3.jpg'),
(3, 0, '/assets/images/products/blue skinny 2.jpg'),
(3, 1, '/assets/images/products/blue skinny 1.jpg'),
(5, 0, '/assets/images/products/gray hoodie 1.jpg'),
(5, 1, '/assets/images/products/gray hoodie 2.jpg'),
(5, 2, '/assets/images/products/gray hoodie 3.jpg'),
(6, 0, '/assets/images/products/green skinny 1.jpg'),
(6, 1, '/assets/images/products/green skinny 2.jpg'),
(7, 0, '/assets/images/products/blue hoodie 1.jpg'),
(7, 1, '/assets/images/products/blue hoodie 2.jpg'),
(7, 2, '/assets/images/products/blue hoodie 3.jpg'),
(7, 3, '/assets/images/products/blue hoodie 4.jpg'),
(8, 0, '/assets/images/products/white skinny 1.jpg'),
(8, 1, '/assets/images/products/white skinny 2.jpg'),
(9, 0, '/assets/images/products/black tank 1.jpg'),
(9, 1, '/assets/images/products/black tank 2.jpg'),
(10, 0, '/assets/images/products/red hoodie 1.jpg'),
(10, 1, '/assets/images/products/red hoodie 2.jpg'),
(10, 2, '/assets/images/products/red hoodie 3.jpg');

-- -----------------------------------------------------------------------------
-- Gift cards
-- -----------------------------------------------------------------------------
INSERT INTO gift_cards (id, code, amount, balance, recipient_email, message, status, created_at, expiry_date, used_at) VALUES
(1, 'GC12345678', 5000, 5000, 'john@example.com', 'Happy Birthday! Enjoy your shopping!', 'active', '2024-01-10 09:00:00', '2025-01-10', NULL),
(2, 'GC87654321', 3000, 1500, 'jane@example.com', 'Thank you for your purchase!', 'active', '2024-01-05 14:30:00', '2025-01-05', NULL),
(3, 'GC11223344', 10000, 0, 'test@example.com', 'Welcome gift!', 'used', '2023-12-01 11:00:00', '2024-12-01', '2024-01-15 16:45:00');

-- -----------------------------------------------------------------------------
-- Orders (shipping flattened to ship_* columns)
-- -----------------------------------------------------------------------------
INSERT INTO orders (
  id, user_id, total_amount, status, payment_method, payment_status,
  order_date, shipped_date, delivered_date, tracking_number, notes,
  ship_first_name, ship_last_name, ship_email, ship_phone, ship_address, ship_city,
  ship_postal_code, shipping_method, shipping_cost
) VALUES (
  1, NULL, 15800, 'shipped', 'cod', 'pending',
  '2026-05-03 08:24:26', '2026-05-03 08:28:37', NULL, NULL, '',
  'John', 'Doe', 'test@gmail.com', '0704567345', '123 Main', 'Colombo',
  '10200', 'standard', 0
);

-- -----------------------------------------------------------------------------
-- Order line items (product_id set when product exists)
-- -----------------------------------------------------------------------------
INSERT INTO order_items (order_id, product_id, name, price, image, size, quantity) VALUES
(1, 2, 'White Hoodie', 5200, '/assets/images/products/white hoodie 1.jpg', 'XXL', 1),
(1, 7, 'Blue Hoodie', 5300, '/assets/images/products/blue hoodie 1.jpg', 'XXL', 2);

-- -----------------------------------------------------------------------------
-- AUTO_INCREMENT (next ids after explicit inserts)
-- -----------------------------------------------------------------------------
ALTER TABLE categories AUTO_INCREMENT = 6;
ALTER TABLE users AUTO_INCREMENT = 3;
ALTER TABLE products AUTO_INCREMENT = 11;
ALTER TABLE product_thumbnails AUTO_INCREMENT = 25;
ALTER TABLE gift_cards AUTO_INCREMENT = 4;
ALTER TABLE orders AUTO_INCREMENT = 2;
ALTER TABLE order_items AUTO_INCREMENT = 3;
