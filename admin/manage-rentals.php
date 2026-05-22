<?php 
include '../config/db.php'; 

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "";
if ($search) {
    $where_clause = " WHERE orders.id LIKE '%$search%' OR users.username LIKE '%$search%' ";
}

// Truy vấn lấy thông tin mượn sách từ bảng orders
$sql = "SELECT orders.*, users.username,
               (SELECT GROUP_CONCAT(books.title SEPARATOR ' | ') 
                FROM order_items 
                JOIN books ON order_items.book_id = books.id 
                WHERE order_items.order_id = orders.id) as book_titles
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        $where_clause
        ORDER BY CASE WHEN orders.status = 'overdue' THEN 1 ELSE 2 END, orders.order_date DESC";
$orders = $conn->query($sql);

include 'header.php'; 
?>
<title>Quản lý mượn trả</title>
<main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;">🤝 Danh sách đơn</h1>
        
        <!-- Form Tìm Kiếm -->
        <form action="manage-rentals.php" method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm mã đơn, tên khách..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 220px; outline: none;">
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Tìm</button>
            <?php if($search): ?>
                <a href="manage-rentals.php" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none;">Xóa</a>
            <?php endif; ?>
        </form>

        <button onclick="document.getElementById('bankModal').style.display='block'" style="background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">🏦 Cấu hình ngân hàng</button>
    </div>
    <div class="admin-card">
        <table>
            <thead>
                <tr>
                    <th>Người mượn / Ngày đặt</th>
                    <th>Sách</th>
                    <th>Hạn trả</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $orders->fetch_assoc()): ?>
                <tr>
                    <td>
                        <b><?php echo htmlspecialchars($row['username']); ?></b><br>
                        <small style="color: #7f8c8d;"><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></small><br>
                        <span style="font-size: 0.85rem; color: #3498db;">Mã đơn: #<?php echo $row['id']; ?></span>
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
                    <td><?php echo ($row['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['due_date'])) : 'Chưa hẹn'; ?></td>
                    <td style="color: #e74c3c; font-weight: bold;">
                        <?php echo number_format($row['total_price'], 0, ',', '.'); ?>đ
                    </td>
                    <td>
                        <span class="status-pill status-<?php echo $row['status']; ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form action="actions/update-status.php" method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                            <input type="hidden" name="rental_id" value="<?php echo $row['id']; ?>">
                            <div style="display: flex; gap: 5px;">
                                <select name="new_status" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd; width: 100%;">
                                    <option value="pending" <?php if($row['status'] == 'pending') echo 'selected'; ?>>Chờ</option>
                                    <option value="confirmed" <?php if($row['status'] == 'confirmed') echo 'selected'; ?>>Đã xác nhận</option>
                                    <option value="borrowing" <?php if($row['status'] == 'borrowing') echo 'selected'; ?>>Đang mượn</option>
                                    <option value="returned" <?php if($row['status'] == 'returned') echo 'selected'; ?>>Đã trả</option>
                                    <option value="overdue" <?php if($row['status'] == 'overdue') echo 'selected'; ?>>Quá hạn</option>
                                    <option value="cancelled" <?php if($row['status'] == 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                                </select>
                                <button type="submit" style="background: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Lưu</button>
                            </div>
                            
                            <?php if($row['status'] == 'pending' && $row['deposit_paid'] == 0): ?>
                                <a href="actions/confirm-payment.php?id=<?php echo $row['id']; ?>&type=deposit" style="background: #f39c12; color: white; text-align: center; text-decoration: none; padding: 5px; border-radius: 4px; font-size: 0.85rem; display: block;">Xác nhận nhận cọc</a>
                            <?php endif; ?>

                            <?php if($row['status'] == 'confirmed' && $row['full_paid'] == 0): ?>
                                <button type="button" onclick="showQRModal(<?php echo $row['id']; ?>, <?php echo $row['total_price'] * 0.8; ?>)" style="background: #2ecc71; color: white; border: none; padding: 5px; border-radius: 4px; font-size: 0.85rem; cursor: pointer;">QR thanh toán 80%</button>
                                <a href="actions/confirm-payment.php?id=<?php echo $row['id']; ?>&type=full" style="background: #8e44ad; color: white; text-align: center; text-decoration: none; padding: 5px; border-radius: 4px; font-size: 0.85rem; display: block;">Xác nhận thanh toán toàn bộ</a>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
$settings_res = $conn->query("SELECT * FROM settings");
$settings = [];
while($srow = $settings_res->fetch_assoc()){
    $settings[$srow['setting_key']] = $srow['setting_value'];
}
$bank_bin = $settings['bank_bin'] ?? '970436';
$bank_account = $settings['bank_account'] ?? '';
$bank_name = $settings['bank_name'] ?? '';
?>

<!-- Modal Cấu hình Ngân hàng -->
<div id="bankModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:400px; margin: 100px auto; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top:0; color: #2c3e50;">🏦 Cấu hình Ngân Hàng</h3>
        <form action="actions/save-bank.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.9rem; color: #7f8c8d;">Ngân Hàng:</label>
                <select name="bank_bin" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px; box-sizing: border-box;">
                    <option value="970436" <?php if($bank_bin == '970436') echo 'selected'; ?>>Vietcombank (VCB)</option>
                    <option value="970415" <?php if($bank_bin == '970415') echo 'selected'; ?>>VietinBank (CTG)</option>
                    <option value="970418" <?php if($bank_bin == '970418') echo 'selected'; ?>>BIDV</option>
                    <option value="970405" <?php if($bank_bin == '970405') echo 'selected'; ?>>Agribank (VBA)</option>
                    <option value="970422" <?php if($bank_bin == '970422') echo 'selected'; ?>>MBBank (MB)</option>
                    <option value="970407" <?php if($bank_bin == '970407') echo 'selected'; ?>>Techcombank (TCB)</option>
                    <option value="970416" <?php if($bank_bin == '970416') echo 'selected'; ?>>ACB</option>
                    <option value="970432" <?php if($bank_bin == '970432') echo 'selected'; ?>>VPBank (VPB)</option>
                    <option value="970423" <?php if($bank_bin == '970423') echo 'selected'; ?>>TPBank (TPB)</option>
                    <option value="970403" <?php if($bank_bin == '970403') echo 'selected'; ?>>Sacombank (STB)</option>
                    <option value="970437" <?php if($bank_bin == '970437') echo 'selected'; ?>>HDBank</option>
                    <option value="970441" <?php if($bank_bin == '970441') echo 'selected'; ?>>VIB</option>
                    <option value="970443" <?php if($bank_bin == '970443') echo 'selected'; ?>>SHB</option>
                    <option value="970440" <?php if($bank_bin == '970440') echo 'selected'; ?>>SeABank</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.9rem; color: #7f8c8d;">Số Tài Khoản:</label>
                <input type="text" name="bank_account" value="<?php echo htmlspecialchars($bank_account); ?>" placeholder="Nhập số tài khoản" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-size: 0.9rem; color: #7f8c8d;">Tên Chủ Tài Khoản:</label>
                <input type="text" name="bank_name" value="<?php echo htmlspecialchars($bank_name); ?>" placeholder="Viết hoa không dấu" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ddd; border-radius:4px; box-sizing: border-box;">
            </div>
            <div style="display:flex; justify-content:space-between;">
                <button type="button" onclick="document.getElementById('bankModal').style.display='none'" style="background:#bdc3c7; color: white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Hủy</button>
                <button type="submit" style="background:#2ecc71; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Lưu cấu hình</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code -->
<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
    <div style="background:white; width:350px; margin: 100px auto; padding: 30px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top:0; color: #2c3e50;">Quét mã thanh toán</h3>
        <p style="color: #e74c3c; font-weight: bold; font-size: 1.2rem;" id="qrAmountDisplay"></p>
        <img id="qrImage" src="" alt="QR Code" style="width: 250px; height: 250px; margin: 20px 0; border: 1px solid #eee; border-radius: 10px; padding: 10px;">
        <p style="font-size: 0.9rem; color: #7f8c8d;">Chuyển khoản với nội dung: <b id="qrContent"></b></p>
        <button type="button" onclick="document.getElementById('qrModal').style.display='none'" style="background:#3498db; color:white; border:none; padding:10px 30px; border-radius:4px; cursor:pointer; margin-top: 10px; font-weight: bold;">Đóng</button>
    </div>
</div>

<script>
function showQRModal(orderId, amount) {
    const bin = '<?php echo $bank_bin; ?>';
    const account = '<?php echo $bank_account; ?>';
    const name = '<?php echo urlencode($bank_name); ?>';
    const content = `Thanh toan don hang ${orderId}`;
    
    // VietQR API Format
    const qrUrl = `https://img.vietqr.io/image/${bin}-${account}-compact2.png?amount=${amount}&addInfo=${encodeURIComponent(content)}&accountName=${name}`;
    
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmountDisplay').innerText = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    document.getElementById('qrContent').innerText = content;
    document.getElementById('qrModal').style.display = 'block';
}
</script>

</body></html>