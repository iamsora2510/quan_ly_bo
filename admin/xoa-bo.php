<?php
include '../config/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. Lấy thông tin ảnh để xóa file vật lý trước
    $res = mysqli_query($conn, "SELECT hinh_anh FROM danh_sach_bo WHERE id = $id");
    $data = mysqli_fetch_assoc($res);
    
    if ($data) {
        $file_name = $data['hinh_anh'];
        $path = "../assets/uploads/" . $file_name;

        // Chỉ xóa file nếu file đó thực sự tồn tại
        if (!empty($file_name) && file_exists($path)) {
            unlink($path);
        }

        // 2. Thực hiện lệnh xóa bò
        $sql_xoa = "DELETE FROM danh_sach_bo WHERE id = $id";

        if (mysqli_query($conn, $sql_xoa)) {
            echo "<script>alert('Đã xóa bò thành công!'); window.location='danh-sach-bo.php';</script>";
        } else {
            echo "Lỗi Database: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Con bò này không tồn tại!'); window.location='danh-sach-bo.php';</script>";
    }
} else {
    header("Location: danh-sach-bo.php");
}
?>