<?php 
include '../config/db.php'; 
include './includes/header.php'; 
include './includes/sidebar.php'; 

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM danh_sach_bo WHERE id = $id"));
$ds_giong = mysqli_query($conn, "SELECT * FROM giong_bo");
$ds_chuong = mysqli_query($conn, "SELECT * FROM chuong_nuoi");

if (isset($_POST['btn_update'])) {
    $ma_so_tai = $_POST['ma_so_tai'];
    $ma_giong = $_POST['ma_giong'];
    $ma_chuong = $_POST['ma_chuong'];
    $can_nang = $_POST['can_nang'];
    $trang_thai = $_POST['trang_thai'];
    $hinh_anh = $data['hinh_anh'];

    if ($_FILES['anh_bo']['name'] != "") {
        $target_dir = "../assets/uploads/";
        $hinh_anh = time() . "_" . basename($_FILES["anh_bo"]["name"]);
        move_uploaded_file($_FILES["anh_bo"]["tmp_name"], $target_dir . $hinh_anh);
    }

    $sql_update = "UPDATE danh_sach_bo SET ma_so_tai='$ma_so_tai', ma_giong='$ma_giong', ma_chuong='$ma_chuong', can_nang_hien_tai='$can_nang', trang_thai='$trang_thai', hinh_anh='$hinh_anh' WHERE id=$id";
    if (mysqli_query($conn, $sql_update)) {
        echo "<script>window.location.href='danh-sach-bo.php';</script>";
        exit();
    }
}
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid"><h3 class="mb-0">Cập nhật: <span class="text-primary"><?php echo $data['ma_so_tai']; ?></span></h3></div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-warning card-outline shadow-sm">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Mã số tai:</label>
                                <input type="text" name="ma_so_tai" class="form-control" value="<?php echo $data['ma_so_tai']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giống bò:</label>
                                <select name="ma_giong" class="form-select" required>
                                    <?php while($g = mysqli_fetch_assoc($ds_giong)) { ?>
                                        <option value="<?php echo $g['id']; ?>" <?php echo ($g['id']==$data['ma_giong'])?'selected':''; ?>><?php echo $g['ten_giong']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Chuồng nuôi:</label>
                                <select name="ma_chuong" class="form-select" required>
                                    <?php while($c = mysqli_fetch_assoc($ds_chuong)) { ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ($c['id']==$data['ma_chuong'])?'selected':''; ?>><?php echo $c['ten_chuong']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cân nặng (kg):</label>
                                <input type="number" step="0.01" name="can_nang" class="form-control" value="<?php echo $data['can_nang_hien_tai']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Trạng thái:</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="dang_nuoi" <?php if($data['trang_thai']=='dang_nuoi') echo 'selected'; ?>>Đang nuôi</option>
                                    <option value="da_ban" <?php if($data['trang_thai']=='da_ban') echo 'selected'; ?>>Đã bán</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Thay đổi hình ảnh:</label>
                            <input type="file" name="anh_bo" class="form-control mb-3">
                            <?php if($data['hinh_anh']): ?>
                                <div class="text-center bg-light p-3 rounded">
                                    <p class="small text-muted">Ảnh bò hiện tại</p>
                                    <img src="../assets/uploads/<?php echo $data['hinh_anh']; ?>" class="img-preview shadow">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-white">
                        <a href="danh-sach-bo.php" class="btn btn-secondary">Hủy</a>
                        <button type="submit" name="btn_update" class="btn btn-warning px-4 text-white fw-bold">LƯU THAY ĐỔI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include './includes/footer.php'; ?>