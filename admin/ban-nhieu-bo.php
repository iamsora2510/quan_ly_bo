<?php 
include '../config/db.php'; 
include './includes/header.php'; 
include './includes/sidebar.php'; 

$ids = $_POST['selected_bo'] ?? [];
if (empty($ids)) {
    echo "<script>window.location='danh-sach-bo.php';</script>";
    exit();
}

$ids_str = implode(',', array_map('intval', $ids));

// Lấy thông tin bò + Chi phí chăm sóc tích lũy từng con
$sql = "SELECT b.*, g.ten_giong,
        (SELECT SUM(so_tien) FROM chi_phi_cham_soc WHERE ma_bo = b.id) as tong_phi_cs
        FROM danh_sach_bo b 
        JOIN giong_bo g ON b.ma_giong = g.id 
        WHERE b.id IN ($ids_str)";
$res = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3><i class="bi bi-cart-check text-success me-2"></i>Bán Bò Theo Lô & Chốt Công Nợ</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <form action="xu-ly-ban-hang.php" method="POST">
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-success text-white fw-bold">DANH SÁCH BÒ XUẤT BÁN</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Thông tin bò</th>
                                        <th>Phân tích vốn (đ)</th>
                                        <th style="width: 250px;">Giá bán thực tế (đ)</th>
                                        <th>Lợi nhuận dự kiến</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($r = mysqli_fetch_assoc($res)): 
                                        $gia_von = $r['gia_mua_vao'] + ($r['tong_phi_cs'] ?? 0);
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/uploads/<?= $r['hinh_anh'] ?: 'no-image.png' ?>" class="rounded me-2" style="width: 50px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <strong class="text-primary"><?= $r['ma_so_tai'] ?></strong><br>
                                                    <small class="text-muted"><?= $r['ten_giong'] ?> - <?= $r['can_nang_hien_tai'] ?>kg</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="can_nang[<?= $r['id'] ?>]" value="<?= $r['can_nang_hien_tai'] ?>">
                                        </td>
                                        <td>
                                            <small>Nhập: <?= number_format($r['gia_mua_vao']) ?></small><br>
                                            <small>Nuôi: <?= number_format($r['tong_phi_cs'] ?? 0) ?></small><br>
                                            <strong>Vốn: <?= number_format($gia_von) ?></strong>
                                        </td>
                                        <td>
                                            <input type="number" name="gia_ban[<?= $r['id'] ?>]" 
                                                   class="form-control fw-bold text-success gia-ban-input" 
                                                   data-von="<?= $gia_von ?>" 
                                                   oninput="tinhToanLôBò()" required>
                                        </td>
                                        <td class="text-center">
                                            <strong id="loi_nhuan_<?= $r['id'] ?>">0</strong> đ
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot class="table-secondary fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end">TỔNG CỘNG HÓA ĐƠN:</td>
                                        <td class="text-end text-success fs-5" id="tong_tien_bill">0 đ</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow border-top border-primary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Khách hàng mua:</label>
                                        <select name="ma_kh" class="form-select border-primary" required>
                                            <?php 
                                            $khs = mysqli_query($conn, "SELECT * FROM khach_hang");
                                            while($k = mysqli_fetch_assoc($khs)) {
                                                echo "<option value='{$k['id']}'>[{$k['loai_khach']}] {$k['ten_khach_hang']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold text-primary">KHÁCH TRẢ TRƯỚC (đ):</label>
                                        <input type="number" name="tra_truoc" id="tra_truoc" class="form-control border-primary fs-5 fw-bold" oninput="tinhToanLôBò()" value="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Ngày bán:</label>
                                        <input type="date" name="ngay_ban" class="form-control" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <div id="vung_no" class="alert alert-warning py-2 mb-0" style="display:none;">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            Tổng nợ còn lại: <strong id="so_tien_no">0</strong> đ
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end bg-white border-0">
                                <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow">XÁC NHẬN XUẤT HÓA ĐƠN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../assets/js/ban-hang.js"></script>
<?php include './includes/footer.php'; ?>