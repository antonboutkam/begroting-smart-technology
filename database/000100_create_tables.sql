CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    display_name VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

SET @users_has_username := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'username'
);

SET @users_add_username_sql := IF(
    @users_has_username = 0,
    'ALTER TABLE users ADD COLUMN username VARCHAR(100) NOT NULL UNIQUE AFTER id',
    'SELECT 1'
);

PREPARE users_add_username_stmt FROM @users_add_username_sql;
EXECUTE users_add_username_stmt;
DEALLOCATE PREPARE users_add_username_stmt;

INSERT INTO users (username, email, display_name, password_hash)
VALUES (
    'anton',
    'anton@localhost',
    'Anton',
    '$2y$10$QPL.3unFxckDpFErtYlxGOGq./WzYQxmtz0756n3uJWxiDqhi7omu'
)
ON DUPLICATE KEY UPDATE
    display_name = VALUES(display_name),
    password_hash = VALUES(password_hash);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    name VARCHAR(190) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    name VARCHAR(190) NOT NULL,
    description TEXT NULL,
    goal TEXT NULL,
    brand VARCHAR(190) NULL,
    priority TINYINT UNSIGNED NOT NULL DEFAULT 0,
    unit VARCHAR(50) NULL,
    is_asset TINYINT(1) NOT NULL DEFAULT 0,
    quantity_per_student DECIMAL(12,4) NOT NULL DEFAULT 1.0000,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_images_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    description TEXT NULL,
    vat_rate DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    shipping_excl DECIMAL(12,2) NULL,
    shipping_incl DECIMAL(12,2) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS supplier_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_supplier_files_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS supplier_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    product_id INT NOT NULL,
    price_excl DECIMAL(12,2) NULL,
    price_incl DECIMAL(12,2) NULL,
    package_information TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_supplier_product (supplier_id, product_id),
    CONSTRAINT fk_supplier_products_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_supplier_products_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
);
