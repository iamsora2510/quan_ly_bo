<?php 
include '../config/db.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

$errors = []; 

if (isset($_POST['btn_save'])) {
    $ma_ho_dan = $_POST['ma_ho_dan'];
    $ngay_mua = $_POST['ngay_mua'];
    $so_luong = $_POST['so_luong']; // Cột mới thêm
    
    // Lấy dữ liệu tiền và làm sạch (xóa dấu phẩy nếu có)
    $phi_van_chuyen = str_replace(',', '', $_POST['chi_phi_xe']); 
    $tong_tien_phieu = str_replace(',', '', $_POST['tong_tien_bo']); 
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu']);

    // --- VALIDATION (Kiểm tra dữ liệu) ---
    if (empty($ma_ho_dan)) $errors[] = "Vui lòng chọn hộ dân cung cấp.";
    if (!is_numeric($so_luong) || $so_luong <= 0) $errors[] = "Số lượng bò phải là số nguyên dương.";
    
    $today = date('Y-m-d');
    if (empty($ngay_mua) || $ngay_mua > $today) $errors[] = "Ngày thu mua không hợp lệ.";

    if (!is_numeric($tong_tien_phieu) || $tong_tien_phieu <= 0) $errors[] = "Tổng tiền bò phải là số dương.";
    if (!is_numeric($phi_van_chuyen) || $phi_van_chuyen < 0) $errors[] = "Phí vận chuyển không được âm.";

    // --- LƯU VÀO DATABASE ---
    if (empty($errors)) {
        // SQL khớp hoàn toàn với Database của Thanh (có thêm so_luong)
        $sql = "INSERT INTO phieu_thu_mua (ma_ho_dan, so_luong, ngay_mua, phi_van_chuyen, tong_tien_phieu) 
                VALUES ('$ma_ho_dan', '$so_luong', '$ngay_mua', '$phi_van_chuyen', '$tong_tien_phieu','ghi_chu')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Lập phiếu nhập thành công!'); window.location='danh-sach-phieu.php';</script>";
            exit();
        } else {
            $errors[] = "Lỗi SQL: " . mysqli_error($conn);
        }
    }
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid"><h3 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Lập Phiếu Thu Mua</h3></div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-success shadow-sm">
                <form method="POST" id="formPhieuNhap">
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger shadow-sm mb-4">
                                <ul class="mb-0">
                                    <?php foreach($errors as $err) echo "<li><i class='bi bi-exclamation-triangle-fill me-2'></i>$err</li>"; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Hộ dân cung cấp:</label>
                                <select name="ma_ho_dan" class="form-select border-primary">
                                    <option value="">-- Chọn hộ dân --</option>
                                    <?php 
                                    $res = mysqli_query($conn, "SELECT id, ten_ho_dan FROM ho_dan ORDER BY ten_ho_dan ASC");
                                    while($h = mysqli_fetch_assoc($res)) {
                                        $selected = (isset($_POST['ma_ho_dan']) && $_POST['ma_ho_dan'] == $h['id']) ? 'selected' : '';
                                        echo "<option value='".$h['id']."' $selected>".$h['ten_ho_dan']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Ngày thu mua:</label>
                                <input type="date" name="ngay_mua" class="form-control" value="<?= $_POST['ngay_mua'] ?? date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="row border-top pt-3 mt-2">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-success">Tiền mua bò (Cả đàn):</label>
                                <div class="input-group">
                                    <input type="number" name="tong_tien_bo" id="tong_tien_bo" class="form-control border-success" oninput="tinhTongPhieuNhap()" value="<?= $_POST['tong_tien_bo'] ?? '' ?>">
                                    <span class="input-group-text bg-success text-white">đ</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Số lượng (con):</label>
                                <input type="number" name="so_luong" id="so_luong" class="form-control" placeholder="Ví dụ: 5" value="<?= $_POST['so_luong'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-info">Tiền xe vận chuyển:</label>
                                <div class="input-group">
                                    <input type="number" name="chi_phi_xe" id="chi_phi_xe" class="form-control border-info" oninput="tinhTongPhieuNhap()" value="<?= $_POST['chi_phi_xe'] ?? '' ?>">
                                    <span class="input-group-text bg-info text-white">đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3 shadow-sm">
                            <h5 class="mb-0 fw-bold text-dark">TỔNG THANH TOÁN DỰ KIẾN:</h5>
                            <h4 class="mb-0 fw-bold text-danger" id="hien_thi_tong">0 đ</h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Ghi chú phiếu nhập:</label>
                            <textarea name="ghi_chu" class="form-control" rows="3"><?= $_POST['ghi_chu'] ?? '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end bg-light">
                        <button type="reset" class="btn btn-secondary px-4 me-2">Làm lại</button>
                        <button type="submit" name="btn_save" class="btn btn-success px-5 fw-bold shadow">XÁC NHẬN LẬP PHIẾU</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="../../assets/js/admin-scripts.js"></script>
<?php include 'includes/footer.php'; ?>