<?php
include 'config/db.php';
session_start();

// Kiểm tra nếu có dữ liệu gửi lên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_gui_dg'])) {
    
    // 1. Kiểm tra đăng nhập (Phải là khách hàng mới được đánh giá)
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để thực hiện đánh giá!'); window.location='login.php';</script>";
        exit;
    }

    // 2. Lấy dữ liệu từ Form
    $ma_kh = $_SESSION['user_id'];
    $ma_bo = isset($_POST['ma_bo']) ? (int)$_POST['ma_bo'] : 0;
    $so_sao = (int)$_POST['so_sao'];
    $noi_dung = mysqli_real_escape_string($conn, $_POST['noi_dung']);

    // 3. Chèn vào database (Mặc định trang_thai = 0 để Admin duyệt)
    $sql = "INSERT INTO danh_gia (ma_khach_hang, ma_bo, noi_dung, so_sao, trang_thai, ngay_danh_gia) 
            VALUES ($ma_kh, $ma_bo, '$noi_dung', $so_sao, 0, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Cảm ơn Thanh! Đánh giá đã được gửi và đang chờ Admin duyệt.'); window.location='index.php';</script>";
    } else {
        echo "Lỗi hệ thống: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
}
?>