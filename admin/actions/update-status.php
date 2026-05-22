<?php
include '../../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['rental_id']);
    $status = $_POST['new_status'];
    $old_status_res = $conn->query("SELECT status FROM orders WHERE id = $id");
    if ($old_status_res && $old_status_res->num_rows > 0) {
        $old_status = $old_status_res->fetch_assoc()['status'];
        
        // Hoàn trả số lượng sách nếu trạng thái mới là returned hoặc cancelled
        if (($status == 'returned' || $status == 'cancelled') && ($old_status != 'returned' && $old_status != 'cancelled')) {
            $items = $conn->query("SELECT book_id FROM order_items WHERE order_id = $id");
            while ($item = $items->fetch_assoc()) {
                $book_id = $item['book_id'];
                $conn->query("UPDATE books SET available_stock = available_stock + 1 WHERE id = $book_id");
            }
        }

        // Thưởng 2 điểm uy tín nếu chuyển sang returned
        if ($status == 'returned' && $old_status != 'returned') {
            $user_res = $conn->query("SELECT user_id FROM orders WHERE id = $id");
            if ($user_res && $user_res->num_rows > 0) {
                $uid = $user_res->fetch_assoc()['user_id'];
                $conn->query("UPDATE users SET reputation_points = reputation_points + 2 WHERE id = $uid");
            }
        }
    }
    
    $conn->query("UPDATE orders SET status = '$status' WHERE id = $id");
    header("Location: ../manage-rentals.php");
}
exit();