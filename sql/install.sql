CREATE DATABASE IF NOT EXISTS office_equipment_store;
USE office_equipment_store;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  address TEXT NOT NULL,
  contact_numbers VARCHAR(255) NOT NULL,
  role ENUM('buyer','admin','superadmin') NOT NULL DEFAULT 'buyer',
  email_verified TINYINT(1) NOT NULL DEFAULT 0,
  verify_token VARCHAR(64) DEFAULT NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_number VARCHAR(50) NOT NULL UNIQUE,
  total_amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  delivery_address TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES
('Chairs'), ('Tables'), ('Cabinets'), ('Desks');

INSERT INTO products (category_id, name, description, price, stock, image_url, created_at, updated_at) VALUES
(1, 'Ergo Mesh Chair', 'Breathable office chair with adjustable height and lumbar support.', 4500.00, 20, '', NOW(), NOW()),
(2, 'Executive Desk', 'Wide desk with durable laminated surface for daily office use.', 8200.00, 12, '', NOW(), NOW()),
(3, '3-Door Storage Cabinet', 'Steel cabinet for files, tools, and office supplies.', 6900.00, 8, '', NOW(), NOW()),
(4, 'Compact Work Table', 'Lightweight table suitable for computers and small office setups.', 3200.00, 15, '', NOW(), NOW());

INSERT INTO users (full_name, email, password_hash, address, contact_numbers, role, email_verified, created_at)
VALUES ('Super Admin', 'admin@example.com', '$2y$12$xymZCAAyLWWA1dGgi5iPqewjk1SuVUYZp5wwgEyTEGRsIFuF8RtU2', 'Office Address', '09170000000', 'superadmin', 1, NOW());
