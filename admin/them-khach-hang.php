<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

if(isset($_POST['btnThem'])) {
    $ten = $_POST['ten'];
    $sdt = $_POST['sdt'];
    $diachi = $_POST['diachi'];
    $loai = $_POST['loai'];

    $sql = "INSERT INTO khach_hang (ten_khach_hang, so_dien_thoai, dia_chi, loai_khach) VALUES ('$ten', '$sdt', '$diachi', '$loai')";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Thêm thành công!'); window.location='quan-ly-doi-tac.php';</script>";
    }
}
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="card shadow-sm border-0" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thêm Khách Hàng Mới</h5>
            </div>
            <form action="" method="POST" class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tên khách hàng/Thương lái:</label>
                    <input type="text" name="ten" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại:</label>
                    <input type="text" name="sdt" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ:</label>
                    <textarea name="diachi" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại khách:</label>
                    <select name="loai" class="form-control">
                        <option value="Thương lái">Thương lái</option>
                        <option value="Lò mổ">Lò mổ</option>
                        <option value="Hộ dân mua lẻ">Hộ dân mua lẻ</option>
                    </select>
                </div>
                <div class="text-center">
                    <button name="btnThem" class="btn btn-primary px-4">Lưu Thông Tin</button>
                    <a href="quan-ly-doi-tac.php" class="btn btn-secondary px-4">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</main>