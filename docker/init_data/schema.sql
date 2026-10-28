DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories(
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL
);

CREATE TABLE products(
    product_id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(8,2),
    in_stock BOOLEAN NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);


INSERT INTO categories (category_id, category_name)
SELECT
i, 'category_name' || i
FROM generate_series(1, 10) AS i;

INSERT INTO products (product_id, category_id, product_name, product_price, in_stock)
WITH product_id_list AS (
    SELECT c.category_id, i, ROW_NUMBER() OVER(ORDER BY c.category_id, i) AS product_id
    FROM categories AS c CROSS JOIN generate_series(1, 10) AS i
)
SELECT product_id, category_id, 'product_name_' || product_id AS product_name, 20 * (floor(random() * 1000) + 1), CASE WHEN i % 2 = 0 THEN TRUE ELSE FALSE END AS in_stock
FROM product_id_list