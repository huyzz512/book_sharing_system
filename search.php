<?php 
include 'config/db.php';
include 'includes/functions.php';
include 'includes/header.php';

// Lấy dữ liệu từ URL
$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';

// Xây dựng câu lệnh SQL linh hoạt
$sql = "SELECT books.*, categories.name as cat_name FROM books 
        JOIN categories ON books.category_id = categories.id 
        WHERE (books.title LIKE '%$keyword%' OR books.author LIKE '%$keyword%')";

if ($category_id != '') {
    $sql .= " AND books.category_id = $category_id";
}

$result = $conn->query($sql);
?>

<div class="container">
    <h2 class="section-title"><span class="text-gradient">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($keyword); ?>"</span></h2>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="book-grid">
            <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-card glass-card">
                    <div class="book-img-wrapper">
                        <div class="book-category"><?php echo $book['cat_name']; ?></div>
                        <?php if($book['available_stock'] <= 0): ?>
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
                    <p class="book-stock">Số lượng: <strong><?php echo $book['available_stock']; ?></strong> / <?php echo $book['total_stock']; ?></p>
                    
                    <?php if($book['available_stock'] > 0): ?>
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                    <?php else: ?>
                        <button class="btn btn-outline" disabled style="cursor: not-allowed; opacity: 0.6;">Hết sách</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="glass-card" style="padding: 50px; text-align: center; margin-top: 30px;">
            <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 20px;">Rất tiếc, không tìm thấy cuốn sách nào phù hợp với yêu cầu của bạn.</p>
            <a href="index.php" class="btn btn-outline">Quay lại trang chủ</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>