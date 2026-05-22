<?php 
include '../config/db.php';
session_start();

if(!isset($_GET['id'])) {
    header("Location: manage-users.php");
    exit;
}

$user_id = intval($_GET['id']);
$user_info = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$sql = "SELECT orders.*,
               (SELECT GROUP_CONCAT(books.title SEPARATOR ' | ') 
                FROM order_items 
                JOIN books ON order_items.book_id = books.id 
                WHERE order_items.order_id = orders.id) as book_titles
        FROM orders 
        WHERE orders.user_id = $user_id 
        ORDER BY orders.order_date DESC";
$orders = $conn->query($sql);

include 'header.php'; 
?>
<title>Lịch sử của <?php echo htmlspecialchars($user_info['username']); ?></title>
<main>
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
        <h1 style="margin: 0; color: #2c3e50;">📖 Lịch sử mượn sách của: <span style="color: #3498db;"><?php echo htmlspecialchars($user_info['username']); ?></span></h1>
        <a href="manage-users.php" style="background: #95a5a6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Quay lại</a>
    </div>
    
    <div class="admin-card">
        <?php if ($orders->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Mã Đơn / Ngày Đặt</th>
                    <th>Sách</th>
                    <th>Ngày mượn/trả</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $orders->fetch_assoc()): ?>
                <tr>
                    <td>
                        <b style="color: #3498db;">#<?php echo $row['id']; ?></b><br>
                        <small style="color: #7f8c8d;"><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></small>
                    </td>
                    <td>
                        <div style="font-size: 0.95rem;">
                            <?php 
                                $titles = explode(' | ', $row['book_titles']);
                                foreach($titles as $t) {
                                    echo "• " . htmlspecialchars($t) . "<br>";
                                }
                            ?>
                        </div>
                    </td>
                    <td>
                        <span style="color: #2980b9;">Lấy:</span> <?php echo $row['pickup_date'] ? date('d/m/Y', strtotime($row['pickup_date'])) : '...'; ?><br>
                        <span style="color: #c0392b;">Trả:</span> <?php echo $row['due_date'] ? date('d/m/Y', strtotime($row['due_date'])) : '...'; ?>
                    </td>
                    <td style="color: #e74c3c; font-weight: bold;">
                        <?php echo number_format($row['total_price'], 0, ',', '.'); ?>đ
                    </td>
                    <td>
                        <span class="status-pill status-<?php echo $row['status']; ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align: center; color: #7f8c8d; font-size: 1.1rem; padding: 30px;">Thành viên này chưa mượn cuốn sách nào.</p>
        <?php endif; ?>
    </div>
</main>
</body></html>
