<?php
include '../config/db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($conn);

try {
    // 1. Lấy dữ liệu từ form
    $ma_kh = $_POST['ma_kh'];
    $ngay = $_POST['ngay_ban'];
    $ngay_ban_giao = $_POST['ngay_ban_giao']; // CẬP NHẬT MỚI: Nhận ngày bàn giao
    $id_dat_cho = isset($_POST['id_dat_cho']) ? (int)$_POST['id_dat_cho'] : 0;

    // Xử lý tiền tệ
    $tra_truoc = floatval(str_replace('.', '', $_POST['tra_truoc']));
    $gia_ban_list = $_POST['gia_ban'];
    // Nếu bán lẻ, $gia_ban_list có thể là mảng từ input hidden hoặc gia_ban_that
    // Để an toàn cho cả bán lẻ và bán lô, mình dùng logic này:
    if (isset($_POST['ban_le']) && $_POST['ban_le'] == '1') {
        $ma_bo_le = (int)$_POST['ma_bo_hidden']; // Cần thêm input hidden này ở form
        $gia_ban_clean = [$ma_bo_le => floatval($_POST['gia_ban_that'])];
    } else {
        $gia_ban_clean = [];
        foreach ($gia_ban_list as $id_bo => $gia_str) {
            $gia_ban_clean[$id_bo] = floatval(str_replace('.', '', $gia_str));
        }
    }

    $tong_tien = array_sum($gia_ban_clean);
    $status = ($tra_truoc >= $tong_tien) ? 'da_thanh_toan' : 'con_no';

    // 2. Lưu bảng phieu_xuat_ban (Thêm cột ngay_ban_giao)
    $stmt = $conn->prepare("INSERT INTO phieu_xuat_ban (ma_khach_hang, ngay_ban, ngay_ban_giao, tong_tien, so_tien_tra_truoc, trang_thai_thanh_toan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdds", $ma_kh, $ngay, $ngay_ban_giao, $tong_tien, $tra_truoc, $status);
    $stmt->execute();

    $id_phieu_vua_tao = $conn->insert_id;

    // 3. Cập nhật chi tiết và trạng thái bò
    foreach ($gia_ban_clean as $id_bo => $gia) {
        // Lấy cân nặng hiện tại của bò để "chốt" vào hóa đơn
        $res_weight = mysqli_query($conn, "SELECT can_nang_hien_tai FROM danh_sach_bo WHERE id = $id_bo");
        $row_weight = mysqli_fetch_assoc($res_weight);

        // Nếu lấy được cân nặng thì dùng, không thì mặc định là 0 (để tránh bị NULL)
        $kg = isset($row_weight['can_nang_hien_tai']) ? $row_weight['can_nang_hien_tai'] : 0;

        // Lưu chi tiết hóa đơn (Phải đảm bảo biến $kg có giá trị)
        $st_ct = $conn->prepare("INSERT INTO chi_tiet_phieu_xuat (ma_phieu_xuat, ma_bo, can_nang_ban, gia_ban_con_nay) VALUES (?, ?, ?, ?)");
        $st_ct->bind_param("iidd", $id_phieu_vua_tao, $id_bo, $kg, $gia);
        $st_ct->execute();

        // Cập nhật trạng thái bò
        $st_up = $conn->prepare("UPDATE danh_sach_bo SET trang_thai = 'da_ban', gia_ban_ra = ? WHERE id = ?");
        $st_up->bind_param("di", $gia, $id_bo);
        $st_up->execute();
    }

    mysqli_commit($conn);

    // Sau khi bán xong, hỏi xem có muốn in hóa đơn luôn không
    echo "<script>
            if(confirm('Lập hóa đơn thành công! Bạn có muốn in hóa đơn ngay không?')) {
                window.location='in-hoa-don.php?id=$id_phieu_vua_tao';
            } else {
                window.location='danh-sach-hoa-don.php';
            }
          </script>";
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Lỗi hệ thống: " . $e->getMessage();
}
