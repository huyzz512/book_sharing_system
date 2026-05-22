<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $user_id = $_SESSION['user_id'];
    
    // Thu thập thông tin khách hàng
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $customer_email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $customer_address = mysqli_real_escape_string($conn, $_POST['customer_address']);
    $pickup_date = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $order_date = date('Y-m-d H:i:s');

    // Kiểm tra điểm uy tín
    $user_check = $conn->query("SELECT reputation_points FROM users WHERE id = $user_id");
    $rep_points = 70;
    if ($user_check && $user_check->num_rows > 0) {
        $rep_points = $user_check->fetch_assoc()['reputation_points'];
        if ($rep_points < 50) {
            echo "<script>alert('Điểm uy tín của bạn quá thấp (" . $rep_points . " < 50). Không thể mượn thêm sách. Vui lòng liên hệ Admin để giải quyết các đơn hàng quá hạn.'); window.location.href='../cart.php';</script>";
            exit();
        }
    }

    $conn->begin_transaction();
    try {
        // Tính số ngày mượn
        $start = new DateTime($pickup_date);
        $end = new DateTime($due_date);
        $interval = $start->diff($end);
        $days = $interval->days > 0 ? $interval->days : 1;
        $total_price_per_day = 0;

        $deposit_rate = 1.0;
        if ($rep_points >= 80) $deposit_rate = 0.15;
        elseif ($rep_points >= 70) $deposit_rate = 0.40;

        // Kiểm tra kho và tính tổng tiền 1 ngày trước
        $total_deposit = 0;
        foreach ($_SESSION['cart'] as $item) {
            $book_id = $item['book_id'];
            $condition = $item['condition'];
            $res = $conn->query("SELECT rental_price, book_value, available_new, available_old FROM books WHERE id = $book_id");
            $book = $res->fetch_assoc();
            
            if (!$book) {
                throw new Exception("Sách không tồn tại.");
            }
            if ($condition === 'new' && $book['available_new'] <= 0) {
                throw new Exception("Sách mới trong giỏ đã hết hàng.");
            }
            if ($condition === 'old' && $book['available_old'] <= 0) {
                throw new Exception("Sách cũ trong giỏ đã hết hàng.");
            }
            
            $price = $book['rental_price'];
            if ($item['condition'] === 'old') {
                $price = $price * 0.8;
            }
            $total_price_per_day += $price;
            $total_deposit += ($book['book_value'] * $deposit_rate);
        }

        $total_price_order = $total_price_per_day * $days;

        // Thêm vào bảng orders
        $sql_order = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_email, customer_address, pickup_date, due_date, total_price, total_deposit, status, order_date) 
                      VALUES ($user_id, '$customer_name', '$customer_phone', '$customer_email', '$customer_address', '$pickup_date', '$due_date', $total_price_order, $total_deposit, 'pending', '$order_date')";
        
        if(!$conn->query($sql_order)) {
            throw new Exception("Lỗi tạo đơn hàng: " . $conn->error);
        }
        
        $order_id = $conn->insert_id;

        // Thêm vào bảng order_items và trừ kho
        foreach ($_SESSION['cart'] as $item) {
            $book_id = $item['book_id'];
            $condition = $item['condition'];

            $res = $conn->query("SELECT rental_price, book_value FROM books WHERE id = $book_id");
            $book = $res->fetch_assoc();
            
            $price_per_day = $book['rental_price'];
            if ($condition === 'old') {
                $price_per_day = $price_per_day * 0.8;
            }
            $item_deposit = $book['book_value'] * $deposit_rate;

            // Trừ kho
            if ($condition === 'new') {
                $conn->query("UPDATE books SET available_new = available_new - 1 WHERE id = $book_id");
            } else {
                $conn->query("UPDATE books SET available_old = available_old - 1 WHERE id = $book_id");
            }

            // Thêm order_item
            $sql_item = "INSERT INTO order_items (order_id, book_id, book_condition, price_per_day, deposit_price) 
                         VALUES ($order_id, $book_id, '$condition', $price_per_day, $item_deposit)";
            $conn->query($sql_item);
        }

        $conn->commit();
        // Xoá giỏ hàng sau khi đặt thành công
        unset($_SESSION['cart']);
        header("Location: ../profile.php?status=success");
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location.href='../cart.php';</script>";
    }
} else {
    header("Location: ../index.php");
}
?>
