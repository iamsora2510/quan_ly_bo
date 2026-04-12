<?php 
include '../config/db.php'; 
include './includes/header.php'; 
include './includes/sidebar.php'; 

// Lấy danh sách Giống, Chuồng và Phiếu nhập (Join thêm hộ dân cho dễ chọn)
$ds_giong = mysqli_query($conn, "SELECT * FROM giong_bo ORDER BY ten_giong ASC");
$ds_chuong = mysqli_query($conn, "SELECT * FROM chuong_nuoi ORDER BY ten_chuong ASC");
$ds_phieu = mysqli_query($conn, "SELECT p.*, h.ten_ho_dan FROM phieu_thu_mua p JOIN ho_dan h ON p.ma_ho_dan = h.id ORDER BY p.ngay_mua DESC");

if (isset($_POST['btn_save'])) {
    $ma_so_tai = mysqli_real_escape_string($conn, $_POST['ma_so_tai']);
    $ma_giong = $_POST['ma_giong'];
    $ma_chuong = $_POST['ma_chuong'];
    $ma_phieu = $_POST['ma_phieu_nhap'];
    $can_nang = $_POST['can_nang'];
    $gia_mua = $_POST['gia_mua'];
    $trang_thai = 'dang_nuoi';
    $hinh_anh = ""; 

    // XỬ LÝ UPLOAD HÌNH ẢNH
    if (isset($_FILES['anh_bo']) && $_FILES['anh_bo']['name'] != "") {
        $target_dir = "../assets/uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true); // Tạo thư mục nếu chưa có
        
        $extension = pathinfo($_FILES["anh_bo"]["name"], PATHINFO_EXTENSION);
        $hinh_anh = "BO_" . time() . "." . $extension; // Tên file ngắn gọn, tránh lỗi ký tự đặc biệt
        move_uploaded_file($_FILES["anh_bo"]["tmp_name"], $target_dir . $hinh_anh);
    }

    $sql_insert = "INSERT INTO danh_sach_bo (ma_so_tai, hinh_anh, ma_giong, ma_chuong, ma_phieu_nhap, can_nang_nhap, can_nang_hien_tai, gia_mua_vao, trang_thai) 
                   VALUES ('$ma_so_tai', '$hinh_anh', '$ma_giong', '$ma_chuong', '$ma_phieu', '$can_nang', '$can_nang', '$gia_mua', '$trang_thai')";
    
    if (mysqli_query($conn, $sql_insert)) {
        echo "<script>
                alert('Thêm bò vào kho thành công!');
                window.location.href='danh-sach-bo.php';
              </script>";
        exit();
    } else {
        $error = "Lỗi: " . mysqli_error($conn);
    }
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid"><h3 class="mb-0"><i class="bi bi-plus-circle-dotted me-2"></i>Thêm Bò Mới Vào Kho</h3></div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline shadow-sm">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-octagon me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Mã số tai:</label>
                                <input type="text" name="ma_so_tai" class="form-control border-primary" placeholder="Ví dụ: BO-100" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Cân nặng khi nhập (kg):</label>
                                <input type="number" step="0.01" name="can_nang" class="form-control" placeholder="250.5" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Thuộc phiếu nhập:</label>
                                <select name="ma_phieu_nhap" class="form-select" required>
                                    <option value="">-- Chọn phiếu nhập --</option>
                                    <?php while($p = mysqli_fetch_assoc($ds_phieu)): ?>
                                        <option value="<?= $p['id'] ?>">Phiếu: <?= $p['ten_ho_dan'] ?> (<?= date('d/m/Y', strtotime($p['ngay_mua'])) ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Giống bò:</label>
                                <select name="ma_giong" class="form-select" required>
                                    <?php while($g = mysqli_fetch_assoc($ds_giong)): ?>
                                        <option value="<?= $g['id'] ?>"><?= $g['ten_giong'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Chuồng nuôi:</label>
                                <select name="ma_chuong" class="form-select" required>
                                    <?php while($c = mysqli_fetch_assoc($ds_chuong)): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['ten_chuong'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Giá mua vào (VNĐ):</label>
                                <div class="input-group">
                                    <input type="number" name="gia_mua" class="form-control" placeholder="15000000" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary">Hình ảnh nhận diện:</label>
                                <input type="file" name="anh_bo" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-light">
                        <a href="danh-sach-bo.php" class="btn btn-secondary px-4 me-2">Hủy bỏ</a>
                        <button type="submit" name="btn_save" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> LƯU THÔNG TIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include './includes/footer.php'; ?>