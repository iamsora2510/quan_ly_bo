<?php
session_start();
include 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['btnChangePass'])) {
    $old_pass = md5($_POST['old_pass']);
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // 1. Kiểm tra mật khẩu cũ có đúng không
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($query);

    if ($old_pass !== $user['password']) {
        $message = "<div class='alert alert-danger'>Mật khẩu cũ không chính xác!</div>";
    } elseif ($new_pass !== $confirm_pass) {
        // 2. Kiểm tra mật khẩu mới và xác nhận có khớp không
        $message = "<div class='alert alert-danger'>Mật khẩu mới và xác nhận không khớp!</div>";
    } elseif (strlen($new_pass) < 6) {
        // 3. Validation độ dài mật khẩu (Mục 2 trong bảng Validation của Thanh)
        $message = "<div class='alert alert-warning'>Mật khẩu mới phải có ít nhất 6 ký tự!</div>";
    } else {
        // 4. Cập nhật mật khẩu mới (Mã hóa MD5)
        $hashed_new_pass = md5($new_pass);
        $sql_update = "UPDATE users SET password = '$hashed_new_pass' WHERE id = $user_id";
        
        if (mysqli_query($conn, $sql_update)) {
            $message = "<div class='alert alert-success'>Đổi mật khẩu thành công!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Lỗi hệ thống, vui lòng thử lại!</div>";
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>ĐỔI MẬT KHẨU</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form method="POST" onsubmit="return validatePassword()">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mật khẩu hiện tại</label>
                            <input type="password" name="old_pass" class="form-control" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mật khẩu mới</label>
                            <input type="password" name="new_pass" id="new_pass" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_pass" id="confirm_pass" class="form-control" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="btnChangePass" class="btn btn-success fw-bold py-2 rounded-pill shadow-sm">
                                CẬP NHẬT MẬT KHẨU
                            </button>
                            <a href="tai-khoan.php" class="btn btn-light btn-sm text-muted">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 alert alert-light border small text-muted">
                <i class="bi bi-info-circle me-1"></i> <strong>Mẹo bảo mật:</strong> Nên sử dụng mật khẩu mạnh bao gồm chữ cái, chữ số và ký tự đặc biệt để bảo vệ tài khoản hộ dân của bạn.
            </div>
        </div>
    </div>
</div>

<script>
// Thêm một lớp Validation bằng Javascript để trải nghiệm mượt hơn
function validatePassword() {
    var newPass = document.getElementById("new_pass").value;
    var confirmPass = document.getElementById("confirm_pass").value;
    
    if (newPass.length < 6) {
        alert("Mật khẩu mới phải từ 6 ký tự trở lên!");
        return false;
    }
    
    if (newPass !== confirmPass) {
        alert("Mật khẩu xác nhận không trùng khớp!");
        return false;
    }
    return true;
}
</script>

<?php include 'includes/footer.php'; ?>