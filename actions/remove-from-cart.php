<?php
session_start();
include '../config/db.php';
if (isset($_GET['id'])) {
    $remove_id = intval($_GET['id']);
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['book_id'] == $remove_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}
header("Location: ../cart.php");
exit();
?>
