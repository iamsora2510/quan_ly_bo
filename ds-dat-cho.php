<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: dang-nhap.php"); exit(); }

$user_id = $_SESSION['user_id'];
include 'includes/header.php';

// Truy vấn lấy thông tin đặt chỗ + tên giống bò + mã số tai
$sql = "SELECT d.*, b.ma_so_tai, g.ten_giong 
        FROM dat_cho_bo d
        JOIN danh_sach_bo b ON d.ma_bo = b.id
        JOIN giong_bo g ON b.ma_giong = g.id
        WHERE d.ma_khach_hang = $user_id ORDER BY d.id DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <h3 class="fw-bold text-success mb-4">Lịch sử giữ chỗ của bạn</h3>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Mã bò</th>
                        <th>Giống bò</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td class="ps-3 fw-bold">#<?= $row['ma_so_tai'] ?></td>
                        <td><?= $row['ten_giong'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['ngay_dat'])) ?></td>
                        <td>
                            <?php if($row['trang_thai'] == 'cho_xac_nhan'): ?>
                                <span class="badge bg-warning text-dark">Đang chờ gọi lại</span>
                            <?php elseif($row['trang_thai'] == 'da_lien_he'): ?>
                                <span class="badge bg-success">Đã xác nhận</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã hủy</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>