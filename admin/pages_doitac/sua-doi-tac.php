<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. Lấy ID và ép kiểu số nguyên để an toàn
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn dữ liệu
$sql = "SELECT * FROM khach_hang WHERE id = $id";
$res = mysqli_query($conn, $sql);
$kh = mysqli_fetch_assoc($res);

// 3. Nếu không tìm thấy khách hàng thì báo lỗi luôn
if (!$kh) {
    echo "<script>alert('Lỗi: Không tìm thấy khách hàng này!'); window.location='quan-ly-doi-tac.php';</script>";
    exit();
}

// 4. Xử lý khi nhấn nút Lưu
if (isset($_POST['btnCapNhat'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $diachi = mysqli_real_escape_string($conn, $_POST['diachi']);
    $loai = $_POST['loai'];

    $update_sql = "UPDATE khach_hang SET 
                    ten_khach_hang='$ten', 
                    so_dien_thoai='$sdt', 
                    dia_chi='$diachi', 
                    loai_khach='$loai' 
                   WHERE id = $id";
                   
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Cập nhật thành công!'); window.location='quan-ly-doi-tac.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>
<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="card shadow-sm border-0" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-warning text-dark fw-bold">Sửa Thông Tin Khách Hàng</div>
            <form action="" method="POST" class="card-body">
                <div class="mb-3">
                    <label>Tên khách hàng:</label>
                    <input type="text" name="ten" class="form-control" value="<?= $kh['ten_khach_hang'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Số điện thoại:</label>
                    <input type="text" name="sdt" class="form-control" value="<?= $kh['so_dien_thoai'] ?>">
                </div>
                <div class="mb-3">
                    <label>Địa chỉ:</label>
                    <textarea name="diachi" class="form-control" rows="2"><?= $kh['dia_chi'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Loại khách:</label>
                    <select name="loai" class="form-select">
                        <option value="Thương lái" <?= $kh['loai_khach'] == 'Thương lái' ? 'selected' : '' ?>>Thương lái</option>
                        <option value="Lò mổ" <?= $kh['loai_khach'] == 'Lò mổ' ? 'selected' : '' ?>>Lò mổ</option>
                        <option value="Hộ dân mua lẻ" <?= $kh['loai_khach'] == 'Hộ dân mua lẻ' ? 'selected' : '' ?>>Hộ dân mua lẻ</option>
                    </select>
                </div>
                <div class="text-center">
                    <button name="btnCapNhat" class="btn btn-warning px-4">Lưu thay đổi</button>
                    <a href="quan-ly-doi-tac.php" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</main>