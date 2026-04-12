<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn bảng ho_dan
$res = mysqli_query($conn, "SELECT * FROM ho_dan WHERE id = $id");
$hd = mysqli_fetch_assoc($res);

if (!$hd) {
    echo "<script>alert('Không tìm thấy hộ dân này!'); window.location='quan-ly-doi-tac.php';</script>";
    exit();
}

if(isset($_POST['btnCapNhat'])) {
    $ten = $_POST['ten'];
    $sdt = $_POST['sdt'];
    $diachi = $_POST['diachi'];

    // Cập nhật đúng tên cột trong DB của bạn
    $sql = "UPDATE ho_dan SET ten_ho_dan='$ten', so_dien_thoai='$sdt', dia_chi='$diachi' WHERE id = $id";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Cập nhật hộ dân thành công!'); window.location='quan-ly-doi-tac.php';</script>";
    }
}
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="card shadow-sm border-0" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-success text-white fw-bold">Sửa Thông Tin Hộ Dân</div>
            <form action="" method="POST" class="card-body">
                <div class="mb-3">
                    <label>Tên hộ dân:</label>
                    <input type="text" name="ten" class="form-control" value="<?= $hd['ten_ho_dan'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Số điện thoại:</label>
                    <input type="text" name="sdt" class="form-control" value="<?= $hd['so_dien_thoai'] ?>">
                </div>
                <div class="mb-3">
                    <label>Địa chỉ:</label>
                    <textarea name="diachi" class="form-control" rows="2"><?= $hd['dia_chi'] ?></textarea>
                </div>
                <div class="text-center">
                    <button name="btnCapNhat" class="btn btn-success px-4">Lưu thay đổi</button>
                    <a href="quan-ly-doi-tac.php" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</main>