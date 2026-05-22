<?php 
include '../config/db.php';

if(!isset($_GET['id'])) { header("Location: manage-users.php"); exit(); }
$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if(!$user) { echo "Không tìm thấy thành viên!"; exit(); }

if(isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    $reputation = intval($_POST['reputation_points']);
    
    // Check if email belongs to someone else
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email' AND id != $id");
    if($check_email->num_rows > 0) {
        $error = "Email đã được sử dụng bởi người khác!";
    } else {
        $pass_sql = "";
        if(!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pass_sql = ", password='$password'";
        }
        
        $sql = "UPDATE users SET 
                username='$username', 
                email='$email', 
                role='$role', 
                reputation_points=$reputation 
                $pass_sql 
                WHERE id=$id";
                
        if($conn->query($sql)) {
            header("Location: manage-users.php?msg=updated");
            exit();
        } else {
            $error = "Lỗi: " . $conn->error;
        }
    }
}

include 'header.php'; 
?>

<title>Sửa Thành viên - Admin</title>
<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
    .btn-update { background: #3498db; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; width: 100%; transition: 0.3s; }
    .btn-update:hover { background: #2980b9; }
    .alert-error { background: #fee; border-left: 4px solid #e74c3c; padding: 15px; color: #c0392b; margin-bottom: 20px; }
</style>

<main>
    <div style="margin-bottom: 30px;">
        <a href="manage-users.php" style="text-decoration: none; color: #3498db;">← Quay lại danh sách</a>
        <h1 style="margin-top: 10px;">Chỉnh sửa: <?php echo htmlspecialchars($user['username']); ?></h1>
    </div>

    <div class="admin-card" style="max-width: 600px; margin: 0 auto;">
        <?php if(isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu (Để trống nếu không đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="***">
            </div>
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Điểm uy tín</label>
                <input type="number" name="reputation_points" class="form-control" value="<?php echo $user['reputation_points']; ?>" required>
            </div>
            
            <button type="submit" name="update" class="btn-update">CẬP NHẬT</button>
        </form>
    </div>
</main>
</body>
</html>
