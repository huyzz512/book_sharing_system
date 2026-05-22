<?php
session_start();
include '../config/db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($user['role'] == 'admin' ? "../admin/index.php" : "../index.php"));
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Book Share</title>
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
        <h2 class="auth-title text-gradient">Đăng Nhập</h2>
        <p class="auth-subtitle">Chào mừng bạn quay trở lại</p>
        
        <?php if(isset($error)) echo "<div class='badge badge-pending' style='display: block; text-align: center; margin-bottom: 20px; font-size: 0.9rem;'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required>
            </div>
            <div class="form-group mb-4">
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Đăng nhập</button>
        </form>
        
        <div style="text-align: center; margin-top: 30px; color: var(--text-muted);">
            Chưa có tài khoản? <a href="register.php" style="color: var(--accent-gold); font-weight: 500;">Đăng ký ngay</a>
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="../index.php" style="color: var(--text-muted); font-size: 0.9rem;">← Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>