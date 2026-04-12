<?php
session_start();
include 'config/db.php';
include 'includes/header.php'; // Đảm bảo đường dẫn này đúng với file của Thanh

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; 

// Truy vấn lấy các hóa đơn (Sử dụng JOIN để đảm bảo tính đồng bộ dữ liệu)
$sql = "SELECT h.*, k.ten_khach_hang 
        FROM phieu_xuat_ban h
        JOIN khach_hang k ON h.ma_khach_hang = k.id
        WHERE k.user_id = $user_id
        ORDER BY h.ngay_ban DESC";

$result = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <h3 class="fw-bold text-success"><i class="bi bi-clock-history me-2"></i>Lịch Sử Mua Hàng & Công Nợ</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">Mã đơn</th>
                                    <th>Ngày mua</th>
                                    <th>Ngày bàn giao</th>
                                    <th>Tổng tiền</th>
                                    <th>Đã trả</th>
                                    <th class="text-danger">Còn nợ</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)):
                                        $con_no = $row['tong_tien'] - $row['so_tien_tra_truoc'];
                                    ?>
                                        <tr>
                                            <td class="ps-3 fw-bold text-primary">#<?= $row['id'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['ngay_ban'])) ?></td>
                                            <td class="fw-bold text-success">
                                                <i class="bi bi-truck me-1"></i>
                                                <?= $row['ngay_ban_giao'] ? date('d/m/Y', strtotime($row['ngay_ban_giao'])) : '<span class="text-muted fw-normal small">Chờ sắp xếp</span>' ?>
                                            </td>
                                            <td class="fw-bold"><?= number_format($row['tong_tien']) ?> đ</td>
                                            <td class="text-primary"><?= number_format($row['so_tien_tra_truoc']) ?> đ</td>
                                            <td class="text-danger fw-bold"><?= number_format($con_no) ?> đ</td>
                                            <td>
                                                <?php if ($con_no <= 0): ?>
                                                    <span class="badge bg-success rounded-pill px-3">Hoàn tất</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3">Còn nợ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="chi-tiet-don-hang.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white shadow-sm">
                                                    <i class="bi bi-eye-fill me-1"></i> Chi tiết bò
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Bạn chưa có lịch sử mua hàng nào tại trại.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>