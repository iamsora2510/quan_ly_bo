<?php
include '../../config/db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($conn);

try {
    // 1. Lấy dữ liệu từ form
    $ma_kh = $_POST['ma_kh'];
    $ngay = $_POST['ngay_ban']; 
    $ngay_ban_giao = $_POST['ngay_ban_giao']; 
    
    // NHẬN ID ĐẶT CHỖ (Nếu có truyền từ form)
    $id_dat_cho = isset($_POST['id_dat_cho']) ? (int)$_POST['id_dat_cho'] : 0;
    
    // Xử lý tiền tệ
    $tra_truoc = floatval(str_replace('.', '', $_POST['tra_truoc']));
    $gia_ban_list = $_POST['gia_ban'];

    if (isset($_POST['ban_le']) && $_POST['ban_le'] == '1') {
        $ma_bo_le = (int)$_POST['ma_bo_hidden'];
        $gia_ban_clean = [$ma_bo_le => floatval($_POST['gia_ban_that'])];
    } else {
        $gia_ban_clean = [];
        foreach ($gia_ban_list as $id_bo => $gia_str) {
            $gia_ban_clean[$id_bo] = floatval(str_replace('.', '', $gia_str));
        }
    }

    $tong_tien = array_sum($gia_ban_clean);
    $status = ($tra_truoc >= $tong_tien) ? 'da_thanh_toan' : 'con_no';

    // 2. Lưu bảng phieu_xuat_ban
    $stmt = $conn->prepare("INSERT INTO phieu_xuat_ban (ma_khach_hang, ngay_ban, ngay_ban_giao, tong_tien, so_tien_tra_truoc, trang_thai_thanh_toan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdds", $ma_kh, $ngay, $ngay_ban_giao, $tong_tien, $tra_truoc, $status);
    $stmt->execute();

    $id_phieu_vua_tao = $conn->insert_id;

 // 3. Cập nhật chi tiết và trạng thái bò
    // Chúng ta lấy danh sách ID bò từ input ẩn để đảm bảo không sót con nào
    $id_bo_list = $_POST['id_bo_list'] ?? array_keys($_POST['gia_ban']);

    foreach ($id_bo_list as $id_bo) {
        $id_bo = (int)$id_bo;
        // Lấy giá bán tương ứng với ID bò này
        $gia = isset($_POST['gia_ban'][$id_bo]) ? floatval(str_replace('.', '', $_POST['gia_ban'][$id_bo])) : 0;

        // Lấy cân nặng hiện tại
        $res_weight = mysqli_query($conn, "SELECT can_nang_hien_tai FROM danh_sach_bo WHERE id = $id_bo");
        $row_weight = mysqli_fetch_assoc($res_weight);
        $kg = isset($row_weight['can_nang_hien_tai']) ? $row_weight['can_nang_hien_tai'] : 0;

        // A. Lưu chi tiết hóa đơn
        $st_ct = $conn->prepare("INSERT INTO chi_tiet_phieu_xuat (ma_phieu_xuat, ma_bo, can_nang_ban, gia_ban_con_nay, ngay_ban, ngay_giao) VALUES (?, ?, ?, ?, ?, ?)");
        $st_ct->bind_param("iiddss", $id_phieu_vua_tao, $id_bo, $kg, $gia, $ngay, $ngay_ban_giao);
        $st_ct->execute();

        // B. CẬP NHẬT TRẠNG THÁI BÒ (Ghi đè trực tiếp để chắc chắn đổi màu)
        $sql_update_status = "UPDATE danh_sach_bo SET trang_thai = 'da_ban' WHERE id = $id_bo";
        mysqli_query($conn, $sql_update_status);
    }

    // 4. MỚI: CẬP NHẬT TRẠNG THÁI ĐƠN ĐẶT CHỖ SANG 'hoan_tat'
    if ($id_dat_cho > 0) {
        $st_up_dc = $conn->prepare("UPDATE dat_cho_bo SET trang_thai = 'hoan_tat' WHERE id = ?");
        $st_up_dc->bind_param("i", $id_dat_cho);
        $st_up_dc->execute();
    }

    mysqli_commit($conn);
    header("Location: ../danh-sach-hoa-don.php");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Lỗi hệ thống: " . $e->getMessage();
}