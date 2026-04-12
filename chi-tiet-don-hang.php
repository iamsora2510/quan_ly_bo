<?php
session_start();
include 'config/db.php';
include 'includes/header.php'; // Thêm header để có Menu

if (!isset($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit(); 
}

$id_hd = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Bảo mật: Chỉ cho khách xem đơn của chính họ
$check_owner = mysqli_query($conn, "SELECT h.id FROM phieu_xuat_ban h 
                                    JOIN khach_hang k ON h.ma_khach_hang = k.id 
                                    WHERE h.id = $id_hd AND k.user_id = $user_id");

if (mysqli_num_rows($check_owner) == 0) {
    echo "<div class='container mt-5 alert alert-danger'>Bạn không có quyền xem đơn hàng này!</div>";
    include 'includes/footer.php';
    exit();
}

// Truy vấn lấy danh sách bò trong hóa đơn
$sql = "SELECT ct.*, b.ma_so_tai, g.ten_giong, b.hinh_anh 
        FROM chi_tiet_phieu_xuat ct
        JOIN danh_sach_bo b ON ct.ma_bo = b.id
        JOIN giong_bo g ON b.ma_giong = g.id
        WHERE ct.ma_phieu_xuat = $id_hd";
$result = mysqli_query($conn, $sql);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-search me-2"></i>CHI TIẾT ĐƠN HÀNG #<?= $id_hd ?>
        </h4>
        <a href="lich-su-mua-hang.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="row">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($bo = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm" style="background: #fdfdfd;">
                            <div class="card-body d-flex align-items-center p-3">
                                <img src="assets/uploads/<?= $bo['hinh_anh'] ?: 'no-image.png' ?>" 
                                     class="rounded shadow-sm me-3" 
                                     style="width: 100px; height: 80px; object-fit: cover; border: 1px solid #eee;">
                                
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">#<?= $bo['ma_so_tai'] ?></h6>
                                    <p class="small mb-1 text-muted">Giống: <span class="text-dark"><?= $bo['ten_giong'] ?></span></p>
                                    <p class="small mb-0 text-success fw-bold">
                                        <?= number_format($bo['gia_ban_con_nay']) ?> đ
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-4">Không tìm thấy dữ liệu bò cho đơn hàng này.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer bg-light text-muted small px-4 py-3">
            <i class="bi bi-info-circle me-1"></i> Đây là danh sách bò thực tế đã bàn giao theo hóa đơn điện tử.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>