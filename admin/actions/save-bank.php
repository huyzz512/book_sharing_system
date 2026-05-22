<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bank_bin = mysqli_real_escape_string($conn, $_POST['bank_bin']);
    $bank_account = mysqli_real_escape_string($conn, $_POST['bank_account']);
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);

    $conn->query("UPDATE settings SET setting_value = '$bank_bin' WHERE setting_key = 'bank_bin'");
    $conn->query("UPDATE settings SET setting_value = '$bank_account' WHERE setting_key = 'bank_account'");
    $conn->query("UPDATE settings SET setting_value = '$bank_name' WHERE setting_key = 'bank_name'");

    header("Location: ../manage-rentals.php");
}
exit();
?>
