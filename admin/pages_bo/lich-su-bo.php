<?php 
include '../../config/db.php'; 
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Bảo mật: ép kiểu ID
$id_bo = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_bo > 0) {
    // 1. Lấy thông tin cơ bản của con bò
    $sql_bo = "SELECT b.*, g.ten_giong, c.ten_chuong, p.ngay_mua, h.ten_ho_dan
               FROM danh_sach_bo b
               JOIN giong_bo g ON b.ma_giong = g.id
               JOIN chuong_nuoi c ON b.ma_chuong = c.id
               LEFT JOIN phieu_thu_mua p ON b.ma_phieu_nhap = p.id
               LEFT JOIN ho_dan h ON p.ma_ho_dan = h.id
               WHERE b.id = $id_bo";
    $res_bo = mysqli_query($conn, $sql_bo);
    $bo = mysqli_fetch_assoc($res_bo);

    // 2. Lấy lịch sử chăm sóc & cân nặng
    $sql_history = "SELECT ngay_thuc_hien as ngay, loai_cham_soc as loai, chi_tiet, can_nang 
                    FROM nhat_ky_cham_soc 
                    WHERE ma_bo = $id_bo 
                    ORDER BY ngay_thuc_hien DESC";
    $res_history = mysqli_query($conn, $sql_history);

    // 3. Tính tổng chi phí chăm sóc tích lũy
    $sql_total_cost = "SELECT SUM(so_tien) as tong_chi_phi FROM chi_phi_cham_soc WHERE ma_bo = $id_bo";
    $res_cost = mysqli_query($conn, $sql_total_cost);
    $cost_data = mysqli_fetch_assoc($res_cost);
    $tong_chiphi = $cost_data['tong_chi_phi'] ?? 0;
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0">Hồ Sơ Chi Tiết: <span class="text-primary"><?= $bo['ma_so_tai'] ?? 'N/A' ?></span></h3>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="danh-sach-bo.php" class="btn btn-secondary shadow-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <?php if (!$bo): ?>
                <div class="alert alert-danger">Không tìm thấy thông tin con bò này.</div>
            <?php else: ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <img src="../../assets/uploads/<?= $bo['hinh_anh'] ?: 'no-image.png' ?>" class="card-img-top" alt="Ảnh bò" style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><?= $bo['ten_giong'] ?></h5>
                            <p class="text-muted mb-3"><i class="bi bi-house-door me-1"></i> <?= $bo['ten_chuong'] ?></p>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ngày nhập:</span>
                                <span class="fw-bold"><?= date('d/m/Y', strtotime($bo['ngay_mua'])) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Cân nặng nhập:</span>
                                <span class="fw-bold"><?= $bo['can_nang_nhap'] ?> kg</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Cân nặng hiện tại:</span>
                                <span class="text-danger fw-bold"><?= $bo['can_nang_hien_tai'] ?> kg</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giá mua vào:</span>
                                <span class="fw-bold"><?= number_format($bo['gia_mua_vao']) ?> đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí chăm sóc:</span>
                                <span class="fw-bold"><?= number_format($tong_chiphi) ?> đ</span>
                            </div>
                            
                            <div class="alert alert-success mt-3 py-2 border-0">
                                <small class="d-block mb-1">TỔNG GIÁ VỐN TÍCH LŨY:</small>
                                <h4 class="fw-bold mb-0 text-center"><?= number_format($bo['gia_mua_vao'] + $tong_chiphi) ?> đ</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Nhật ký chăm sóc & Tăng trưởng
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Ngày</th>
                                            <th>Hoạt động</th>
                                            <th>Chi tiết xử lý</th>
                                            <th class="text-center">Cân nặng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(mysqli_num_rows($res_history) > 0): ?>
                                            <?php while($h = mysqli_fetch_assoc($res_history)): ?>
                                            <tr>
                                                <td class="ps-3"><?= date('d/m/Y', strtotime($h['ngay'])) ?></td>
                                                <td>
                                                    <?php 
                                                        $map = [
                                                            'can_nang' => '<span class="badge bg-info">Theo dõi cân</span>', 
                                                            'tiem_vaccine' => '<span class="badge bg-primary">Tiêm phòng</span>', 
                                                            'uong_thuoc' => '<span class="badge bg-warning text-dark">Thuốc/Giun</span>', 
                                                            'kham_benh' => '<span class="badge bg-danger">Khám bệnh</span>'
                                                        ];
                                                        echo $map[$h['loai']] ?? '<span class="badge bg-secondary">'.$h['loai'].'</span>';
                                                    ?>
                                                </td>
                                                <td><?= $h['chi_tiet'] ?></td>
                                                <td class="text-center fw-bold"><?= $h['can_nang'] ?> kg</td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có nhật ký chăm sóc cho con bò này.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>