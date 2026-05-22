<?php 
include 'config/db.php';
include 'includes/functions.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: auth/login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$u_res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $u_res->fetch_assoc();

// Lấy danh sách Đơn Hàng
$orders_query = $conn->query("
    SELECT orders.*, 
           (SELECT GROUP_CONCAT(books.title SEPARATOR ' | ') 
            FROM order_items 
            JOIN books ON order_items.book_id = books.id 
            WHERE order_items.order_id = orders.id) as book_titles,
           (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id) as total_books
    FROM orders 
    WHERE orders.user_id = $user_id 
    ORDER BY orders.order_date DESC
");
?>

<div class="container">
    <div class="profile-wrapper">
        <aside class="profile-sidebar glass-card">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <h2 class="profile-name text-gradient"><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            <div class="profile-stats">
                <span class="text-muted">Điểm uy tín:</span>
                <?php
                $rep_color = 'var(--accent-gold)';
                if ($user['reputation_points'] > 80) $rep_color = '#2ecc71';
                elseif ($user['reputation_points'] < 50) $rep_color = '#e74c3c';
                ?>
                <b style="font-size: 1.2rem; color: <?php echo $rep_color; ?>; margin-left: 10px;"><?php echo $user['reputation_points']; ?></b>
            </div>
        </aside>

        <section class="glass-card">
            <h3 class="section-title" style="margin: 0; padding: 30px 30px 10px; border-bottom: 1px solid var(--border-glass);">
                <span class="text-gradient">Lịch Sử Đơn Đặt Sách</span>
            </h3>
            
            <div class="rentals-table-container">
                <?php if ($orders_query->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã Đơn / Ngày Đặt</th>
                                <th>Thông tin sách</th>
                                <th>Ngày mượn/trả</th>
                                <th>Thành tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $orders_query->fetch_assoc()): 
                                // Tính toán số ngày
                                $days = 0;
                                if($row['pickup_date'] && $row['due_date']) {
                                    $start = new DateTime($row['pickup_date']);
                                    $end = new DateTime($row['due_date']);
                                    $days = $start->diff($end)->days ?: 1;
                                }
                            ?>
                            <tr>
                                <td>
                                    <b style="color: var(--accent-gold); font-size: 1.1rem;">#<?php echo $row['id']; ?></b><br>
                                    <small style="color: var(--text-muted);"><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></small>
                                </td>
                                <td>
                                    <div style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 5px;">
                                        <?php 
                                            $titles = explode(' | ', $row['book_titles']);
                                            foreach($titles as $t) {
                                                echo "• " . htmlspecialchars($t) . "<br>";
                                            }
                                        ?>
                                    </div>
                                    <span class="badge badge-pending" style="font-size: 0.75rem;"><?php echo $row['total_books']; ?> cuốn</span>
                                </td>
                                <td>
                                    <div style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.8;">
                                        <span style="color: var(--info);">Lấy:</span> <?php echo $row['pickup_date'] ? date('d/m/Y', strtotime($row['pickup_date'])) : '...'; ?><br>
                                        <span style="color: var(--danger);">Trả:</span> <?php echo $row['due_date'] ? date('d/m/Y', strtotime($row['due_date'])) : '...'; ?>
                                    </div>
                                </td>
                                <td>
                                    <b style="color: var(--danger); font-size: 1.2rem;"><?php echo number_format($row['total_price'], 0, ',', '.'); ?>đ</b>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;"><?php echo $days; ?> ngày</div>
                                    
                                    <?php if($row['status'] == 'pending' && $row['deposit_paid'] == 0): ?>
                                        <button type="button" onclick="showCustomerQR(<?php echo $row['id']; ?>, <?php echo $row['total_price'] * 0.2; ?>)" class="btn btn-primary" style="margin-top: 10px; font-size: 0.8rem; padding: 5px 10px;">Thanh toán cọc 20%</button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'badge-pending';
                                        if($row['status'] == 'borrowing') $statusClass = 'badge-borrowing';
                                        if($row['status'] == 'returned') $statusClass = 'badge-returned';
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo strtoupper($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: 40px; font-size: 1.1rem;">Bạn chưa có giao dịch mượn sách nào.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php
// Load bank config
$settings_res = $conn->query("SELECT * FROM settings");
$settings = [];
while($srow = $settings_res->fetch_assoc()){
    $settings[$srow['setting_key']] = $srow['setting_value'];
}
$bank_bin = $settings['bank_bin'] ?? '970436';
$bank_account = $settings['bank_account'] ?? '';
$bank_name = $settings['bank_name'] ?? '';
?>

<!-- Modal QR Code -->
<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items: center; justify-content: center;">
    <div style="background:white; width:350px; padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <h3 style="margin-top:0; color: var(--text-main);">Quét mã đặt cọc</h3>
        <p style="color: var(--danger); font-weight: bold; font-size: 1.5rem; margin: 10px 0;" id="qrAmountDisplay"></p>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; display: inline-block;">
            <img id="qrImage" src="" alt="QR Code" style="width: 250px; height: 250px;">
        </div>
        <p style="font-size: 0.95rem; color: var(--text-muted); margin-top: 15px;">Nội dung CK: <b id="qrContent" style="color: var(--text-main);"></b></p>
        <button type="button" onclick="document.getElementById('qrModal').style.display='none'" class="btn btn-outline" style="margin-top: 20px; width: 100%;">Đóng</button>
    </div>
</div>

<script>
function showCustomerQR(orderId, amount) {
    const bin = '<?php echo $bank_bin; ?>';
    const account = '<?php echo $bank_account; ?>';
    const name = '<?php echo urlencode($bank_name); ?>';
    const content = `Thanh toan coc don hang ${orderId}`;
    
    const qrUrl = `https://img.vietqr.io/image/${bin}-${account}-compact2.png?amount=${amount}&addInfo=${encodeURIComponent(content)}&accountName=${name}`;
    
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmountDisplay').innerText = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    document.getElementById('qrContent').innerText = content;
    document.getElementById('qrModal').style.display = 'flex';
}
</script>

<?php include 'includes/footer.php'; ?>