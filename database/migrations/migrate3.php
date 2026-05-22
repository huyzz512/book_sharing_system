<?php
include 'config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) UNIQUE,
        setting_value TEXT
    )",
    "INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
        ('bank_bin', '970436'), 
        ('bank_account', '123456789'), 
        ('bank_name', 'NGUYEN VAN A')",
    "ALTER TABLE orders ADD COLUMN deposit_paid TINYINT(1) DEFAULT 0",
    "ALTER TABLE orders ADD COLUMN full_paid TINYINT(1) DEFAULT 0"
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

echo "<h3>Hoàn tất quá trình Migrate 3 ($successCount/" . count($queries) . " thành công).</h3>";
?>
