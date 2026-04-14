<?php
include '../../config/db.php';

// Bật báo lỗi để kiểm tra nếu có vấn đề
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Lấy thông tin con bò trước khi xóa
    $sql_info = "SELECT ma_phieu_nhap, gia_mua_vao, trang_thai FROM danh_sach_bo WHERE id = $id";
    $res_info = mysqli_query($conn, $sql_info);
    $bo = mysqli_fetch_assoc($res_info);

    if ($bo) {
        // KHÔNG cho xóa nếu bò đã bán (để tránh lệch sổ sách hóa đơn bán)
        if ($bo['trang_thai'] == 'da_ban') {
            echo "<script>alert('Bò này đã bán, không thể xóa để giữ lịch sử hóa đơn!'); window.location='../pages_bo/danh-sach-bo.php';</script>";
            exit();
        }

        $ma_phieu = $bo['ma_phieu_nhap'];
        $gia_mua = $bo['gia_mua_vao'];

        // Dùng Transaction để đảm bảo: Hoặc cập nhật hết, hoặc không làm gì cả
        mysqli_begin_transaction($conn);
        try {

            // Giảm số lượng đi 1 và trừ số tiền tương ứng của con bò đó
            $sql_update_phieu = "UPDATE phieu_thu_mua 
                                 SET so_luong = so_luong - 1, 
                                     tong_tien_phieu = tong_tien_phieu - $gia_mua 
                                 WHERE id = $ma_phieu";
            mysqli_query($conn, $sql_update_phieu);

            // 3. Xóa các dữ liệu liên quan khác (Ví dụ: Chi phí chăm sóc của con bò này)
            // Nếu Thanh có bảng chi_phi_cham_soc thì nên xóa sạch để tránh rác DB
            mysqli_query($conn, "DELETE FROM chi_phi_cham_soc WHERE ma_bo = $id");

            // 4. Tiến hành xóa con bò khỏi danh sách
            $sql_delete = "DELETE FROM danh_sach_bo WHERE id = $id";
            mysqli_query($conn, $sql_delete);

            mysqli_commit($conn);
            
            // Chuyển hướng về trang danh sách bò (nhớ thêm ../ vì đang ở trong folder modules)
            header("Location: ../pages_bo/danh-sach-bo.php?msg=xoa_thanh_cong");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Lỗi hệ thống khi xóa: " . $e->getMessage();
        }
    } else {
        header("Location: ../pages_bo/danh-sach-bo.php?msg=khong_tim_thay");
    }
}