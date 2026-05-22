<?php 
include '../config/db.php';
session_start();
// Lấy thống kê
$total_books = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$pending_rentals = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];

// Tính doanh thu từ bảng orders
$rev_res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'returned'");
$total_revenue = $rev_res ? $rev_res->fetch_assoc()['total'] : 0;
if (!$total_revenue) $total_revenue = 0;

$recent_rentals = $conn->query("SELECT orders.*, users.username, 
                               (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id) as total_books
                               FROM orders 
                               JOIN users ON orders.user_id = users.id 
                               ORDER BY order_date DESC LIMIT 5");

include 'header.php'; 
?>
<title>Admin Dashboard</title>
<main>
    <h1 style="margin-bottom: 30px; color: #2c3e50;">📊 Thống kê hệ thống</h1>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="admin-card" style="border-left: 5px solid #3498db;"><h3>Tổng sách</h3><p style="font-size: 2rem; margin: 0;"><?php echo $total_books; ?></p></div>
        <div class="admin-card" style="border-left: 5px solid #f1c40f;"><h3>Chờ xử lý</h3><p style="font-size: 2rem; margin: 0; color: #f1c40f;"><?php echo $pending_rentals; ?></p></div>
        <div class="admin-card" style="border-left: 5px solid #2ecc71;"><h3>Thành viên</h3><p style="font-size: 2rem; margin: 0;"><?php echo $total_users; ?></p></div>
        <div class="admin-card" style="border-left: 5px solid #e74c3c;"><h3>Doanh Thu</h3><p style="font-size: 1.8rem; margin: 0; color: #e74c3c;"><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</p></div>
    </div>

    <div class="admin-card">
        <h2 style="margin-bottom: 20px;">Yêu cầu mượn gần đây</h2>
        <table>
            <thead>
                <tr><th>Người mượn</th><th>Đơn Hàng</th><th>Trạng thái</th></tr>
            </thead>
            <tbody>
                <?php while($row = $recent_rentals->fetch_assoc()): ?>
                <tr>
                    <td><b><?php echo htmlspecialchars($row['username']); ?></b></td>
                    <td>
                        <b style="color: #3498db;">#<?php echo $row['id']; ?></b>
                        <small style="color: #7f8c8d; margin-left: 5px;">(<?php echo $row['total_books']; ?> sách)</small>
                    </td>
                    <td><span class="status-pill status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body></html>