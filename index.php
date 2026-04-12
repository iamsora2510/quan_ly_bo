<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Lấy 3 con bò mới nhất để làm "Bò nổi bật"
$sql_hot = "SELECT b.*, g.ten_giong FROM danh_sach_bo b 
            JOIN giong_bo g ON b.ma_giong = g.id 
            WHERE b.trang_thai = 'dang_nuoi' ORDER BY b.id DESC LIMIT 3";
$res_hot = mysqli_query($conn, $sql_hot);
?>

<div class="p-5 mb-4 bg-light rounded-3 shadow-sm" style="background: url('assets/img/hero-beef.jpg') no-repeat center; background-size: cover; min-height: 400px;">
    <div class="container-fluid py-5 text-white" style="background: rgba(0,0,0,0.5); border-radius: 15px;">
        <h1 class="display-5 fw-bold">THANH STU BEEF</h1>
        <p class="col-md-8 fs-4">Hệ thống cung cấp bò giống chất lượng cao, quy trình chăn nuôi hiện đại và minh bạch công nợ cho hộ dân.</p>
        <a href="danh-muc.php" class="btn btn-success btn-lg px-5 rounded-pill shadow">Xem Danh Mục Bò Ngay</a>
    </div>
</div>

<div class="container py-5 text-center">
    <div class="row g-4">
        <div class="col-md-4">
            <i class="bi bi-shield-check fs-1 text-success"></i>
            <h4 class="mt-3 fw-bold">Chất lượng hàng đầu</h4>
            <p class="text-muted">Bò được tuyển chọn kỹ lưỡng, tiêm ngừa đầy đủ trước khi bàn giao.</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-person-check fs-1 text-success"></i>
            <h4 class="mt-3 fw-bold">Hỗ trợ tận tình</h4>
            <p class="text-muted">Đội ngũ kỹ thuật hỗ trợ hộ dân chăm sóc và theo dõi sức khỏe bò.</p>
        </div>
        <div class="col-md-4">
            <i class="bi bi-wallet2 fs-1 text-success"></i>
            <h4 class="mt-3 fw-bold">Công nợ linh hoạt</h4>
            <p class="text-muted">Minh bạch hóa đơn, hỗ trợ trả trước và thanh toán dần cho hộ dân.</p>
        </div>
    </div>
</div>

<div class="bg-white py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-fire text-danger me-2"></i>BÒ MỚI NHẬP TRẠI</h3>
            <a href="danh-muc.php" class="text-success text-decoration-none">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php while($row = mysqli_fetch_assoc($res_hot)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="assets/uploads/<?= $row['hinh_anh'] ?: 'no-image.png' ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-4 text-center">
                        <span class="badge bg-light text-success border border-success mb-2"><?= $row['ten_giong'] ?></span>
                        <h5 class="fw-bold">Mã số: #<?= $row['ma_so_tai'] ?></h5>
                        <p class="text-danger fw-bold fs-5">Giá: Liên hệ</p>
                        <a href="chi-tiet-bo.php?id=<?= $row['id'] ?>" class="btn btn-outline-success rounded-pill px-4">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>