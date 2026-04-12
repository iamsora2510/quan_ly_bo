<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin chi tiết con bò
$sql = "SELECT b.*, g.ten_giong, c.ten_chuong FROM danh_sach_bo b 
        JOIN giong_bo g ON b.ma_giong = g.id 
        JOIN chuong_nuoi c ON b.ma_chuong = c.id
        WHERE b.id = $id";
$res = mysqli_query($conn, $sql);
$bo = mysqli_fetch_assoc($res);

if (!$bo) {
    echo "<script>window.location='index.php';</script>";
    exit();
}
?>


<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Danh mục</a></li>
            <li class="breadcrumb-item active">Chi tiết bò #<?= $bo['ma_so_tai'] ?></li>
        </ol>
    </nav>
    <button onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm mb-4">
        <i class="bi bi-arrow-left me-2"></i>Quay lại trang trước
    </button>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="assets/uploads/<?= $bo['hinh_anh'] ?>" class="img-fluid w-100" alt="Hình ảnh bò">
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="fw-bold text-success mb-3">Bò <?= $bo['ten_giong'] ?></h1>
            <h5 class="text-muted mb-4">Mã số tai: #<?= $bo['ma_so_tai'] ?></h5>

            <div class="card border-0 bg-light p-4 rounded-4 mb-4">
                <div class="row g-3">
                    <div class="col-6">
                        <p class="text-muted mb-1 small">Giống bò:</p>
                        <p class="fw-bold"><?= $bo['ten_giong'] ?></p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1 small">Chuồng nuôi:</p>
                        <p class="fw-bold"><?= $bo['ten_chuong'] ?></p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1 small">Trọng lượng hiện tại:</p>
                        <p class="fw-bold text-danger fs-4"><?= $bo['can_nang_hien_tai'] ?> kg</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1 small">Tình trạng:</p>
                        <span class="badge bg-success">Đang khỏe mạnh</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Lịch sử tiêm chủng</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered border-light shadow-sm">
                        <thead class="table-primary text-white">
                            <tr>
                                <th>Ngày tiêm</th>
                                <th>Loại Vaccine</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_tc = "SELECT * FROM nhat_ky_cham_soc WHERE ma_bo = $id ORDER BY ngay_thuc_hien DESC";
                            $res_tc = mysqli_query($conn, $sql_tc);
                            if (mysqli_num_rows($res_tc) > 0):
                                while ($tc = mysqli_fetch_assoc($res_tc)): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($tc['ngay_thuc_hien'])) ?></td>
                                        <td><?= $tc['ten_vaccine'] ?></td>
                                        <td class="small"><?= $tc['ghi_chu'] ?: 'Không' ?></td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted small py-3">Chưa có dữ liệu tiêm chủng.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="dat-cho.php?id=<?= $bo['id'] ?>" class="btn btn-warning btn-lg fw-bold rounded-pill py-3 shadow">
                    <i class="bi bi-calendar-check me-2"></i>ĐẶT GIỮ CHỖ NGAY
                </a>
                <p class="text-center text-muted small mt-2">Đại lý sẽ liên hệ báo giá trong vòng 24h.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>