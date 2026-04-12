<?php 
include '../config/db.php'; 
include './includes/header.php'; 
include './includes/sidebar.php'; 

$id_bo = $_GET['id'] ?? null;
if (!$id_bo) {
    echo "<script>window.location='danh-sach-bo.php';</script>";
    exit();
}

// Lấy thông tin con bò hiện tại
$res_bo = mysqli_query($conn, "SELECT * FROM danh_sach_bo WHERE id = $id_bo");
$bo = mysqli_fetch_assoc($res_bo);

if (isset($_POST['btn_save'])) {
    $ngay = $_POST['ngay_thuc_hien'];
    $loai = $_POST['loai_cham_soc'];
    $can_nang_moi = $_POST['can_nang'];
    $chi_tiet = mysqli_real_escape_string($conn, $_POST['chi_tiet']);
    $so_tien = $_POST['so_tien'] ?? 0;

    // 1. Lưu vào Nhật ký chăm sóc
    $sql_nhat_ky = "INSERT INTO nhat_ky_cham_soc (ma_bo, ngay_thuc_hien, can_nang, loai_cham_soc, chi_tiet) 
                    VALUES ('$id_bo', '$ngay', '$can_nang_moi', '$loai', '$chi_tiet')";
    
    if (mysqli_query($conn, $sql_nhat_ky)) {
        // 2. Cập nhật lại cân nặng hiện tại trong bảng danh_sach_bo nếu có nhập cân mới
        if ($can_nang_moi > 0) {
            mysqli_query($conn, "UPDATE danh_sach_bo SET can_nang_hien_tai = '$can_nang_moi' WHERE id = $id_bo");
        }

        // 3. Nếu có phát sinh chi phí (> 0) thì lưu vào bảng chi_phi_cham_soc
        if ($so_tien > 0) {
            $ten_chi_phi = "Chi phí " . $loai . ": " . $chi_tiet;
            mysqli_query($conn, "INSERT INTO chi_phi_cham_soc (ma_bo, ngay_chi, loai_chi_phi, so_tien) 
                                 VALUES ('$id_bo', '$ngay', '$ten_chi_phi', '$so_tien')");
        }

        echo "<script>alert('Cập nhật sức khỏe và chi phí thành công!'); window.location='danh-sach-bo.php';</script>";
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3><i class="bi bi-heart-pulse text-danger me-2"></i>Cập nhật Sức khỏe & Chi phí</h3>
            <p>Đang thực hiện cho bò mã tai: <strong><?= $bo['ma_so_tai'] ?></strong></p>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-info shadow">
                <form method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Ngày thực hiện:</label>
                                <input type="date" name="ngay_thuc_hien" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Loại chăm sóc:</label>
                                <select name="loai_cham_soc" class="form-select border-info">
                                    <option value="can_nang">Cân định kỳ</option>
                                    <option value="tiem_vaccine">Tiêm Vaccine</option>
                                    <option value="uong_thuoc">Cho uống thuốc / Tẩy giun</option>
                                    <option value="kham_benh">Khám bệnh</option>
                                    <option value="khac">Khác (Thức ăn, công chăm...)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-success">Số tiền chi phí (VNĐ):</label>
                                <input type="number" name="so_tien" class="form-control border-success" placeholder="Nhập 0 nếu không tốn phí">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Cân nặng mới (kg):</label>
                                <input type="number" step="0.1" name="can_nang" class="form-control" placeholder="Cân cũ: <?= $bo['can_nang_hien_tai'] ?> kg">
                                <small class="text-muted">Để trống nếu không cân</small>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Nội dung chi tiết:</label>
                                <input type="text" name="chi_tiet" class="form-control" placeholder="Ví dụ: Tiêm tụ huyết trùng, Mua thêm cám đậm đặc..." required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="danh-sach-bo.php" class="btn btn-secondary me-2">Hủy</a>
                        <button type="submit" name="btn_save" class="btn btn-info px-4 fw-bold">LƯU NHẬT KÝ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>