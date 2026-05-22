<?php
include '../config/db.php';
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);

    $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        $error = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        $sql = "INSERT INTO users (username, email, password, role, reputation_points) VALUES ('$username', '$email', '$password', 'user', 100)";
        if ($conn->query($sql)) header("Location: login.php?msg=success");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Book Share</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Fix background for standalone auth pages */
        body { 
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="auth-card glass-card">
        <h2 class="auth-title text-gradient">Đăng Ký</h2>
        <p class="auth-subtitle">Trở thành thành viên của cộng đồng chia sẻ tri thức</p>
        
        <?php if(isset($error)) echo "<div class='badge badge-pending' style='display: block; text-align: center; margin-bottom: 20px; font-size: 0.9rem;'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group mb-4">
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">Tham gia ngay</button>
        </form>
        
        <div style="text-align: center; margin-top: 30px; color: var(--text-muted);">
            Đã có tài khoản? <a href="login.php" style="color: var(--accent-gold); font-weight: 500;">Đăng nhập</a>
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="../index.php" style="color: var(--text-muted); font-size: 0.9rem;">← Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>