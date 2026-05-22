<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $condition = $_POST['condition'] ?? 'new';

    // Check if book already in cart
    $exists = false;
    foreach ($_SESSION['cart'] as $item) {
        if ($item['book_id'] == $book_id) {
            $exists = true;
            break;
        }
    }

    if ($exists) {
        header("Location: ../book-detail.php?id=$book_id&msg=exists");
        exit();
    } else {
        $_SESSION['cart'][] = [
            'book_id' => $book_id,
            'condition' => $condition
        ];
        header("Location: ../cart.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
