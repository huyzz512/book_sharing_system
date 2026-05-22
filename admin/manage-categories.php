<?php 
include '../config/db.php'; 
if (isset($_POST['add_cat'])) {
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    if (!empty($name)) { $conn->query("INSERT INTO categories (name) VALUES ('$name')"); header("Location: manage-categories.php"); exit(); }
}
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
include 'header.php'; 
?>
<title>Quản lý danh mục</title>
<main>
    <h1 style="margin-bottom: 20px;">📁 Quản lý thể loại</h1>
    <div class="admin-card" style="margin-bottom: 20px;">
        <form method="POST" style="display: flex; gap: 10px;">
            <input type="text" name="cat_name" placeholder="Tên thể loại mới..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
            <button type="submit" name="add_cat" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">+ Thêm</button>
        </form>
    </div>
    <div class="admin-card">
        <table>
            <thead><tr><th>ID</th><th>Tên thể loại</th><th>Thao tác</th></tr></thead>
            <tbody>
                <?php while($row = $categories->fetch_assoc()): ?>
                <tr><td>#<?php echo $row['id']; ?></td><td><strong><?php echo $row['name']; ?></strong></td>
                    <td><a href="?delete=<?php echo $row['id']; ?>" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Xóa?')">Xóa</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body></html>