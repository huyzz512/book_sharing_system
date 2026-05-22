<?php 
include 'config/db.php';
include 'includes/functions.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header("Location: index.php");
    exit();
}

$total_per_day = 0;
$total_deposit = 0;

$user_id = $_SESSION['user_id'];
$user_q = $conn->query("SELECT reputation_points FROM users WHERE id = $user_id");
$user_points = $user_q->fetch_assoc()['reputation_points'];

$deposit_rate = 1.0; // 100%
if ($user_points >= 80) {
    $deposit_rate = 0.15; // 15%
} elseif ($user_points >= 70) {
    $deposit_rate = 0.40; // 40%
}
?>
<div class="container" style="margin-top: 40px;">
    <h1 class="text-gradient" style="margin-bottom: 30px;">Thông Tin Thanh Toán</h1>

    <form action="actions/process-checkout.php" method="POST" class="checkout-grid">
        <!-- Form Thông Tin Khách Hàng -->
        <div class="glass-card" style="padding: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--accent-gold);">Thông tin người nhận</h3>
            
            <div class="form-group">
                <label class="form-label">Họ và tên:</label>
                <input type="text" name="customer_name" required class="form-control" placeholder="Nhập họ tên đầy đủ">
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại:</label>
                <input type="text" name="customer_phone" required class="form-control" placeholder="Nhập số điện thoại">
            </div>

            <div class="form-group">
                <label class="form-label">Email liên hệ:</label>
                <input type="email" name="customer_email" required class="form-control" placeholder="Nhập email">
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ giao/nhận sách:</label>
                <textarea name="customer_address" required class="form-control" placeholder="Nhập địa chỉ chi tiết" style="height: 100px; resize: vertical;"></textarea>
            </div>

            <div class="checkout-grid" style="grid-template-columns: 1fr 1fr; margin-top: 0; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Ngày đến lấy sách:</label>
                    <input type="date" name="pickup_date" required class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày hẹn trả sách:</label>
                    <input type="date" name="due_date" required class="form-control">
                </div>
            </div>
        </div>

        <!-- Tóm tắt đơn hàng -->
        <div class="glass-card" style="padding: 30px; height: max-content;">
            <h3 style="margin-bottom: 20px; color: var(--accent-gold);">Đơn hàng của bạn</h3>
            
            <div style="margin-bottom: 20px; max-height: 300px; overflow-y: auto;">
                <?php 
                foreach ($cart_items as $item): 
                    $book_id = $item['book_id'];
                    $res = $conn->query("SELECT title, rental_price, book_value, cover_image FROM books WHERE id = $book_id");
                    if ($res && $res->num_rows > 0):
                        $book = $res->fetch_assoc();
                        $price = $book['rental_price'];
                        if ($item['condition'] === 'old') {
                            $price = $price * 0.8;
                        }
                        $total_per_day += $price;

                        $item_deposit = $book['book_value'] * $deposit_rate;
                        $total_deposit += $item_deposit;
                ?>
                <div class="d-flex gap-2" style="margin-bottom: 15px; border-bottom: 1px solid var(--border-glass); padding-bottom: 15px;">
                    <img src="uploads/books/<?php echo htmlspecialchars($book['cover_image']); ?>" class="cart-item-img" alt="Cover" style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px;">
                    <div style="flex: 1;">
                        <h5 style="margin-bottom: 5px;"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">
                            <?php echo $item['condition'] == 'new' ? 'Sách Mới' : 'Sách Cũ (-20%)'; ?>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div style="color: var(--accent-gold); font-weight: bold;">
                                Thuê: <?php echo number_format($price, 0, ',', '.'); ?>đ/ngày
                            </div>
                            <div style="color: #e74c3c; font-weight: bold; font-size: 0.9rem;">
                                Cọc (<?php echo $deposit_rate * 100; ?>%): <?php echo number_format($item_deposit, 0, ',', '.'); ?>đ
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>

            <div style="background: rgba(59, 130, 246, 0.05); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <p style="margin: 0 0 10px 0; font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
                    Tổng phí thuê: <b style="color: var(--accent-gold); font-size: 1.1rem; float: right;"><?php echo number_format($total_per_day, 0, ',', '.'); ?>đ/ngày</b>
                </p>
                <div style="clear: both;"></div>
                <p style="margin: 0; font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; border-top: 1px dashed #ddd; padding-top: 10px;">
                    Tổng tiền cọc: <b style="color: #e74c3c; font-size: 1.1rem; float: right;"><?php echo number_format($total_deposit, 0, ',', '.'); ?>đ</b>
                </p>
                <div style="clear: both;"></div>
                <p style="margin-top: 15px; font-size: 0.85rem; text-align: center;">Tổng thanh toán sẽ bao gồm Phí thuê (nhân số ngày) + Tiền cọc.</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;">
                Xác nhận đặt sách
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
