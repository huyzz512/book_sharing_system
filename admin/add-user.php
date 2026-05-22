<?php 
include '../config/db.php';

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $reputation = intval($_POST['reputation_points']);
    
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check_email->num_rows > 0) {
        $error = "Email đã tồn tại!";
    } else {
        $sql = "INSERT INTO users (username, email, password, role, reputation_points) 
                VALUES ('$username', '$email', '$password', '$role', $reputation)";
        if($conn->query($sql)) {
            header("Location: manage-users.php?msg=added");
            exit();
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}

include 'header.php'; 
?>

<title>Thêm Thành viên - Admin</title>
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
    .btn-submit { background: #2ecc71; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: 0.3s; }
    .btn-submit:hover { background: #27ae60; }
    .alert-error { background: #fee; border-left: 4px solid #e74c3c; padding: 15px; color: #c0392b; margin-bottom: 20px; }
</style>

<main>
    <div style="margin-bottom: 30px;">
        <a href="manage-users.php" style="text-decoration: none; color: #3498db;">← Quay lại danh sách</a>
        <h1 style="margin-top: 10px;">Thêm Thành Viên Mới</h1>
    </div>

    <div class="admin-card" style="max-width: 600px; margin: 0 auto;">
        <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Điểm uy tín khởi tạo</label>
                <input type="number" name="reputation_points" class="form-control" value="70" required>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">THÊM THÀNH VIÊN</button>
        </form>
    </div>
</main>
</body>
</html>
