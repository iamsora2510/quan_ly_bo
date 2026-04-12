<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. LẤY THÔNG TIN TỪ URL (Nếu đi từ trang Đặt chỗ)
$ma_bo_get = isset($_GET['ma_bo']) ? (int)$_GET['ma_bo'] : 0;
$ma_kh_get = isset($_GET['ma_kh']) ? (int)$_GET['ma_kh'] : 0;
$id_dat_cho = isset($_GET['id_dat_cho']) ? (int)$_GET['id_dat_cho'] : 0;

// Lấy thông tin con bò cụ thể và vốn tích lũy
$sql_bo = "SELECT b.*, g.ten_giong,
          (SELECT SUM(so_tien) FROM chi_phi_cham_soc WHERE ma_bo = b.id) as tong_phi_cs
          FROM danh_sach_bo b 
          JOIN giong_bo g ON b.ma_giong = g.id 
          WHERE b.id = $ma_bo_get";
$res_bo = mysqli_query($conn, $sql_bo);
$r = mysqli_fetch_assoc($res_bo);

// Tính toán vốn ngay từ đầu
$gia_von = ($r['gia_mua_vao'] ?? 0) + ($r['tong_phi_cs'] ?? 0);
?>

<main class="app-main">
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <h3><i class="bi bi-receipt-cutoff text-primary me-2"></i>Lập Hóa Đơn Bán Lẻ & Chốt Nợ</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form action="xu-ly-ban-hang.php" method="POST">
                <input type="hidden" name="id_dat_cho" value="<?= $id_dat_cho ?>">
                <input type="hidden" name="ban_le" value="1">

                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-primary text-white fw-bold">CHI TIẾT CON BÒ XUẤT BÁN</div>
                    <div class="card-body p-0">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Thông tin bò</th>
                                    <th>Phân tích vốn (đ)</th>
                                    <th style="width: 250px;">Giá bán chốt (đ)</th>
                                    <th>Lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($r): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/uploads/<?= $r['hinh_anh'] ?: 'no-image.png' ?>" class="rounded me-2" style="width: 60px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <strong class="text-primary">#<?= $r['ma_so_tai'] ?></strong><br>
                                                    <small class="text-muted"><?= $r['ten_giong'] ?> - <?= $r['can_nang_hien_tai'] ?>kg</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="gia_ban[<?= $r['id'] ?>]" id="id_bo_an" value="0">
                                        </td>
                                        <td>
                                            <small>Giá nhập: <?= number_format($r['gia_mua_vao']) ?></small><br>
                                            <small>Nuôi: <?= number_format($r['tong_phi_cs'] ?? 0) ?></small><br>
                                            <strong class="text-danger">Tổng vốn: <?= number_format($gia_von) ?></strong>
                                        </td>
                                        <td>
                                            <input type="number" id="input_gia_ban" name="gia_ban_that"
                                                class="form-control fw-bold text-success fs-5"
                                                oninput="capNhatGiaBan(<?= $gia_von ?>)" required>
                                        </td>
                                        <td class="text-center">
                                            <strong id="loi_nhuan_hien_thi" class="fs-5">0</strong> đ
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center p-4">Vui lòng chọn bò từ danh sách hoặc phiếu đặt chỗ!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow border-top border-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Khách hàng mua:</label>
                                <select name="ma_kh" class="form-select border-primary" required>
                                    <option value="">-- Chọn khách hàng --</option>
                                    <?php
                                    // Lấy từ bảng khach_hang để ID luôn chính xác cho Hóa đơn & Công nợ
                                    $khs = mysqli_query($conn, "SELECT id, ten_khach_hang, so_dien_thoai, loai_khach FROM khach_hang");
                                    while ($k = mysqli_fetch_assoc($khs)) {
                                        // Kiểm tra xem ID này có khớp với ID truyền từ URL (trang Đặt chỗ) không
                                        // Lưu ý quan trọng: ma_kh_get bây giờ phải là ID của bảng khach_hang
                                        $sel = ($k['id'] == $ma_kh_get) ? 'selected' : '';

                                        echo "<option value='{$k['id']}' $sel>
                [{$k['loai_khach']}] {$k['ten_khach_hang']} - {$k['so_dien_thoai']}
              </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">KHÁCH TRẢ TRƯỚC (đ):</label>
                                <input type="number" name="tra_truoc" id="tra_truoc" class="form-control border-primary fs-5 fw-bold" oninput="tinhToanNo()" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày lập hóa đơn:</label>
                                <input type="date" name="ngay_ban" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Ngày bàn giao bò (dự kiến):</label>
                                <input type="date" name="ngay_ban_giao" class="form-control border-success" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-12">
                                <div id="vung_no" class="alert alert-warning py-2 mb-0" style="display:none;">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Còn nợ lại: <strong id="so_tien_no">0</strong> đ (Sẽ tính vào công nợ khách hàng)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-white border-0 p-3">
                        <a href="quan-ly-dat-cho.php" class="btn btn-light px-4 me-2">Hủy bỏ</a>
                        <button type="submit" name="btnXuatHD" class="btn btn-primary btn-lg px-5 fw-bold shadow">HOÀN TẤT & XUẤT PHIẾU</button>
                    </div>
                </div>
                <input type="hidden" name="id_dat_cho" value="<?= isset($_GET['id_dat_cho']) ? $_GET['id_dat_cho'] : 0 ?>">
            </form>
        </div>
    </div>
</main>

<script>
    function capNhatGiaBan(giaVon) {
        let giaBan = parseFloat(document.getElementById('input_gia_ban').value) || 0;

        // Đồng bộ giá vào hidden input để file xu-ly-ban-hang nhận được
        document.getElementById('id_bo_an').value = giaBan;

        // Tính lợi nhuận
        let loiNhuan = giaBan - giaVon;
        let hienThi = document.getElementById('loi_nhuan_hien_thi');
        hienThi.innerText = loiNhuan.toLocaleString();
        hienThi.style.color = (loiNhuan >= 0) ? 'green' : 'red';

        tinhToanNo();
    }

    function tinhToanNo() {
        let giaBan = parseFloat(document.getElementById('input_gia_ban').value) || 0;
        let traTruoc = parseFloat(document.getElementById('tra_truoc').value) || 0;
        let nợ = giaBan - traTruoc;

        if (nợ > 0) {
            document.getElementById('vung_no').style.display = 'block';
            document.getElementById('so_tien_no').innerText = nợ.toLocaleString();
        } else {
            document.getElementById('vung_no').style.display = 'none';
        }
    }
</script>

<?php include './includes/footer.php'; ?>