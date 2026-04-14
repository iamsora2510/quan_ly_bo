<?php
include '../config/db.php';

// 0. XÁC ĐỊNH CHẾ ĐỘ XEM (Tab hiện tại)
$view = $_GET['view'] ?? 'cho_duyet';

// --- 1. XỬ LÝ LOGIC DUYỆT / MUA ĐỨT ---
if (isset($_POST['btn_xac_nhan_duyet'])) {
    $id_kg = (int)$_POST['id_ky_gui'];
    
    // Kiểm tra trạng thái đơn ký gửi (Chống xử lý trùng lặp)
    $check_status = mysqli_query($conn, "SELECT trang_thai FROM ky_gui_bo WHERE id = $id_kg");
    $status_row = mysqli_fetch_assoc($check_status);
    
    if ($status_row['trang_thai'] != 'da_nhan_bo') {
        $ma_so_tai = mysqli_real_escape_string($conn, $_POST['ma_so_tai']);
        $ma_chuong = (int)$_POST['ma_chuong'];
        $hinh_thuc = $_POST['hinh_thuc_tiep_nhan']; 
        $gia_mua = ($hinh_thuc == 'mua_dut') ? (float)$_POST['gia_mua'] : 0;

        $sql_data = "SELECT kg.*, k.ten_khach_hang, k.dia_chi FROM ky_gui_bo kg 
                     JOIN khach_hang k ON kg.ma_khach_hang = k.id WHERE kg.id = $id_kg";
        $res = mysqli_query($conn, $sql_data);
        $data = mysqli_fetch_assoc($res);

        if ($data) {
            $ma_kh_web = $data['ma_khach_hang'];
            $sdt_moi_nhat = $data['so_dien_thoai_lien_he'];

            // A. XỬ LÝ HỘ DÂN (Gắn khách web vào hệ thống quản lý hộ dân nội bộ)
            $check_hd = mysqli_query($conn, "SELECT id FROM ho_dan WHERE ma_khach_hang_web = $ma_kh_web");
            if (mysqli_num_rows($check_hd) > 0) {
                $hd_row = mysqli_fetch_assoc($check_hd);
                $id_ho_dan = $hd_row['id'];
                mysqli_query($conn, "UPDATE ho_dan SET so_dien_thoai = '$sdt_moi_nhat' WHERE id = $id_ho_dan");
            } else {
                $ten_hd = mysqli_real_escape_string($conn, $data['ten_khach_hang']);
                $dc_hd = mysqli_real_escape_string($conn, $data['dia_chi']);
                mysqli_query($conn, "INSERT INTO ho_dan (ten_ho_dan, so_dien_thoai, dia_chi, loai_ho_dan, ma_khach_hang_web) 
                                     VALUES ('$ten_hd', '$sdt_moi_nhat', '$dc_hd', 'Hộ dân Web', $ma_kh_web)");
                $id_ho_dan = mysqli_insert_id($conn);
            }

            // B. LẬP PHIẾU THU MUA (Dành cho Mua đứt - Cập nhật đúng ý Thanh)
            $ma_phieu_vua_tao = 0;
            if ($hinh_thuc == 'mua_dut') {
                // Insert vào bảng phieu_thu_mua các thông số cần thiết
                $sql_phieu = "INSERT INTO phieu_thu_mua (ma_ho_dan, so_luong, ngay_mua, phi_van_chuyen, tong_tien_phieu, ghi_chu) 
                              VALUES ($id_ho_dan, 1, NOW(), 0, $gia_mua, 'Mua lẻ từ đơn ký gửi Web')";
                if(mysqli_query($conn, $sql_phieu)) {
                    $ma_phieu_vua_tao = mysqli_insert_id($conn);
                }
            }

            // C. THÊM BÒ VÀO DANH SÁCH CHÍNH THỨC
            $ma_giong = $data['ma_giong'];
            $can_nang = $data['can_nang_ky_gui'];
            $hinh_anh = $data['hinh_anh'];

            $sql_insert_bo = "INSERT INTO danh_sach_bo (ma_so_tai, hinh_anh, ma_giong, ma_chuong, ma_phieu_nhap, ma_ho_dan, can_nang_nhap, can_nang_hien_tai, gia_mua_vao, trang_thai, ngay_nhap) 
                              VALUES ('$ma_so_tai', '$hinh_anh', $ma_giong, $ma_chuong, $ma_phieu_vua_tao, $id_ho_dan, $can_nang, $can_nang, $gia_mua, 'dang_nuoi', NOW())";

            if (mysqli_query($conn, $sql_insert_bo)) {
                mysqli_query($conn, "UPDATE ky_gui_bo SET trang_thai = 'da_nhan_bo' WHERE id = $id_kg");
                echo "<script>alert('Tiếp nhận và lập phiếu thu mua thành công!'); window.location='duyet-ky-gui.php';</script>";
                exit;
            }
        }
    }
}

// XỬ LÝ TỪ CHỐI
if (isset($_GET['action']) && $_GET['action'] == 'tu_choi') {
    $id_tu_choi = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE ky_gui_bo SET trang_thai = 'tu_choi' WHERE id = $id_tu_choi");
    header("Location: duyet-ky-gui.php?view=cho_duyet");
    exit;
}

// --- 2. LẤY DỮ LIỆU THEO TAB ---
if ($view == 'lich_su') {
    $sql_list = "SELECT kg.*, g.ten_giong, k.ten_khach_hang 
                 FROM ky_gui_bo kg 
                 JOIN giong_bo g ON kg.ma_giong = g.id 
                 JOIN khach_hang k ON kg.ma_khach_hang = k.id 
                 WHERE kg.trang_thai IN ('da_nhan_bo', 'tu_choi') 
                 ORDER BY kg.id DESC";
} else {
    $sql_list = "SELECT kg.*, g.ten_giong, k.ten_khach_hang 
                 FROM ky_gui_bo kg 
                 JOIN giong_bo g ON kg.ma_giong = g.id 
                 JOIN khach_hang k ON kg.ma_khach_hang = k.id 
                 WHERE kg.trang_thai = 'dang_cho_duyet' OR kg.trang_thai IS NULL 
                 ORDER BY kg.id DESC";
}
$list = mysqli_query($conn, $sql_list);
$chuongs = mysqli_query($conn, "SELECT * FROM chuong_nuoi");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h3 class="p-3 fw-bold text-success"><i class="bi bi-patch-check-fill me-2"></i>Quản Lý Ký Gửi</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <ul class="nav nav-tabs border-0 mb-3">
                <li class="nav-item">
                    <a class="nav-link <?= ($view == 'cho_duyet') ? 'active bg-success text-white' : 'text-success' ?> shadow-sm" href="?view=cho_duyet">
                        <i class="bi bi-hourglass-split me-1"></i> Đang chờ duyệt
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link <?= ($view == 'lich_su') ? 'active bg-secondary text-white' : 'text-secondary' ?> shadow-sm" href="?view=lich_su">
                        <i class="bi bi-clock-history me-1"></i> Lịch sử xử lý
                    </a>
                </li>
            </ul>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Khách hàng</th>
                                <th>Hình ảnh</th>
                                <th>Thông tin bò</th>
                                <th>Trạng thái / Quyết định</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= $row['ten_khach_hang'] ?></div>
                                        <a href="tel:<?= $row['so_dien_thoai_lien_he'] ?>" class="text-primary small"><?= $row['so_dien_thoai_lien_he'] ?></a>
                                    </td>
                                    <td><img src="../assets/uploads/<?= $row['hinh_anh'] ?>" width="75" class="rounded shadow-sm"></td>
                                    <td>
                                        <span class="badge bg-primary">Giống: <?= $row['ten_giong'] ?></span><br>
                                        <strong><?= $row['can_nang_ky_gui'] ?> kg</strong>
                                    </td>
                                    <td>
                                        <?php if ($view == 'cho_duyet'): ?>
                                            <button class="btn btn-success btn-sm fw-bold px-3 rounded-pill" onclick="openModal(<?= $row['id'] ?>, '<?= $row['ten_giong'] ?>', <?= $row['can_nang_ky_gui'] ?>, 'ky_gui')">Tiếp nhận</button>
                                            <button class="btn btn-warning btn-sm fw-bold px-3 rounded-pill" onclick="openModal(<?= $row['id'] ?>, '<?= $row['ten_giong'] ?>', <?= $row['can_nang_ky_gui'] ?>, 'mua_dut')">Mua đứt</button>
                                            <a href="?action=tu_choi&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill ms-1" onclick="return confirm('Từ chối đơn này?')">X</a>
                                        <?php else: ?>
                                            <?php if ($row['trang_thai'] == 'da_nhan_bo'): ?>
                                                <span class="badge bg-success py-2 px-3 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Đã tiếp nhận</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger py-2 px-3 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> Đã từ chối</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($list) == 0): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Trống.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalDuyet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content shadow border-0" id="formDuyet">
            <div id="modalHeader" class="modal-header text-white py-3">
                <h5 class="modal-title fw-bold" id="modalTitle">Tiếp nhận bò</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_ky_gui" id="id_ky_gui">
                <input type="hidden" name="hinh_thuc_tiep_nhan" id="hinh_thuc_tiep_nhan">
                <div id="info_bo" class="alert alert-info border-0 text-center fw-bold mb-4"></div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">GÁN MÃ SỐ TAI</label>
                    <input type="text" name="ma_so_tai" class="form-control" required>
                </div>
                <div id="box_gia_mua" class="mb-3" style="display:none;">
                    <label class="form-label small fw-bold text-danger">GIÁ MUA (VNĐ)</label>
                    <input type="number" name="gia_mua" id="input_gia_mua" class="form-control border-danger">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">CHỌN CHUỒNG</label>
                    <select name="ma_chuong" class="form-select" required>
                        <?php mysqli_data_seek($chuongs, 0); while ($c = mysqli_fetch_assoc($chuongs)) echo "<option value='{$c['id']}'>Chuồng: {$c['ten_chuong']}</option>"; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="submit" name="btn_xac_nhan_duyet" id="btnSubmit" class="btn px-5 fw-bold rounded-pill">XÁC NHẬN</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/duyet_ky_gui.js"></script>

<?php include 'includes/footer.php'; ?>