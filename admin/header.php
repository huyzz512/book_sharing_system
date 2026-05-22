<?php
// admin/header.php
$current_page = basename($_SERVER['PHP_SELF']);

// Tự động cập nhật quá hạn và trừ điểm
$new_overdues = $conn->query("SELECT user_id FROM orders WHERE status = 'borrowing' AND due_date < CURDATE()");
if($new_overdues && $new_overdues->num_rows > 0) {
    while($row = $new_overdues->fetch_assoc()) {
        $uid = $row['user_id'];
        $conn->query("UPDATE users SET reputation_points = reputation_points - 10 WHERE id = $uid");
    }
    $conn->query("UPDATE orders SET status = 'overdue' WHERE status = 'borrowing' AND due_date < CURDATE()");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7f6; }
        header { width: 100%; background: #2c3e50; color: white; position: fixed; top: 0; left: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; height: 70px; }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: bold; font-size: 1.2rem; color: #fff; text-decoration: none; }
        nav ul { list-style: none; padding: 0; margin: 0; display: flex; gap: 5px; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 10px 15px; border-radius: 5px; font-size: 0.9rem; font-weight: 500; transition: 0.3s; }
        .nav-link:hover { background: #34495e; color: #3498db; }
        .nav-link.active { background: #3498db; color: white; }
        .btn-exit { color: #bdc3c7; text-decoration: none; font-size: 0.8rem; border: 1px solid #7f8c8d; padding: 5px 12px; border-radius: 20px; transition: 0.3s; }
        .btn-exit:hover { background: #e74c3c; color: white; border-color: #e74c3c; }
        main { max-width: 1200px; margin: 90px auto 40px; padding: 0 20px; }
        .admin-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f8f9fa; padding: 15px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .status-pill { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-borrowing { background: #d1ecf1; color: #0c5460; }
        .status-returned { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
<header>
    <div class="nav-container">
        <a href="index.php" class="logo">📚 BOOK ADMIN</a>
        <nav>
            <ul>
                <li><a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="manage-books.php" class="nav-link <?php echo ($current_page == 'manage-books.php' || $current_page == 'add-book.php' || $current_page == 'edit-book.php') ? 'active' : ''; ?>">Sách</a></li>
                <li><a href="manage-categories.php" class="nav-link <?php echo ($current_page == 'manage-categories.php') ? 'active' : ''; ?>">Danh mục</a></li>
                <li><a href="manage-rentals.php" class="nav-link <?php echo ($current_page == 'manage-rentals.php') ? 'active' : ''; ?>">Mượn/Trả</a></li>
                <li><a href="revenue.php" class="nav-link <?php echo ($current_page == 'revenue.php') ? 'active' : ''; ?>">Doanh thu</a></li>
                <li><a href="manage-users.php" class="nav-link <?php echo ($current_page == 'manage-users.php') ? 'active' : ''; ?>">Thành viên</a></li>
            </ul>
        </nav>
        <a href="../index.php" class="btn-exit">Thoát Admin</a>
    </div>
</header>