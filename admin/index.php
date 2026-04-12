<?php 
include '../config/db.php';
include './includes/header.php'; 
include './includes/sidebar.php'; 

// --- LẤY DỮ LIỆU THẬT ---
// 1. Tổng số bò đang nuôi
$tong_bo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as sl FROM danh_sach_bo WHERE trang_thai = 'dang_nuoi'"))['sl'];

// 2. Tổng doanh thu (từ tất cả các đơn bán)
$tong_dt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(tong_tien) as dt FROM phieu_xuat_ban"))['dt'] ?? 0;

// 3. Tổng lợi nhuận thuần (Công thức: Bán - Vốn - Nuôi)
$sql_ln = "SELECT 
    (SUM(ctx.gia_ban_con_nay) - (SUM(b.gia_mua_vao) + (SELECT IFNULL(SUM(so_tien),0) FROM chi_phi_cham_soc WHERE ma_bo IN (SELECT ma_bo FROM chi_tiet_phieu_xuat)))) as ln
    FROM chi_tiet_phieu_xuat ctx
    JOIN danh_sach_bo b ON ctx.ma_bo = b.id";
$tong_ln = mysqli_fetch_assoc(mysqli_query($conn, $sql_ln))['ln'] ?? 0;

// 4. Số lượng khách hàng
$tong_kh = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as sl FROM khach_hang"))['sl'];
?>

<main class="app-main">
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark">Bảng Điều Khiển Hệ Thống</h3>
                </div>
                <div class="col-sm-6 text-end">
                    <span class="text-muted small">Chào mừng trở lại, <strong>Admin</strong>!</span>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary shadow-sm rounded-3 p-3 mb-4 position-relative overflow-hidden">
                        <div class="inner">
                            <h3 class="fw-bold"><?= $tong_bo ?></h3>
                            <p class="mb-0">Đàn bò hiện có</p>
                        </div>
                        <div class="icon position-absolute end-0 top-0 m-2 opacity-25">
                            <i class="bi bi-cow fs-1"></i>
                        </div>
                        <a href="danh-sach-bo.php" class="small-box-footer text-white-50 text-decoration-none small">
                            Xem chi tiết <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success shadow-sm rounded-3 p-3 mb-4 position-relative overflow-hidden">
                        <div class="inner">
                            <h3 class="fw-bold"><?= number_format($tong_dt/1000000, 1) ?>M</h3>
                            <p class="mb-0">Doanh thu (VNĐ)</p>
                        </div>
                        <div class="icon position-absolute end-0 top-0 m-2 opacity-25">
                            <i class="bi bi-currency-dollar fs-1"></i>
                        </div>
                        <a href="bao-cao-thong-ke.php" class="small-box-footer text-white-50 text-decoration-none small">
                            Xem báo cáo <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-info shadow-sm rounded-3 p-3 mb-4 position-relative overflow-hidden">
                        <div class="inner">
                            <h3 class="fw-bold text-white"><?= number_format($tong_ln/1000000, 1) ?>M</h3>
                            <p class="mb-0 text-white">Lợi nhuận dự tính</p>
                        </div>
                        <div class="icon position-absolute end-0 top-0 m-2 opacity-25">
                            <i class="bi bi-graph-up-arrow fs-1 text-white"></i>
                        </div>
                        <a href="bao-cao-thong-ke.php" class="small-box-footer text-white-50 text-decoration-none small">
                            Xem chi tiết <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning shadow-sm rounded-3 p-3 mb-4 position-relative overflow-hidden">
                        <div class="inner">
                            <h3 class="fw-bold"><?= $tong_kh ?></h3>
                            <p class="mb-0">Đối tác / Khách hàng</p>
                        </div>
                        <div class="icon position-absolute end-0 top-0 m-2 opacity-25">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                        <a href="quan-ly-doi-tac.php" class="small-box-footer text-dark-50 text-decoration-none small">
                            Quản lý đối tác <i class="bi bi-arrow-right-circle"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold py-3">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Thao tác nhanh
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-4">
                                    <a href="them-bo.php" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-plus-circle d-block mb-1 fs-4"></i> Nhập bò mới
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="danh-sach-bo.php" class="btn btn-outline-success w-100 py-3">
                                        <i class="bi bi-cart-plus d-block mb-1 fs-4"></i> Xuất bán bò
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="cong-no.php" class="btn btn-outline-danger w-100 py-3">
                                        <i class="bi bi-cash-coin d-block mb-1 fs-4"></i> Thu tiền nợ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold py-3 text-info">
                            <i class="bi bi-info-circle me-1"></i> Thông tin trại
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-2">Đại lý bò: <strong>Thanh STU</strong></p>
                            <p class="small text-muted mb-2">Trạng thái: <span class="badge bg-success">Hoạt động</span></p>
                            <p class="small text-muted mb-0">Phiên bản: <strong>v2.1 (Final)</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>