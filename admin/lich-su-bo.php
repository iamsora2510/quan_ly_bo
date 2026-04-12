<?php 
include '../config/db.php'; 
include './includes/header.php'; 
include './includes/sidebar.php'; 

$id_bo = $_GET['id'] ?? null;

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
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0">Hồ Sơ Chi Tiết: <?= $bo['ma_so_tai'] ?></h3>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="danh-sach-bo.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <img src="../assets/uploads/<?= $bo['hinh_anh'] ?: 'no-image.png' ?>" class="card-img-top" alt="Ảnh bò">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><?= $bo['ten_giong'] ?></h5>
                            <hr>
                            <p><strong>Ngày nhập:</strong> <?= date('d/m/Y', strtotime($bo['ngay_mua'])) ?></p>
                            <p><strong>Cân nặng nhập:</strong> <?= $bo['can_nang_nhap'] ?> kg</p>
                            <p><strong>Cân nặng hiện tại:</strong> <span class="text-danger fw-bold"><?= $bo['can_nang_hien_tai'] ?> kg</span></p>
                            <p><strong>Giá mua vào:</strong> <?= number_format($bo['gia_mua_vao']) ?> đ</p>
                            <p><strong>Phí chăm sóc:</strong> <?= number_format($tong_chiphi) ?> đ</p>
                            <div class="alert alert-success mt-3">
                                <h6 class="mb-0">TỔNG GIÁ VỐN:</h6>
                                <h4 class="fw-bold mb-0"><?= number_format($bo['gia_mua_vao'] + $tong_chiphi) ?> đ</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history me-2"></i>Nhật ký chăm sóc & Tăng trưởng</div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Hoạt động</th>
                                        <th>Chi tiết</th>
                                        <th>Cân nặng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($h = mysqli_fetch_assoc($res_history)): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($h['ngay'])) ?></td>
                                        <td>
                                            <?php 
                                                $map = ['can_nang'=>'Cân nặng', 'tiem_vaccine'=>'Tiêm phòng', 'uong_thuoc'=>'Thuốc/Giun', 'kham_benh'=>'Khám bệnh'];
                                                echo $map[$h['loai']] ?? $h['loai'];
                                            ?>
                                        </td>
                                        <td><?= $h['chi_tiet'] ?></td>
                                        <td><?= $h['can_nang'] ?> kg</td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>