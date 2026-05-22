<?php 
include 'config/db.php';
include 'includes/functions.php';
include 'includes/header.php';

// Lấy ID sách từ URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Truy vấn thông tin chi tiết sách và tên danh mục
$res = $conn->query("SELECT books.*, categories.name as cat_name FROM books 
                     JOIN categories ON books.category_id = categories.id 
                     WHERE books.id = $book_id");
$book = $res->fetch_assoc();

// Nếu không có sách thì báo lỗi
if (!$book) { 
    echo "<div class='container'><p>Sách không tồn tại hoặc đã bị xóa.</p></div>"; 
    include 'includes/footer.php';
    exit; 
}
?>

<div class="container">
    <div class="detail-wrapper">
        <div class="detail-img-box">
            <?php if(!empty($book['cover_image']) && file_exists("uploads/books/".$book['cover_image'])): ?>
                <img src="uploads/books/<?php echo $book['cover_image']; ?>" alt="Bìa sách">
            <?php else: ?>
                <img src="assets/images/default-book.png" alt="No Image">
            <?php endif; ?>
        </div>
        
        <div class="detail-content">
            <h1 class="text-gradient"><?php echo htmlspecialchars($book['title']); ?></h1>
            <div class="detail-meta">
                <span class="text-muted">Tác giả: <b style="color: var(--text-main);"><?php echo htmlspecialchars($book['author']); ?></b></span>
                <span class="text-muted">Thể loại: <span class="badge badge-borrowing"><?php echo $book['cat_name']; ?></span></span>
            </div>
            
            <p class="detail-price">
                <?php echo number_format($book['rental_price'], 0, ',', '.'); ?>đ <span style="font-size: 1.2rem; font-weight: 500; color: var(--text-muted);">/ngày</span>
            </p>
            
            <h4 style="margin-bottom: 10px; color: var(--accent-gold);">Mô tả sách:</h4>
            <p class="detail-desc"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'exists'): ?>
                <div class="badge badge-pending mb-3" style="display:inline-block; font-size:1rem; padding: 10px;">Sách này đã có trong giỏ hàng!</div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <?php if ($book['available_stock'] > 0): ?>
                    <form action="actions/add-to-cart.php" method="POST" class="d-flex align-center gap-2">
                        <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                        <select name="condition" class="form-control" style="width: auto; height: 50px;">
                            <option value="new">Sách mới (Giá gốc)</option>
                            <option value="old">Sách cũ (Giảm 20%)</option>
                        </select>
                        <button type="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem; height: 50px;">
                            🛒 Thêm vào giỏ
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-outline" disabled style="padding: 15px 40px; font-size: 1.1rem; cursor: not-allowed; opacity: 0.6;">
                        ❌ Sách đã được mượn hết
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <section class="reviews-section glass-card" style="margin-bottom: 40px;">
        <h3 class="section-title"><span class="text-gradient">Đánh giá từ độc giả</span></h3>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="review-form">
                <h4 style="margin-bottom: 20px;">Viết đánh giá của bạn</h4>
                <form action="actions/process-review.php" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Chọn số sao:</label>
                        <select name="rating" required class="form-control" style="width: 250px;">
                            <option value="5">⭐⭐⭐⭐⭐ (Rất tốt)</option>
                            <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                            <option value="3">⭐⭐⭐ (Bình thường)</option>
                            <option value="2">⭐⭐ (Kém)</option>
                            <option value="1">⭐ (Rất tệ)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bình luận:</label>
                        <textarea name="comment" class="form-control" placeholder="Bạn thấy cuốn sách này thế nào? Hãy chia sẻ cho mọi người cùng biết nhé..." required style="height: 120px; resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Gửi đánh giá ngay</button>
                </form>
            </div>
        <?php else: ?>
            <div class="glass-card" style="padding: 20px; margin-bottom: 30px; border-left: 4px solid var(--accent-gold);">
                Vui lòng <a href="auth/login.php" style="color: var(--accent-gold); font-weight: bold; text-decoration: underline;">đăng nhập</a> để để lại đánh giá cho cuốn sách này.
            </div>
        <?php endif; ?>

        <div class="review-list">
            <?php 
            $reviews = $conn->query("SELECT reviews.*, users.username FROM reviews 
                                     JOIN users ON reviews.user_id = users.id 
                                     WHERE book_id = $book_id ORDER BY created_at DESC");
            
            if ($reviews && $reviews->num_rows > 0): 
                while($rev = $reviews->fetch_assoc()):
            ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="review-author">
                            <span style="font-size: 1.5rem;">👤</span>
                            <?php echo htmlspecialchars($rev['username']); ?>
                        </div>
                        <div class="review-stars">
                            <?php for($i=1; $i<=$rev['rating']; $i++) echo "⭐"; ?>
                        </div>
                    </div>
                    <p class="review-text">
                        <?php echo htmlspecialchars($rev['comment']); ?>
                    </p>
                    <div class="review-date">
                        Đã đánh giá vào: <?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div style="text-align: center; padding: 40px;">
                    <p style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;">✍️</p>
                    <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên đánh giá cuốn sách này!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>