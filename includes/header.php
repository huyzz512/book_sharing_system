<?php
// Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand text-gradient">BOOK SHARE</a>
            
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Trang chủ</a></li>
                
                <?php 
                if (isset($_SESSION['user_id'])): 
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                ?>
                    <li>
                        <a href="cart.php" class="nav-link" style="position: relative;">
                            Giỏ hàng 
                            <?php if($cart_count > 0): ?>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="profile.php" class="nav-link">Trang cá nhân</a></li>
                    
                    <?php 
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): 
                    ?>
                        <li><a href="admin/index.php" class="btn btn-outline" style="border-color: var(--accent-gold); color: var(--accent-gold);">Quản trị</a></li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="auth/logout.php" class="btn btn-danger">
                            Đăng xuất (<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>)
                        </a>
                    </li>

                <?php else: ?>
                    <li>
                        <a href="auth/login.php" class="btn btn-primary">
                            Đăng nhập
                        </a>
                    </li>
                    <li>
                        <a href="auth/register.php" class="btn btn-outline">
                            Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main>