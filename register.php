<?php
include 'config/db.php';

if (isset($_POST['btn_register'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $dia_chi = mysqli_real_escape_string($conn, $_POST['dia_chi']);
    $pass = md5($_POST['password']);
    $role = 'ho_dan';

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$user'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Tên đăng nhập này đã có người sử dụng!";
    } else {
        // 1. Chèn vào bảng users
        $sql = "INSERT INTO users (username, password, fullname, email, sdt, dia_chi, role) 
                VALUES ('$user', '$pass', '$fullname', '$email', '$sdt', '$dia_chi', '$role')";
        
        if (mysqli_query($conn, $sql)) {
            // 2. LẤY ID VỪA TẠO CỦA USER ĐÓ
            $new_user_id = mysqli_insert_id($conn);

            // 3. TỰ ĐỘNG CHÈN SANG BẢNG KHACH_HANG (Kèm theo user_id để nhận diện)
            $sql_kh = "INSERT INTO khach_hang (ten_khach_hang, so_dien_thoai, dia_chi, loai_khach, user_id) 
                       VALUES ('$fullname', '$sdt', '$dia_chi', 'Hộ dân Web', $new_user_id)";
            
            mysqli_query($conn, $sql_kh);

            echo "<script>alert('Đăng ký thành công! Thông tin đã được đồng bộ vào hệ thống khách hàng.'); window.location='login.php';</script>";
        } else {
            $error = "Lỗi hệ thống: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký Hộ dân | Thanh STU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-card my-5">
        <h2 class="auth-title text-success"><i class="bi bi-house-heart me-2"></i>ĐĂNG KÝ HỘ DÂN</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" placeholder="user123" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="******" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Họ và tên chủ hộ</label>
                <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="vi_du@gmail.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Số điện thoại</label>
                    <input type="text" name="sdt" class="form-control" placeholder="090..." required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Địa chỉ cư trú</label>
                <textarea name="dia_chi" class="form-control" rows="2" placeholder="Số nhà, tên đường, xã/huyện..."></textarea>
            </div>

            <button type="submit" name="btn_register" class="btn btn-success btn-auth w-100 text-white shadow">
                XÁC NHẬN ĐĂNG KÝ
            </button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="login.php" class="text-success fw-bold text-decoration-none">Đăng nhập</a>
        </div>
    </div>
</body>
</html>