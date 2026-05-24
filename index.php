<?php 
include 'config/db.php'; 
include 'includes/functions.php'; // Chứa các hàm hỗ trợ
include 'includes/header.php';    // CHÈN HEADER VÀO ĐÂY

// Xử lý Tìm kiếm & Lọc (Giữ nguyên logic của bạn)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT books.*, categories.name as cat_name FROM books 
        JOIN categories ON books.category_id = categories.id 
        WHERE books.title LIKE '%$search%'";

if ($cat_id) {
    $sql .= " AND category_id = $cat_id";
}

$result = $conn->query($sql);
$categories = $conn->query("SELECT * FROM categories");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title text-gradient">Khám Phá Thế Giới Tri Thức</h1>
        <p class="hero-subtitle">Mượn và chia sẻ những cuốn sách tuyệt vời cùng cộng đồng. Hàng ngàn cuốn sách đang chờ đón bạn.</p>
        
        <form action="index.php" method="GET" class="search-bar-wrapper glass-card">
            <input type="text" name="search" class="form-control" placeholder="Bạn muốn tìm sách gì hôm nay?" value="<?php echo htmlspecialchars($search); ?>">
            <select name="category" class="form-control">
                <option value="">Tất cả thể loại</option>
                <?php while($c = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>" <?php if($cat_id == $c['id']) echo 'selected'; ?>>
                        <?php echo $c['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        </form>
    </div>
</section>

<div class="container">
    <h2 class="section-title"><span class="text-gradient">Sách Mới Cập Nhật</span></h2>
    <div class="book-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-card glass-card">
                    <div class="book-img-wrapper">
                        <div class="book-category"><?php echo $book['cat_name']; ?></div>
                        <?php if(($book['available_new'] + $book['available_old']) <= 0): ?>
                            <div class="out-of-stock-overlay">
                                <div class="out-of-stock-text">Sách đã được mượn hết</div>
                            </div>
                        <?php endif; ?>
                        <?php if(!empty($book['cover_image']) && file_exists("uploads/books/".$book['cover_image'])): ?>
                            <img src="uploads/books/<?php echo $book['cover_image']; ?>" alt="Bìa sách">
                        <?php else: ?>
                            <img src="assets/images/default-book.png" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <h3 class="book-title"><?php echo $book['title']; ?></h3>
                    <p class="book-price"><?php echo number_format($book['rental_price']); ?>đ <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: normal;">/ ngày</span></p>
                    <p class="book-stock">Số lượng: <strong><?php echo ($book['available_new'] + $book['available_old']); ?></strong> / <?php echo ($book['stock_new'] + $book['stock_old']); ?></p>
                    
                    <?php if(($book['available_new'] + $book['available_old']) > 0): ?>
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                    <?php else: ?>
                        <button class="btn btn-outline" disabled style="cursor: not-allowed; opacity: 0.6;">Hết sách</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="glass-card" style="grid-column: 1 / -1; padding: 40px; text-align: center;">
                <p style="font-size: 1.2rem; color: var(--text-muted);">Không tìm thấy cuốn sách nào phù hợp với tìm kiếm của bạn.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>