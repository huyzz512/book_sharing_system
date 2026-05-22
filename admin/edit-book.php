<?php 
include '../config/db.php';

if(!isset($_GET['id'])) { header("Location: manage-books.php"); exit(); }

$id = intval($_GET['id']);
$book = $conn->query("SELECT * FROM books WHERE id=$id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");

if(!$book) { echo "Không tìm thấy sách!"; exit(); }

if(isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $cat_id = intval($_POST['category_id']);
    $price = floatval($_POST['rental_price']);
    $book_value = floatval($_POST['book_value']);
    $stock_new = intval($_POST['stock_new']);
    $stock_old = intval($_POST['stock_old']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    $img_sql = "";
    if(!empty($_FILES['cover_image']['name'])) {
        $img_name = time() . "_" . $_FILES['cover_image']['name'];
        move_uploaded_file($_FILES['cover_image']['tmp_name'], "../uploads/books/" . $img_name);
        $img_sql = ", cover_image='$img_name'";
    }

    $diff_new = $stock_new - $book['stock_new'];
    $diff_old = $stock_old - $book['stock_old'];

    $sql = "UPDATE books SET 
            title='$title', 
            author='$author',
            category_id='$cat_id',
            stock_new='$stock_new', 
            available_new=available_new + $diff_new,
            stock_old='$stock_old', 
            available_old=available_old + $diff_old,
            rental_price='$price', 
            book_value='$book_value',
            description='$desc' 
            $img_sql 
            WHERE id=$id";
            
    if($conn->query($sql)) {
        header("Location: manage-books.php?msg=updated");
        exit();
    }
}

include 'header.php'; 
?>

<title>Chỉnh sửa sách - Admin</title>
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
    .btn-update { background: #3498db; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: 0.3s; }
    .btn-update:hover { background: #2980b9; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .current-img { border-radius: 8px; border: 1px solid #ddd; margin-top: 10px; display: block; }
</style>

<main>
    <div style="margin-bottom: 30px;">
        <a href="manage-books.php" style="text-decoration: none; color: #3498db;">← Quay lại danh sách</a>
        <h1 style="margin-top: 10px;">Chỉnh sửa: <?php echo htmlspecialchars($book['title']); ?></h1>
    </div>

    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên sách</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Tác giả</label>
                <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Thể loại</label>
                    <select name="category_id" class="form-control">
                        <?php while($c = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $book['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá thuê</label>
                    <input type="number" name="rental_price" class="form-control" value="<?php echo $book['rental_price']; ?>">
                </div>
                <div class="form-group">
                    <label>Giá trị gốc của sách (VNĐ)</label>
                    <input type="number" name="book_value" class="form-control" value="<?php echo $book['book_value']; ?>" required>
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Tổng Sách Mới</label>
                    <input type="number" name="stock_new" class="form-control" value="<?php echo $book['stock_new']; ?>">
                </div>
                <div class="form-group">
                    <label>Tổng Sách Cũ</label>
                    <input type="number" name="stock_old" class="form-control" value="<?php echo $book['stock_old']; ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Ảnh bìa hiện tại</label>
                <img src="../uploads/books/<?php echo $book['cover_image']; ?>" width="120" class="current-img">
                <p style="font-size: 0.85rem; color: #7f8c8d; margin-top: 10px;">Chọn ảnh mới nếu muốn thay đổi:</p>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            
            <button type="submit" name="update" class="btn-update">CẬP NHẬT THÔNG TIN</button>
        </form>
    </div>
</main>
</body>
</html>