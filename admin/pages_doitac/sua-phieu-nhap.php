<?php 
include '../config/db.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

$errors = []; 
// Lấy ID từ URL hoặc từ Form gửi lên
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if ($id) {
    $res = mysqli_query($conn, "SELECT * FROM phieu_thu_mua WHERE id = $id");
    $data = mysqli_fetch_assoc($res);
    if (!$data) {
        echo "<script>alert('Không tìm thấy phiếu!'); window.location='danh-sach-phieu.php';</script>";
        exit();
    }
}

if (isset($_POST['btn_update'])) {
    $ma_ho_dan = $_POST['ma_ho_dan'];
    $ngay_mua = $_POST['ngay_mua'];
    $so_luong = $_POST['so_luong'];
    $phi_van_chuyen = str_replace(',', '', $_POST['chi_phi_xe']); 
    $tong_tien_phieu = str_replace(',', '', $_POST['tong_tien_bo']); 
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu'] ?? '');

    if (!is_numeric($so_luong) || $so_luong < 0) $errors[] = "Số lượng bò không hợp lệ.";
    if (!is_numeric($tong_tien_phieu) || $tong_tien_phieu <= 0) $errors[] = "Tổng tiền bò phải là số dương.";

    if (empty($errors)) {
        // Câu lệnh UPDATE khớp 100% với Database của Thanh
        $sql = "UPDATE phieu_thu_mua SET 
                ma_ho_dan = '$ma_ho_dan', 
                so_luong = '$so_luong', 
                ngay_mua = '$ngay_mua', 
                phi_van_chuyen = '$phi_van_chuyen', 
                tong_tien_phieu = '$tong_tien_phieu', 
                ghi_chu = '$ghi_chu' 
                WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Cập nhật phiếu thành công!'); window.location='danh-sach-phieu.php';</script>";
            exit();
        } else {
            $errors[] = "Lỗi hệ thống (SQL): " . mysqli_error($conn);
        }
    }
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid"><h3><i class="bi bi-pencil-square me-2"></i>Chỉnh Sửa Phiếu Nhập #<?= $id ?></h3></div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-warning shadow-sm">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger shadow-sm mb-4">
                                <ul class="mb-0"><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Hộ dân cung cấp:</label>
                                <select name="ma_ho_dan" class="form-select border-primary">
                                    <?php 
                                    $res_ho = mysqli_query($conn, "SELECT id, ten_ho_dan FROM ho_dan");
                                    while($h = mysqli_fetch_assoc($res_ho)) {
                                        $sel = ($h['id'] == $data['ma_ho_dan']) ? 'selected' : '';
                                        echo "<option value='".$h['id']."' $sel>".$h['ten_ho_dan']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Ngày thu mua:</label>
                                <input type="date" name="ngay_mua" class="form-control" value="<?= $data['ngay_mua'] ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-success">Tiền mua bò (Đàn):</label>
                                <input type="number" name="tong_tien_bo" id="tong_tien_bo" class="form-control" oninput="tinhTongPhieuNhap()" value="<?= (int)$data['tong_tien_phieu'] ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Số lượng (con):</label>
                                <input type="number" name="so_luong" id="so_luong" class="form-control" value="<?= $data['so_luong'] ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-info">Tiền xe:</label>
                                <input type="number" name="chi_phi_xe" id="chi_phi_xe" class="form-control" oninput="tinhTongPhieuNhap()" value="<?= (int)$data['phi_van_chuyen'] ?>">
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3">
                            <h5 class="mb-0 fw-bold text-dark">TỔNG SAU KHI SỬA:</h5>
                            <h4 class="mb-0 fw-bold text-danger" id="hien_thi_tong"><?= number_format($data['tong_tien_phieu'] + $data['phi_van_chuyen']) ?> đ</h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Ghi chú:</label>
                            <textarea name="ghi_chu" class="form-control" rows="3"><?= $data['ghi_chu'] ?? '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="danh-sach-phieu.php" class="btn btn-secondary px-4 me-2">Hủy</a>
                        <button type="submit" name="btn_update" class="btn btn-warning px-5 fw-bold shadow">CẬP NHẬT PHIẾU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="../../assets/js/admin-scripts.js"></script>
<?php include 'includes/footer.php'; ?>