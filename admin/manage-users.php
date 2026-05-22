<?php 
include '../config/db.php';
session_start();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "";
if ($search) {
    $where_clause = " WHERE username LIKE '%$search%' OR email LIKE '%$search%' ";
}

$users = $conn->query("SELECT * FROM users $where_clause ORDER BY created_at DESC");

include 'header.php'; 
?>
<title>Quản lý Thành viên</title>
<main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">👥 Danh sách Thành viên</h1>
        
        <!-- Form Tìm Kiếm -->
        <form action="manage-users.php" method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm tên, email..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 250px; outline: none;">
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Tìm</button>
            <?php if($search): ?>
                <a href="manage-users.php" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none;">Xóa</a>
            <?php endif; ?>
        </form>

        <a href="add-user.php" style="background: #2ecc71; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">+ THÊM THÀNH VIÊN</a>
    </div>
    
    <div class="admin-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Điểm uy tín</th>
                    <th>Ngày tham gia</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><b>#<?php echo $row['id']; ?></b></td>
                    <td><b><?php echo htmlspecialchars($row['username']); ?></b></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <?php if($row['role'] == 'admin'): ?>
                            <span class="status-pill status-borrowing">ADMIN</span>
                        <?php else: ?>
                            <span class="status-pill status-returned">USER</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $rep_color = '#f39c12';
                        if ($row['reputation_points'] > 80) $rep_color = '#2ecc71';
                        elseif ($row['reputation_points'] < 50) $rep_color = '#e74c3c';
                        ?>
                        <b style="color: <?php echo $rep_color; ?>;"><?php echo $row['reputation_points']; ?></b>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="user-history.php?id=<?php echo $row['id']; ?>" style="background: #3498db; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Lịch sử</a>
                            <a href="edit-user.php?id=<?php echo $row['id']; ?>" style="background: #f39c12; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Sửa</a>
                            <a href="actions/delete-user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này? Toàn bộ dữ liệu mượn sách của họ cũng sẽ bị ảnh hưởng!')" style="background: #e74c3c; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Xóa</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body></html>