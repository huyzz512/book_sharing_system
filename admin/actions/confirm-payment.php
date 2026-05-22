<?php
include '../../config/db.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    if ($type == 'deposit') {
        $conn->query("UPDATE orders SET deposit_paid = 1, status = 'borrowing' WHERE id = $id");
    } elseif ($type == 'full') {
        $conn->query("UPDATE orders SET full_paid = 1, status = 'borrowing' WHERE id = $id");
    }

    header("Location: ../manage-rentals.php");
}
exit();
?>
