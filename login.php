<?php
session_start();
include 'config/db.php';

if (isset($_POST['btn_login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    // Vì DB của Thanh đang lưu MD5 (chuỗi e10adc...) nên dùng md5() ở đây
    $pass = md5($_POST['password']);

    // Truy vấn vào bảng users theo ảnh Thanh gửi
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Lưu thông tin vào Session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['fullname']; // Lấy cột fullname hiển thị cho đẹp
        $_SESSION['role'] = $row['role']; // Lưu quyền để phân biệt admin/ho_dan

        // Phân quyền chuyển hướng
        if ($row['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Tài khoản hoặc mật khẩu không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống | Thanh STU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h2 class="auth-title"><i class="bi bi-shield-lock-fill me-2"></i>HỆ THỐNG BÒ</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger py-2 small text-center shadow-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-success"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="admin, hodan1..." required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-success"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" name="btn_login" class="btn btn-success btn-auth w-100 text-white shadow">ĐĂNG NHẬP HỆ THỐNG</button>
        </form>

        <div class="auth-footer">
            Bán bò - Thu mua - Ký gửi <br>
            <a href="register.php" class="text-success fw-bold text-decoration-none">Đăng ký tài khoản mới</a>
        </div>
    </div>
</body>
</html>