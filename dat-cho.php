<?php
session_start();
include 'config/db.php';

// 1. Kiểm tra đăng nhập (Bắt buộc phải đăng nhập mới cho giữ chỗ)
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để giữ chỗ bò!'); window.location='dang-nhap.php';</script>";
    exit();
}

$id_bo = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// 2. Xử lý khi khách bấm nút "Xác nhận"
if (isset($_POST['btnConfirm'])) {
    $ghi_chu = mysqli_real_escape_string($conn, $_POST['ghi_chu']);
    
    // Lưu vào bảng dat_cho_bo
    $sql = "INSERT INTO dat_cho_bo (ma_bo, ma_khach_hang, ghi_chu_khach) VALUES ($id_bo, $user_id, '$ghi_chu')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đã gửi yêu cầu giữ chỗ! Thanh STU sẽ gọi lại cho bạn sớm nhất.'); window.location='ds-dat-cho.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-warning text-dark text-center py-3 fw-bold">
                    <i class="bi bi-calendar2-check me-2"></i>XÁC NHẬN GIỮ CHỖ
                </div>
                <div class="card-body p-4 text-center">
                    <p class="mb-4">Bạn đang thực hiện giữ chỗ con bò mã số <strong>#<?= $id_bo ?></strong>.</p>
                    
                    <form method="POST">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold small">Lời nhắn/Yêu cầu (nếu có):</label>
                            <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ví dụ: Tôi muốn hỏi thêm về lịch tiêm chủng..."></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="btnConfirm" class="btn btn-success fw-bold py-2 rounded-pill shadow">GỬI YÊU CẦU GIỮ CHỖ</button>
                            <button type="button" onclick="window.history.back()" class="btn btn-light btn-sm text-muted">Hủy bỏ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>