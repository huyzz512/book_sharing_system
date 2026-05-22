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

    $user_id = $_SESSION['user_id'];
    $user_q = $conn->query("SELECT reputation_points FROM users WHERE id = $user_id");
    $user_points = $user_q->fetch_assoc()['reputation_points'];

    if ($user_points < 50) {
        header("Location: ../book-detail.php?id=$book_id&msg=blocked");
        exit();
    }

    if ($condition === 'new' && $user_points < 80) {
        header("Location: ../book-detail.php?id=$book_id&msg=low_rep");
        exit();
    }

    // Kiểm tra tồn kho
    $book_q = $conn->query("SELECT available_new, available_old FROM books WHERE id = $book_id");
    $book = $book_q->fetch_assoc();
    
    if ($condition === 'new' && $book['available_new'] <= 0) {
        header("Location: ../book-detail.php?id=$book_id&msg=out_of_stock");
        exit();
    }
    if ($condition === 'old' && $book['available_old'] <= 0) {
        header("Location: ../book-detail.php?id=$book_id&msg=out_of_stock");
        exit();
    }

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
