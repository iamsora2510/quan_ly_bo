<?php
include '../../config/db.php';

$id_phieu = $_GET['id'] ?? null;

if ($id_phieu) {
    // 1. Kiểm tra phiếu có tồn tại không
    $check = mysqli_query($conn, "SELECT id FROM phieu_thu_mua WHERE id = $id_phieu");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_begin_transaction($conn);

        try {
            // 2. Gỡ bỏ liên kết phiếu này ở bảng danh_sach_bo (để tránh lỗi khóa ngoại)
            mysqli_query($conn, "UPDATE danh_sach_bo SET ma_phieu_nhap = 0 WHERE ma_phieu_nhap = $id_phieu");

            // 3. Xóa phiếu thu mua
            mysqli_query($conn, "DELETE FROM phieu_thu_mua WHERE id = $id_phieu");

            mysqli_commit($conn);
            echo "<script>alert('Đã xóa phiếu thu mua thành công!'); window.location='danh-sach-phieu.php';</script>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Lỗi hệ thống: " . mysqli_error($conn) . "'); window.location='../danh-sach-phieu-nhap.php';</script>";
        }
    } else {
        echo "<script>alert('Phiếu không tồn tại!'); window.location='danh-sach-phieu-nhap.php';</script>";
    }
}
?>