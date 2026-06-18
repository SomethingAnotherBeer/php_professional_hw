DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO categories (id, category_name) VALUES
    (1, 'category name 1'),
    (2, 'category name 2');

INSERT INTO products (id, category_id, product_name) VALUES 
    (1, 1, 'product name 1'),
    (2, 1, 'product name 2'),
    (3, 2, 'product name 3'),
    (4, 2, 'product name 4');


SET FOREIGN_KEY_CHECKS = 1;