-- Restaurant Soham - Phase 2 (MySQL / phpMyAdmin)
-- Import this file in phpMyAdmin.

CREATE DATABASE IF NOT EXISTS restaurant_soham
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE restaurant_soham;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(60) NOT NULL UNIQUE, -- home, about, contact
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  updated_by INT UNSIGNED NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pages_user FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sliders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  image_path VARCHAR(255) NOT NULL,
  caption VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  title VARCHAR(160) NOT NULL,
  description TEXT NULL,
  price DECIMAL(10,2) NULL,
  image_path VARCHAR(255) NULL,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_products_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  body TEXT NOT NULL,
  media_path VARCHAR(255) NULL, -- image or pdf
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_news_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed (minimal)
INSERT IGNORE INTO users (id, name, email, password_hash, role)
VALUES
  (1, 'Admin', 'admin@soham.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Admin password: password

INSERT IGNORE INTO pages (slug, title, body, updated_by)
VALUES
  ('home', 'Mirë se vini te Restaurant Soham', 'Përshkrimi i faqes kryesore lexohet nga databaza (phpMyAdmin).', 1),
  ('about', 'Rreth nesh', 'Kjo pjesë është dinamike: mund ta ndryshosh nga Admin Dashboard.', 1),
  ('contact', 'Na kontaktoni', 'Dërgo një mesazh dhe administratori do ta shohë në dashboard.', 1);

INSERT IGNORE INTO sliders (id, image_path, caption, sort_order)
VALUES
  (1, 'foto/home.png', 'Restaurant Soham', 1),
  (2, 'foto/res.jpg', 'Ushqim i shijshëm', 2),
  (3, 'foto/hom.jpg', 'Mikpritje', 3);

INSERT IGNORE INTO categories (id, name)
VALUES
  (1, 'Specialitete'),
  (2, 'Pije');

INSERT IGNORE INTO products (id, category_id, title, description, price, image_path, created_by)
VALUES
  (1, 1, 'Biftek', 'Pjatë kryesore', 7.50, NULL, 1),
  (2, 2, 'Birrë Peja', 'Pije alkoolike', 2.00, NULL, 1);

INSERT IGNORE INTO news (id, title, body, media_path, created_by)
VALUES
  (1, 'Lajm i ri', 'Ky lajm është nga databaza dhe tregohet në faqen News.', NULL, 1);

