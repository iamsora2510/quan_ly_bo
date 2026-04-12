<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

if(isset($_POST['btnThem'])) {
    // Lấy dữ liệu từ form
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $diachi = mysqli_real_escape_string($conn, $_POST['diachi']);

    // SQL sửa lại: INSERT vào bảng ho_dan, bỏ cột loai_khach vì DB không có
    $sql = "INSERT INTO ho_dan (ten_ho_dan, so_dien_thoai, dia_chi) VALUES ('$ten', '$sdt', '$diachi')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Thêm hộ dân thành công!'); window.location='quan-ly-doi-tac.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="card shadow-sm border-0" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-house-add me-2"></i>Thêm Hộ Dân Cung Cấp</h5>
            </div>
            <form action="" method="POST" class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên hộ dân:</label>
                    <input type="text" name="ten" class="form-control" placeholder="Ví dụ: Nguyễn Văn Teo" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số điện thoại:</label>
                    <input type="text" name="sdt" class="form-control" placeholder="0901234567">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ:</label>
                    <textarea name="diachi" class="form-control" rows="2" placeholder="Địa chỉ hộ dân..."></textarea>
                </div>
                
                <div class="text-center">
                    <button name="btnThem" class="btn btn-primary px-4 shadow">
                        <i class="bi bi-save me-1"></i> Lưu Thông Tin
                    </button>
                    <a href="quan-ly-doi-tac.php" class="btn btn-secondary px-4">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>