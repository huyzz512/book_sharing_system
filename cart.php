<?php 
include 'config/db.php';
include 'includes/functions.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_per_day = 0;
?>

<div class="container" style="margin-top: 40px;">
    <h1 class="text-gradient" style="margin-bottom: 30px;">Giỏ Hàng Của Bạn</h1>

    <?php if (empty($cart_items)): ?>
        <div class="glass-card text-center" style="padding: 50px;">
            <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 20px;">Giỏ hàng của bạn đang trống.</p>
            <a href="index.php" class="btn btn-primary">Khám phá sách ngay</a>
        </div>
    <?php else: ?>
        <div class="glass-card" style="padding: 30px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sách</th>
                        <th>Tình trạng</th>
                        <th>Giá thuê/ngày</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($cart_items as $item): 
                        $book_id = $item['book_id'];
                        $res = $conn->query("SELECT * FROM books WHERE id = $book_id");
                        if ($res && $res->num_rows > 0):
                            $book = $res->fetch_assoc();
                            
                            // Calculate price based on condition
                            $price = $book['rental_price'];
                            if ($item['condition'] === 'old') {
                                $price = $price * 0.8; // 20% discount
                            }
                            $total_per_day += $price;
                    ?>
                    <tr>
                        <td>
                            <div class="table-book-info">
                                <img src="uploads/books/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover">
                                <div>
                                    <h4 style="color: var(--text-main);"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    <span style="font-size: 0.9rem; color: var(--text-muted);"><?php echo htmlspecialchars($book['author']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($item['condition'] == 'new'): ?>
                                <span class="badge badge-returned">Sách Mới</span>
                            <?php else: ?>
                                <span class="badge badge-pending">Sách Cũ (-20%)</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: var(--accent-gold); font-weight: bold;">
                            <?php echo number_format($price, 0, ',', '.'); ?>đ
                        </td>
                        <td>
                            <a href="actions/remove-from-cart.php?id=<?php echo $book['id']; ?>" class="btn btn-danger" style="padding: 5px 15px; font-size: 0.9rem;">Xóa</a>
                        </td>
                    </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; text-align: right; padding-top: 20px; border-top: 1px solid var(--border-glass);">
                <p style="font-size: 1.2rem; margin-bottom: 15px;">Tổng cộng: <strong style="color: var(--danger); font-size: 1.5rem;"><?php echo number_format($total_per_day, 0, ',', '.'); ?>đ/ngày</strong></p>
                <div class="d-flex justify-between align-center">
                    <a href="index.php" class="btn btn-outline">← Tiếp tục mượn sách</a>
                    <a href="checkout.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 12px 30px;">Tiến hành đặt sách</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
