<?php
$db_server = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "accounts";
$port = 3308;

$conn = mysqli_connect($db_server, $db_username, $db_password, $db_name, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Table 0 - user_email
$sql0 = "CREATE TABLE IF NOT EXISTS user_email (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL
)";
if (!mysqli_query($conn, $sql0)) {
    echo "Error creating user_email: " . mysqli_error($conn) . "<br>";
}

// Table 1 - products FIRST
$sql2 = "CREATE TABLE IF NOT EXISTS products (
    product_id  INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(255) NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    quantity    INT NOT NULL DEFAULT 0,
    is_in_stock TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $sql2)) {
    echo "Error creating products: " . mysqli_error($conn) . "<br>";
}

// Table 2 - orders SECOND (references products)
$sql1 = "CREATE TABLE IF NOT EXISTS orders (
    order_id    INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT DEFAULT NULL,
    email       VARCHAR(255) NOT NULL,
    wilaya      VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    first_name  VARCHAR(100),
    addresss    VARCHAR(255) NOT NULL,
    apartment   VARCHAR(255),
    city        VARCHAR(100) NOT NULL,
    phone       VARCHAR(20)  NOT NULL,
    product_id  INT DEFAULT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_email(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
)";
if (!mysqli_query($conn, $sql1)) {
    echo "Error creating orders: " . mysqli_error($conn) . "<br>";
}

// Trigger
mysqli_query($conn, "DROP TRIGGER IF EXISTS after_order_insert");
$trigger = "CREATE TRIGGER after_order_insert
    AFTER INSERT ON orders
    FOR EACH ROW
    UPDATE products
    SET 
        quantity = GREATEST(quantity - NEW.quantity, 0),
        is_in_stock = IF(quantity - NEW.quantity <= 0, 0, 1)
    WHERE product_id = NEW.product_id";
if (!mysqli_query($conn, $trigger)) {
    echo "Error creating trigger: " . mysqli_error($conn) . "<br>";
}

// Insert product only if not exists
$sql3 = "INSERT INTO products (name, price, quantity, is_in_stock, created_at)
         SELECT 'Supercharged Glow Complex', 80, 100, 1, NOW()
         WHERE NOT EXISTS (
             SELECT 1 FROM products WHERE name = 'Supercharged Glow Complex'
         )";
if (!mysqli_query($conn, $sql3)) {
    echo "Error inserting product: " . mysqli_error($conn) . "<br>";
}
?>
