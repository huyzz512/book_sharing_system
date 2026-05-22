<?php
include 'config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        customer_name VARCHAR(255),
        customer_phone VARCHAR(20),
        customer_email VARCHAR(255),
        customer_address TEXT,
        pickup_date DATE,
        due_date DATE,
        total_price DECIMAL(10,2),
        status VARCHAR(20) DEFAULT 'pending',
        order_date DATETIME
    )",
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        book_id INT,
        book_condition VARCHAR(10),
        price_per_day DECIMAL(10,2),
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )"
];

$successCount = 0;
foreach ($queries as $query) {
    if ($conn->query($query)) {
        $successCount++;
        echo "<p style='color:green;'>Thực thi thành công: $query</p>";
    } else {
        echo "<p style='color:red;'>Lỗi: " . $conn->error . "</p>";
    }
}

echo "<h3>Hoàn tất quá trình Migrate 2 ($successCount/" . count($queries) . " thành công).</h3>";
?>
