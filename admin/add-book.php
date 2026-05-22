<?php 
include '../config/db.php';
$categories = $conn->query("SELECT * FROM categories");

if(isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $cat_id = intval($_POST['category_id']);
    $price = floatval($_POST['rental_price']);
    $deposit = floatval($_POST['deposit_price']);
    $stock = intval($_POST['total_stock']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $img_name = $_FILES['cover_image']['name'];
    $target = "../uploads/books/" . time() . "_" . $img_name; // Thêm time() để tránh trùng tên file

    if(move_uploaded_file($_FILES['cover_image']['tmp_name'], $target)) {
        $final_name = time() . "_" . $img_name;
        $sql = "INSERT INTO books (title, author, category_id, rental_price, deposit_price, total_stock, available_stock, description, cover_image) 
                VALUES ('$title', '$author', '$cat_id', '$price', '$deposit', '$stock', '$stock', '$desc', '$final_name')";
        
        if($conn->query($sql)) {
            header("Location: manage-books.php?msg=added");
            exit();
        }
    }
}

include 'header.php'; 
?>

<title>Thêm sách mới - Admin</title>
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
    .btn-submit { background: #2ecc71; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: 0.3s; }
    .btn-submit:hover { background: #27ae60; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
</style>

<main>
    <div style="margin-bottom: 30px;">
        <a href="manage-books.php" style="text-decoration: none; color: #3498db;">← Quay lại danh sách</a>
        <h1 style="margin-top: 10px;">Thêm sách mới vào kho</h1>
    </div>

    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên sách</label>
                <input type="text" name="title" class="form-control" placeholder="Nhập tên sách..." required>
            </div>

            <div class="form-group">
                <label>Tác giả</label>
                <input type="text" name="author" class="form-control" placeholder="Tên tác giả..." required>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>Thể loại</label>
                    <select name="category_id" class="form-control">
                        <?php while($c = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá thuê (VNĐ)</label>
                    <input type="number" name="rental_price" class="form-control" placeholder="VD: 5000">
                </div>
                <div class="form-group">
                    <label>Tiền cọc (VNĐ)</label>
                    <input type="number" name="deposit_price" class="form-control" placeholder="VD: 50000">
                </div>
            </div>

            <div class="form-group">
                <label>Số lượng nhập kho</label>
                <input type="number" name="total_stock" class="form-control" value="1" min="1">
            </div>

            <div class="form-group">
                <label>Mô tả nội dung</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Tóm tắt nội dung sách..."></textarea>
            </div>

            <div class="form-group">
                <label>Ảnh bìa sách</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*" required>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">LƯU VÀO KHO SÁCH</button>
        </form>
    </div>
</main>
</body>
</html>