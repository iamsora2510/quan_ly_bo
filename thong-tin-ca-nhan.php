<?php
session_start();
include 'config/db.php';

// Chặn nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 1. Xử lý cập nhật thông tin
if (isset($_POST['btnUpdate'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $dia_chi = mysqli_real_escape_string($conn, $_POST['dia_chi']);

    $sql_update = "UPDATE users SET fullname='$fullname', email='$email', sdt='$sdt', dia_chi='$dia_chi' WHERE id=$user_id";
    
    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['user_name'] = $fullname; // Cập nhật lại tên trên header ngay lập tức
        $message = "<div class='alert alert-success'>Cập nhật thông tin thành công!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Lỗi: " . mysqli_error($conn) . "</div>";
    }
}

// 2. Lấy dữ liệu mới nhất từ DB để hiển thị lên Form
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm text-center p-4 rounded-4">
                        <div class="mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['fullname']) ?>&background=198754&color=fff&size=128" 
                                 class="rounded-circle shadow-sm" alt="Avatar">
                        </div>
                        <h5 class="fw-bold mb-1"><?= $user['fullname'] ?></h5>
                        <p class="text-muted small mb-3">@<?= $user['username'] ?></p>
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                            <i class="bi bi-patch-check-fill me-1"></i> Hộ dân thành viên
                        </span>
                        <hr class="my-4">
                        <div class="text-start small">
                            <p class="mb-2"><strong>Ngày tham gia:</strong> 12/04/2026</p>
                            <p class="mb-0"><strong>Vai trò:</strong> <?= ($user['role'] == 'ho_dan') ? 'Hộ chăn nuôi' : 'Quản trị viên' ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4 text-success">Thiết lập tài khoản</h4>
                            <?= $message ?>
                            
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Tên đăng nhập</label>
                                        <input type="text" class="form-control bg-light" value="<?= $user['username'] ?>" readonly>
                                        <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Họ và tên chủ hộ</label>
                                        <input type="text" name="fullname" class="form-control" value="<?= $user['fullname'] ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Địa chỉ Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Số điện thoại</label>
                                        <input type="text" name="sdt" class="form-control" value="<?= $user['sdt'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Địa chỉ thường trú</label>
                                    <textarea name="dia_chi" class="form-control" rows="3"><?= $user['dia_chi'] ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="doi-mat-khau.php" class="text-decoration-none text-danger small fw-bold">
                                        <i class="bi bi-key me-1"></i> Đổi mật khẩu?
                                    </a>
                                    <button type="submit" name="btnUpdate" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm">
                                        LƯU THAY ĐỔI
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>