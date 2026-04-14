<?php
include '../../config/db.php';

$id = $_GET['id'];
$type = $_GET['type']; // Dùng để phân biệt xóa khách hay xóa hộ dân

if ($type == 'khach') {
    $sql = "DELETE FROM khach_hang WHERE id = $id";
} else {
    $sql = "DELETE FROM ho_dan WHERE id = $id";
}

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Đã xóa đối tác thành công!'); window.location='../quan-ly-doi-tac.php';</script>";
} else {
    echo "<script>alert('Lỗi: Không thể xóa đối tác này vì có liên quan đến dữ liệu khác!'); window.location='quan-ly-doi-tac.php';</script>";
}
?>