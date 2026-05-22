<?php 
include '../config/db.php'; 

// Lấy tham số bộ lọc (nếu có)
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Truy vấn lấy danh sách các đơn hàng đã hoàn thành trong tháng
$sql = "SELECT * FROM orders 
        WHERE status = 'returned' 
        AND MONTH(order_date) = $month 
        AND YEAR(order_date) = $year
        ORDER BY order_date DESC";
$revenue_orders = $conn->query($sql);

// Tính tổng doanh thu tháng
$total_revenue = 0;
$total_orders = 0;

$order_data = [];
if ($revenue_orders && $revenue_orders->num_rows > 0) {
    while($row = $revenue_orders->fetch_assoc()) {
        $total_revenue += $row['total_price'];
        $total_orders++;
        $order_data[] = $row;
    }
}

include 'header.php'; 
?>
<title>Quản lý Doanh thu</title>
<main>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="display: flex; align-items: center; gap: 10px;">💰 Quản Lý Doanh Thu</h1>
        
        <!-- Form Lọc -->
        <form action="revenue.php" method="GET" style="display: flex; gap: 10px; align-items: center; background: white; padding: 10px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <label style="font-weight: bold; color: #2c3e50;">Tháng:</label>
            <select name="month" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ddd;">
                <?php for($i=1; $i<=12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if($i == $month) echo 'selected'; ?>>Tháng <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            
            <label style="font-weight: bold; color: #2c3e50;">Năm:</label>
            <select name="year" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ddd;">
                <?php for($i=2024; $i<=date('Y'); $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if($i == $year) echo 'selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            
            <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Lọc dữ liệu</button>
        </form>
    </div>

    <!-- Tổng quan Doanh Thu -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div class="admin-card" style="border-left: 5px solid #2ecc71; background: linear-gradient(135deg, rgba(46, 204, 113, 0.1) 0%, rgba(255,255,255,1) 100%);">
            <h3 style="color: #2ecc71; margin-bottom: 10px;">Tổng Doanh Thu Tháng <?php echo $month; ?>/<?php echo $year; ?></h3>
            <p style="font-size: 2.5rem; margin: 0; font-weight: bold; color: #27ae60;">
                <?php echo number_format($total_revenue, 0, ',', '.'); ?>đ
            </p>
        </div>
        <div class="admin-card" style="border-left: 5px solid #3498db; background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(255,255,255,1) 100%);">
            <h3 style="color: #3498db; margin-bottom: 10px;">Tổng Đơn Hàng Hoàn Thành</h3>
            <p style="font-size: 2.5rem; margin: 0; font-weight: bold; color: #2980b9;">
                <?php echo $total_orders; ?> <span style="font-size: 1rem; font-weight: normal; color: #7f8c8d;">đơn hàng</span>
            </p>
        </div>
    </div>

    <!-- Chi tiết Đơn hàng -->
    <div class="admin-card">
        <h2 style="margin-bottom: 20px; color: #2c3e50;">Danh sách Đơn hàng tạo ra Doanh thu</h2>
        <?php if ($total_orders > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Ngày Đặt</th>
                        <th>Khách hàng</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($order_data as $row): ?>
                    <tr>
                        <td><b style="color: #e74c3c;">#<?php echo $row['id']; ?></b></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                        <td>
                            <b><?php echo htmlspecialchars($row['customer_name']); ?></b><br>
                            <small style="color: #7f8c8d;"><?php echo htmlspecialchars($row['customer_phone']); ?></small>
                        </td>
                        <td style="color: #27ae60; font-weight: bold; font-size: 1.1rem;">
                            + <?php echo number_format($row['total_price'], 0, ',', '.'); ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <p style="font-size: 3rem; margin-bottom: 10px; color: #bdc3c7;">📊</p>
                <p style="font-size: 1.2rem; color: #7f8c8d;">Không có dữ liệu doanh thu trong tháng này.</p>
            </div>
        <?php endif; ?>
    </div>
</main>
</body></html>
