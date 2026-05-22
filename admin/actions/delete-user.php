<?php
include '../../config/db.php';
session_start();

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Không cho phép xóa chính mình (nếu admin đang đăng nhập)
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Bạn không thể xóa chính mình!'); window.location.href='../manage-users.php';</script>";
        exit;
    }

    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: ../manage-users.php?msg=deleted");
} else {
    header("Location: ../manage-users.php");
}
exit();
?>
