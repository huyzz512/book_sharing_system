<?php
// Khởi động session để lấy user_id của người đang đăng nhập
session_start();

// Kết nối cơ sở dữ liệu
include '../config/db.php';

// Kiểm tra nếu người dùng gửi dữ liệu qua phương thức POST và đã đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    
    // 1. Thu thập và làm sạch dữ liệu đầu vào
    $user_id = $_SESSION['user_id'];
    $book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
    $rating  = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    
    // Làm sạch chuỗi bình luận để tránh lỗi SQL và bảo mật
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // 2. Kiểm tra tính hợp lệ của thang điểm (Đảm bảo luôn nằm trong khoảng 1-5)
    if ($rating < 1) {
        $rating = 1;
    } elseif ($rating > 5) {
        $rating = 5;
    }

    // 3. Kiểm tra ID sách hợp lệ trước khi chèn vào database
    if ($book_id > 0) {
        // Câu lệnh SQL chèn dữ liệu vào bảng reviews
        // Lưu ý: Cột created_at sẽ tự động lấy thời gian hiện tại nếu bạn dùng NOW()
        $sql = "INSERT INTO reviews (book_id, user_id, rating, comment, created_at) 
                VALUES ($book_id, $user_id, $rating, '$comment', NOW())";
        
        if ($conn->query($sql)) {
            // Thành công: Chuyển hướng về trang chi tiết sách kèm thông báo thành công
            header("Location: ../book-detail.php?id=$book_id&status=review_success");
        } else {
            // Thất bại: Ghi nhật ký lỗi hoặc hiển thị thông báo lỗi database
            // Trong môi trường thực tế, bạn nên dùng header và báo lỗi thay vì echo
            echo "Lỗi hệ thống: " . $conn->error;
        }
    } else {
        // ID sách không hợp lệ
        header("Location: ../index.php");
    }

} else {
    // Nếu truy cập file này trực tiếp hoặc chưa đăng nhập thì đẩy về trang chủ
    header("Location: ../index.php");
}

// Ngắt kết nối và kết thúc script
exit();
?>