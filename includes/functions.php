<?php
// 1. Hàm định dạng tiền tệ Việt Nam
function formatMoney($number) {
    return number_format($number, 0, ',', '.') . ' VNĐ';
}

// 2. Hàm kiểm tra đăng nhập
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth/login.php");
        exit();
    }
}

// 3. Hàm tính toán điểm uy tín (Reputation logic)
// Trả về màu sắc dựa trên số điểm
function getReputationStatus($points) {
    if ($points >= 80) return ['label' => 'Tuyệt vời', 'color' => '#27ae60'];
    if ($points >= 50) return ['label' => 'Trung bình', 'color' => '#f39c12'];
    return ['label' => 'Thấp (Hạn chế mượn)', 'color' => '#e74c3c'];
}

// 4. Hàm cập nhật số lượng kho (Stock Update)
// $type: 'borrow' (mượn - giảm kho) hoặc 'return' (trả - tăng kho)
function updateBookStock($conn, $book_id, $type) {
    if ($type == 'borrow') {
        $sql = "UPDATE books SET available_stock = available_stock - 1 WHERE id = $book_id AND available_stock > 0";
    } else {
        $sql = "UPDATE books SET available_stock = available_stock + 1 WHERE id = $book_id";
    }
    return $conn->query($sql);
}

// 5. Hàm rút gọn mô tả sách (dùng cho trang chủ)
function limitText($text, $limit = 100) {
    if (strlen($text) <= $limit) return $text;
    return substr($text, 0, $limit) . '...';
}
?>