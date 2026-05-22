<?php
$host = "localhost";
$user = "root";
$pass = ""; // Mặc định của XAMPP là rỗng
$dbname = "book_sharing_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập tiếng Việt
$conn->set_charset("utf8mb4");
?>