-- Database structure for Militaria Przemka

-- Create database
CREATE DATABASE IF NOT EXISTS militaria_przemka;
USE militaria_przemka;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    slug VARCHAR(100) NOT NULL UNIQUE
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    slug VARCHAR(100) NOT NULL UNIQUE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Create users table for authentication
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert categories
INSERT INTO categories (name, description, slug) VALUES
('Broń krótka', 'Handguns including pistols and revolvers', 'handguns'),
('Broń długa', 'Long guns including rifles and shotguns', 'long-guns');

-- Insert products
INSERT INTO products (category_id, name, description, price, stock, image, slug, featured) VALUES
-- Handguns
(1, 'Sig Sauer P320 M17', 'The official sidearm of the U.S. Armed Forces, featuring a coyote-tan PVD coated stainless steel slide with the same optic cut as the MHS.', 599.99, 15, 'sig_p320_m17.jpg', 'sig-sauer-p320-m17', TRUE),
(1, 'Glock 19 Gen 5', 'The Glock 19 Gen5 features over 20 design modifications from previous generations, including improved accuracy, durability, and versatility.', 549.99, 20, 'glock_19_gen5.jpg', 'glock-19-gen-5', TRUE),
(1, 'Beretta 92X Performance', 'The Beretta 92X Performance was designed with one goal in mind: win in Production Division. It''s specifically designed for competitive shooting.', 899.99, 10, 'beretta_92x.jpg', 'beretta-92x-performance', FALSE),
(1, 'Springfield Armory XD-M Elite 9mm', 'The XD-M Elite features a match-grade barrel, enhanced trigger, and increased capacity, making it ideal for competition or self-defense.', 649.99, 12, 'springfield_xdm.jpg', 'springfield-xdm-elite', FALSE),
(1, 'CZ P-10F', 'The CZ P-10F is a full-size striker-fired pistol with excellent ergonomics, trigger, and reliability. Perfect for duty use or sport shooting.', 499.99, 18, 'cz_p10f.jpg', 'cz-p10f', FALSE),

-- Long guns
(2, 'FN SCAR 16S', 'The FN SCAR 16S is a semi-automatic rifle chambered in 5.56x45mm NATO. It features an extremely rugged and reliable design.', 2999.99, 8, 'fn_scar_16s.jpg', 'fn-scar-16s', TRUE),
(2, 'Smith & Wesson M&P15 Sport II', 'The M&P15 Sport II delivers premium features and proven reliability in an affordable modern sporting rifle.', 799.99, 25, 'mp15_sport.jpg', 'smith-wesson-mp15', FALSE),
(2, 'Heckler & Koch HK416', 'The HK416 is a battle-proven weapon and is in use with numerous special operations units around the world.', 3499.99, 5, 'hk416.jpg', 'heckler-koch-hk416', TRUE),
(2, 'Steyr AUG A3 M1', 'The Steyr AUG A3 M1 is a bullpup rifle that combines the features of a modern sporting rifle with the reliability of the original AUG design.', 1999.99, 7, 'steyr_aug.jpg', 'steyr-aug-a3-m1', FALSE),
(2, 'CZ Bren 2 MS', 'The CZ Bren 2 MS is a modular rifle that is lighter, more compact, and more durable than previous generations.', 1799.99, 9, 'cz_bren_2.jpg', 'cz-bren-2-ms', FALSE);

-- Create admin user
INSERT INTO users (username, email, password, first_name, last_name, is_admin) VALUES
('admin', 'admin@militariaprzemka.com', '$2y$10$6SuTxAXkSQDLjGqSzVJtCeZrD1vQPMvxO6HGR/ZzpXGpSPDrNwQDC', 'Admin', 'User', TRUE);
-- Password is 'admin123' (hashed)