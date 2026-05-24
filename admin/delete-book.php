<?php
include '../config/db.php';
session_start();

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Có thể bạn sẽ cần kiểm tra xem sách có đang được mượn hay không trước khi xóa
    // Tạm thời xóa trực tiếp
    $conn->query("DELETE FROM books WHERE id=$id");
    
    header("Location: manage-books.php?msg=deleted");
} else {
    header("Location: manage-books.php");
}
exit();
?>
