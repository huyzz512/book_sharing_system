<?php 
include '../config/db.php'; 
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "";
if ($search) {
    $where_clause = " WHERE books.title LIKE '%$search%' OR books.author LIKE '%$search%' ";
}

$result = $conn->query("SELECT books.*, categories.name as cat_name FROM books 
                        LEFT JOIN categories ON books.category_id = categories.id 
                        $where_clause
                        ORDER BY id DESC");
include 'header.php'; 
?>
<title>Quản lý sách</title>
<main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">📚 Quản lý kho sách</h1>
        
        <!-- Form Tìm Kiếm -->
        <form action="manage-books.php" method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm tên sách, tác giả..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 250px; outline: none;">
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Tìm</button>
            <?php if($search): ?>
                <a href="manage-books.php" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none;">Xóa</a>
            <?php endif; ?>
        </form>

        <a href="add-book.php" style="background: #2ecc71; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">+ THÊM SÁCH</a>
    </div>
    <div class="admin-card">
        <table>
            <thead>
                <tr><th>Ảnh</th><th>Tên sách</th><th>Thể loại</th><th>Kho</th><th>Giá thuê</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="../uploads/books/<?php echo $row['cover_image']; ?>" style="width: 40px; height: 60px; object-fit: cover; border-radius: 4px;"></td>
                    <td><strong><?php echo $row['title']; ?></strong><br><small><?php echo $row['author']; ?></small></td>
                    <td><span style="background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;"><?php echo $row['cat_name']; ?></span></td>
                    <td><b><?php echo $row['available_new'] + $row['available_old']; ?></b>/<?php echo $row['stock_new'] + $row['stock_old']; ?></td>
                    <td><?php echo number_format($row['rental_price']); ?>đ</td>
                    <td>
                        <a href="edit-book.php?id=<?php echo $row['id']; ?>" style="color: #3498db; text-decoration: none;">Sửa</a> |
                        <a href="delete-book.php?id=<?php echo $row['id']; ?>" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body></html>