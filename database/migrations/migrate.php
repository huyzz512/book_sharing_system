<?php
include 'config/db.php';

$queries = [
    "ALTER TABLE rentals ADD COLUMN customer_name VARCHAR(255) NULL AFTER status;",
    "ALTER TABLE rentals ADD COLUMN customer_phone VARCHAR(20) NULL AFTER customer_name;",
    "ALTER TABLE rentals ADD COLUMN customer_email VARCHAR(255) NULL AFTER customer_phone;",
    "ALTER TABLE rentals ADD COLUMN customer_address TEXT NULL AFTER customer_email;",
    "ALTER TABLE rentals ADD COLUMN book_condition VARCHAR(10) DEFAULT 'new' AFTER customer_address;",
    "ALTER TABLE rentals ADD COLUMN price_per_day DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER book_condition;"
];

$successCount = 0;
foreach ($queries as $query) {
    if ($conn->query($query)) {
        $successCount++;
        echo "<p style='color:green;'>Thực thi thành công: $query</p>";
    } else {
        echo "<p style='color:red;'>Lỗi hoặc cột đã tồn tại: " . $conn->error . "</p>";
    }
}

echo "<h3>Hoàn tất quá trình Migrate Database ($successCount/" . count($queries) . " thành công).</h3>";
echo "<a href='index.php'>Quay lại trang chủ</a>";
?>
