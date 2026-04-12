<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. LẤY THAM SỐ LỌC (Quý và Năm)
$selected_year = $_GET['year'] ?? date('Y');
$selected_quarter = $_GET['quarter'] ?? '';

// Xây dựng điều kiện WHERE cho thời gian
$time_condition = " WHERE YEAR(p.ngay_ban) = $selected_year";
if ($selected_quarter != '') {
    if ($selected_quarter == '1') $time_condition .= " AND MONTH(p.ngay_ban) IN (1,2,3)";
    if ($selected_quarter == '2') $time_condition .= " AND MONTH(p.ngay_ban) IN (4,5,6)";
    if ($selected_quarter == '3') $time_condition .= " AND MONTH(p.ngay_ban) IN (7,8,9)";
    if ($selected_quarter == '4') $time_condition .= " AND MONTH(p.ngay_ban) IN (10,11,12)";
}

// 2. THỐNG KÊ TỔNG QUAN (Theo bộ lọc)
$doanh_thu_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(tong_tien) as dt, SUM(tong_tien - so_tien_tra_truoc) as no FROM phieu_xuat_ban p $time_condition"));
$tong_doanh_thu = $doanh_thu_res['dt'] ?? 0;
$tong_no = $doanh_thu_res['no'] ?? 0;

// Thống kê tồn kho (Cái này không theo thời gian bán, mà là hiện tại)
$tong_bo_kho = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as sl FROM danh_sach_bo WHERE trang_thai = 'dang_nuoi'"))['sl'];

// 3. TÍNH LỢI NHUẬN THUẦN (Theo bộ lọc)
$sql_ln = "SELECT SUM(ctx.gia_ban_con_nay) as total_ban, SUM(b.gia_mua_vao) as total_mua
           FROM chi_tiet_phieu_xuat ctx
           JOIN phieu_xuat_ban p ON ctx.ma_phieu_xuat = p.id
           JOIN danh_sach_bo b ON ctx.ma_bo = b.id
           $time_condition";
$res_ln = mysqli_fetch_assoc(mysqli_query($conn, $sql_ln));

// Tổng chi phí nuôi của những con bò đã bán trong khoảng thời gian này
$sql_phi = "SELECT SUM(cp.so_tien) as total_phi 
            FROM chi_phi_cham_soc cp 
            WHERE cp.ma_bo IN (SELECT ctx.ma_bo FROM chi_tiet_phieu_xuat ctx JOIN phieu_xuat_ban p ON ctx.ma_phieu_xuat = p.id $time_condition)";
$res_phi = mysqli_fetch_assoc(mysqli_query($conn, $sql_phi));

$loi_nhuan_thuan = ($res_ln['total_ban'] ?? 0) - (($res_ln['total_mua'] ?? 0) + ($res_phi['total_phi'] ?? 0));
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>QUẢN LÝ THỐNG KÊ</h3>
            
            <form method="GET" class="d-flex gap-2">
                <select name="quarter" class="form-select form-select-sm" style="width: 120px;">
                    <option value="">Cả năm</option>
                    <option value="1" <?= $selected_quarter == '1' ? 'selected' : '' ?>>Quý 1</option>
                    <option value="2" <?= $selected_quarter == '2' ? 'selected' : '' ?>>Quý 2</option>
                    <option value="3" <?= $selected_quarter == '3' ? 'selected' : '' ?>>Quý 3</option>
                    <option value="4" <?= $selected_quarter == '4' ? 'selected' : '' ?>>Quý 4</option>
                </select>
                <select name="year" class="form-select form-select-sm" style="width: 100px;">
                    <?php for($y = 2024; $y <= 2026; $y++) echo "<option value='$y' ".($selected_year == $y ? 'selected' : '').">$y</option>"; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Xem</button>
            </form>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                    <div class="card-body">
                        <small>DOANH THU</small>
                        <h4 class="fw-bold mb-0"><?= number_format($tong_doanh_thu) ?> đ</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white mb-4">
                    <div class="card-body">
                        <small>LỢI NHUẬN THUẦN</small>
                        <h4 class="fw-bold mb-0"><?= number_format($loi_nhuan_thuan) ?> đ</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-danger text-white mb-4">
                    <div class="card-body">
                        <small>NỢ PHẢI THU</small>
                        <h4 class="fw-bold mb-0"><?= number_format($tong_no) ?> đ</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-dark">
                <div class="card border-0 shadow-sm bg-warning mb-4">
                    <div class="card-body">
                        <small>BÒ ĐANG NUÔI (TỒN KHO)</small>
                        <h4 class="fw-bold mb-0"><?= $tong_bo_kho ?> Con</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold">
                Báo cáo chi tiết lợi nhuận (Giá bán - Giá vốn - CP Nuôi)
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th class="text-start ps-3">Mã bò</th>
                            <th>Giá mua (Vốn gốc)</th>
                            <th>Chi phí nuôi</th>
                            <th>Giá bán thực tế</th>
                            <th class="text-end pe-3">Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_dt = "SELECT b.ma_so_tai, b.gia_mua_vao, ctx.gia_ban_con_nay,
                                   (SELECT SUM(so_tien) FROM chi_phi_cham_soc WHERE ma_bo = b.id) as cp_nuoi
                                   FROM chi_tiet_phieu_xuat ctx
                                   JOIN phieu_xuat_ban p ON ctx.ma_phieu_xuat = p.id
                                   JOIN danh_sach_bo b ON ctx.ma_bo = b.id
                                   $time_condition";
                        $res_dt = mysqli_query($conn, $sql_dt);
                        while($row = mysqli_fetch_assoc($res_dt)):
                            $cp_nuoi = $row['cp_nuoi'] ?? 0;
                            $lai = $row['gia_ban_con_nay'] - ($row['gia_mua_vao'] + $cp_nuoi);
                        ?>
                        <tr class="text-center">
                            <td class="text-start ps-3 fw-bold"><?= $row['ma_so_tai'] ?></td>
                            <td><?= number_format($row['gia_mua_vao']) ?></td>
                            <td><?= number_format($cp_nuoi) ?></td>
                            <td class="fw-bold text-primary"><?= number_format($row['gia_ban_con_nay']) ?></td>
                            <td class="text-end pe-3 fw-bold <?= $lai >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($lai) ?> đ
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>