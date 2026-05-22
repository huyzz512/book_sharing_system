<?php
include __DIR__ . '/../../config/db.php';

$queries = [
    // 1. Cập nhật default reputation_points cho users
    "ALTER TABLE users ALTER COLUMN reputation_points SET DEFAULT 70;",
    
    // 2. Cập nhật bảng books
    // Thêm các cột quản lý tồn kho và giá trị sách
    "ALTER TABLE books ADD COLUMN book_value DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER rental_price;",
    "ALTER TABLE books ADD COLUMN stock_new INT NOT NULL DEFAULT 0 AFTER book_value;",
    "ALTER TABLE books ADD COLUMN available_new INT NOT NULL DEFAULT 0 AFTER stock_new;",
    "ALTER TABLE books ADD COLUMN stock_old INT NOT NULL DEFAULT 0 AFTER available_new;",
    "ALTER TABLE books ADD COLUMN available_old INT NOT NULL DEFAULT 0 AFTER stock_old;",
    
    // Chuyển dữ liệu cũ sang mới (Giả sử toàn bộ stock cũ là Sách Mới để không mất dữ liệu)
    "UPDATE books SET stock_new = total_stock, available_new = available_stock, book_value = deposit_price;",
    
    // (Tùy chọn) Xóa các cột cũ nếu không dùng nữa, nhưng tạm thời giữ để tránh lỗi code cũ chưa update kịp
    // "ALTER TABLE books DROP COLUMN total_stock, DROP COLUMN available_stock, DROP COLUMN deposit_price;",
    
    // 3. Cập nhật bảng orders
    "ALTER TABLE orders ADD COLUMN total_deposit DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total_price;",
    
    // 4. Cập nhật bảng order_items
    "ALTER TABLE order_items ADD COLUMN deposit_price DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER price_per_day;"
];

$successCount = 0;
foreach ($queries as $query) {
    if ($conn->query($query)) {
        $successCount++;
        echo "<p style='color:green;'>Thực thi thành công: $query</p>";
    } else {
        // Bỏ qua lỗi nếu cột đã tồn tại
        if (strpos($conn->error, 'Duplicate column name') !== false) {
            $successCount++;
            echo "<p style='color:orange;'>Cột đã tồn tại: $query</p>";
        } else {
            echo "<p style='color:red;'>Lỗi: " . $conn->error . "</p>";
        }
    }
}

echo "<h3>Hoàn tất quá trình Migrate 4 ($successCount/" . count($queries) . " thành công).</h3>";
?>
