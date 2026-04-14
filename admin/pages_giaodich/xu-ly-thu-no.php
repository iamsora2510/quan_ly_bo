<?php
include '../../config/db.php';
mysqli_begin_transaction($conn);

try {
    $id_phieu = $_POST['id_phieu'];
    $so_tien_thu = floatval($_POST['so_tien_thu']);
    $ngay_thu = date('Y-m-d H:i:s'); // Lấy ngày giờ hiện tại

    // 1. Ghi vào bảng Lịch sử thu nợ
    $stmt_log = $conn->prepare("INSERT INTO lich_su_thu_no (ma_phieu_xuat, ngay_thu, so_tien_thu) VALUES (?, ?, ?)");
    $stmt_log->bind_param("isd", $id_phieu, $ngay_thu, $so_tien_thu);
    $stmt_log->execute();

    // 2. Lấy số tiền đã trả cũ để cộng dồn
    $res = mysqli_query($conn, "SELECT tong_tien, so_tien_tra_truoc FROM phieu_xuat_ban WHERE id = $id_phieu");
    $data = mysqli_fetch_assoc($res);
    $da_tra_moi = $data['so_tien_tra_truoc'] + $so_tien_thu;
    $status = ($da_tra_moi >= $data['tong_tien']) ? 'da_thanh_toan' : 'con_no';

    // 3. Cập nhật lại tổng tiền đã trả ở bảng Phiếu xuất
    $stmt_up = $conn->prepare("UPDATE phieu_xuat_ban SET so_tien_tra_truoc = ?, trang_thai_thanh_toan = ? WHERE id = ?");
    $stmt_up->bind_param("dsi", $da_tra_moi, $status, $id_phieu);
    $stmt_up->execute();

    mysqli_commit($conn);
    echo "<script>alert('Đã thu tiền và lưu vào lịch sử!'); window.location='../cong-no.php';</script>";

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Lỗi: " . $e->getMessage();
}
?>