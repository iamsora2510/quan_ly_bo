<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để gửi yêu cầu ký gửi!'); window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$res_kh = mysqli_query($conn, "SELECT id FROM khach_hang WHERE user_id = $user_id");
$row_kh = mysqli_fetch_assoc($res_kh);
$ma_kh = $row_kh['id'];

if (isset($_POST['btn_ky_gui'])) {
    $ma_giong = (int)$_POST['ma_giong'];
    $can_nang = (float)$_POST['can_nang'];
    $suc_khoe = mysqli_real_escape_string($conn, $_POST['suc_khoe']);
    
    // --- FIX LỖI TÊN BIẾN TẠI ĐÂY ---
    // Thẻ input bên dưới đặt name="sdt_lien_he" thì $_POST phải dùng đúng tên đó
    $sdt_lien_he = mysqli_real_escape_string($conn, $_POST['sdt_lien_he']);

    $hinh_anh = "";
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $ext = pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION);
        $hinh_anh = 'kg_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['hinh_anh']['tmp_name'], 'assets/uploads/' . $hinh_anh);
    }

    // Câu lệnh INSERT (đảm bảo cột so_dien_thoai_lien_he đã tồn tại trong DB)
    $sql = "INSERT INTO ky_gui_bo (ma_khach_hang, so_dien_thoai_lien_he, ma_giong, can_nang_ky_gui, hinh_anh, tinh_trang_suc_khoe) 
            VALUES ($ma_kh, '$sdt_lien_he', $ma_giong, $can_nang, '$hinh_anh', '$suc_khoe')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Gửi yêu cầu thành công!'); window.location='lich-su-ky-gui.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white py-3 text-center">
                    <h4 class="mb-0 fw-bold">ĐĂNG KÝ KÝ GỬI BÒ ONLINE</h4>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Giống bò:</label>
                            <select name="ma_giong" class="form-select border-success" required>
                                <?php
                                $giongs = mysqli_query($conn, "SELECT * FROM giong_bo");
                                while ($g = mysqli_fetch_assoc($giongs)) echo "<option value='{$g['id']}'>Bò {$g['ten_giong']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cân nặng (kg):</label>
                            <input type="number" name="can_nang" class="form-control border-success" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh bò thực tế:</label>
                            <input type="file" name="hinh_anh" class="form-control border-success" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại liên hệ:</label>
                            <input type="text" name="sdt_lien_he" class="form-control border-success"
                                placeholder="Số điện thoại để Admin gọi xác nhận" required>
                            <small class="text-muted italic">Chúng tôi sẽ gọi vào số này để xác nhận đơn hàng.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả sức khỏe:</label>
                            <textarea name="suc_khoe" class="form-control border-success" rows="3"></textarea>
                        </div>
                        <button type="submit" name="btn_ky_gui" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow">GỬI YÊU CẦU</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>