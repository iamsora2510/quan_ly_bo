<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. LẤY THAM SỐ LỌC (Quý và Năm)
$selected_year = $_GET['year'] ?? date('Y');
$selected_quarter = $_GET['quarter'] ?? '';

$time_condition = " WHERE YEAR(p.ngay_ban) = $selected_year";
if ($selected_quarter != '') {
    $months = ['1' => '(1,2,3)', '2' => '(4,5,6)', '3' => '(7,8,9)', '4' => '(10,11,12)'];
    $time_condition .= " AND MONTH(p.ngay_ban) IN " . $months[$selected_quarter];
}

// 2. TỔNG DOANH THU & NỢ (Dựa trên phiếu xuất)
$sql_tong_quan = "SELECT SUM(tong_tien) as dt, SUM(tong_tien - so_tien_tra_truoc) as no FROM phieu_xuat_ban p $time_condition";
$doanh_thu_res = mysqli_fetch_assoc(mysqli_query($conn, $sql_tong_quan));
$tong_doanh_thu = $doanh_thu_res['dt'] ?? 0;
$tong_no = $doanh_thu_res['no'] ?? 0;

// 3. THỐNG KÊ TỒN KHO HIỆN TẠI
$res_kho = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as tong_sl, SUM(CASE WHEN gia_mua_vao > 0 THEN 1 ELSE 0 END) as sl_mua_dut, SUM(CASE WHEN gia_mua_vao = 0 THEN 1 ELSE 0 END) as sl_ky_gui FROM danh_sach_bo WHERE trang_thai = 'dang_nuoi'"));

// 4. CHUẨN BỊ TRUY VẤN CHI TIẾT LỢI NHUẬN (Dùng chung cho cả tính tổng và hiển thị bảng)
$sql_all_sold = "SELECT b.ma_so_tai, b.gia_mua_vao, ctx.gia_ban_con_nay, 
                (SELECT SUM(so_tien) FROM chi_phi_cham_soc WHERE ma_bo = b.id) as cp_nuoi 
                FROM chi_tiet_phieu_xuat ctx 
                JOIN phieu_xuat_ban p ON ctx.ma_phieu_xuat = p.id 
                JOIN danh_sach_bo b ON ctx.ma_bo = b.id 
                $time_condition";

// Tính toán Lợi nhuận ròng tổng quát
$res_for_total = mysqli_query($conn, $sql_all_sold);
$tong_loi_nhuan_thuc_te = 0;
while($item = mysqli_fetch_assoc($res_for_total)) {
    $v = $item['gia_mua_vao'];
    $b = $item['gia_ban_con_nay'];
    $c = $item['cp_nuoi'] ?? 0;
    
    if ($v > 0) {
        $tong_loi_nhuan_thuc_te += ($b - ($v + $c)); // Hàng nhà
    } else {
        $tong_loi_nhuan_thuc_te += ($b * 0.2); // Ký gửi lấy 20% hoa hồng
    }
}
?>

<link rel="stylesheet" href="../assets/css/thong-ke.css">

<main class="main-container">
    <div class="container-fluid">
        <div class="content-box mb-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Báo Cáo Tài Chính</h4>
            <form method="GET" class="d-flex gap-3">
                <select name="quarter" class="form-select border-0 bg-light rounded-pill px-4">
                    <option value="">Cả năm</option>
                    <?php for($i=1; $i<=4; $i++) echo "<option value='$i' ".($selected_quarter == $i ? 'selected' : '').">Quý $i</option>"; ?>
                </select>
                <select name="year" class="form-select border-0 bg-light rounded-pill px-4">
                    <?php for($y = 2024; $y <= 2026; $y++) echo "<option value='$y' ".($selected_year == $y ? 'selected' : '').">$y</option>"; ?>
                </select>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Lọc Dữ Liệu</button>
            </form>
        </div>

        <div class="row g-4 mb-5 text-center">
            <div class="col-md-3">
                <div class="stat-card bg-grad-blue shadow">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75 fw-bold">Doanh Thu Bán</small>
                        <h2 class="fw-bold mb-0"><?= number_format($tong_doanh_thu) ?> đ</h2>
                    </div>
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-grad-green shadow">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75 fw-bold">Lợi Nhuận Ròng</small>
                        <h2 class="fw-bold mb-0"><?= number_format($tong_loi_nhuan_thuc_te) ?> đ</h2>
                    </div>
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-grad-red shadow">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75 fw-bold">Nợ Phải Thu</small>
                        <h2 class="fw-bold mb-0"><?= number_format($tong_no) ?> đ</h2>
                    </div>
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-grad-dark shadow">
                    <div class="card-body text-start ps-4">
                        <small class="text-uppercase opacity-75 fw-bold">Tồn Kho Hiện Tại</small>
                        <h2 class="fw-bold mb-0"><?= $res_kho['tong_sl'] ?> Con</h2>
                        <div class="small opacity-75 mt-1">Hàng nhà: <?= $res_kho['sl_mua_dut'] ?> | Ký gửi: <?= $res_kho['sl_ky_gui'] ?></div>
                    </div>
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>

        <div class="content-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-secondary">CHI TIẾT LỢI NHUẬN TRÊN TỪNG CÁ THỂ</h5>
                <span class="text-muted small italic">Dữ liệu theo năm <?= $selected_year ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-center small">
                            <th class="text-start">MÃ SỐ TAI</th>
                            <th>LOẠI HÌNH</th>
                            <th>VỐN GỐC (TRẠI CHI)</th>
                            <th>CHI PHÍ NUÔI</th>
                            <th>GIÁ BÁN THỰC TẾ</th>
                            <th class="text-end">LỢI NHUẬN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Gọi lại kết quả truy vấn cho bảng
                        $res_dt = mysqli_query($conn, $sql_all_sold); 
                        while($row = mysqli_fetch_assoc($res_dt)):
                            $cp = $row['cp_nuoi'] ?? 0;
                            $von = $row['gia_mua_vao'];
                            $gia_ban = $row['gia_ban_con_nay'];
                            
                            if ($von > 0) {
                                $lai = $gia_ban - ($von + $cp);
                                $label = 'Hàng Nhà';
                                $class = 'badge-mua-dut';
                            } else {
                                $lai = $gia_ban * 0.2; // Hoa hồng 20% cho hàng ký gửi
                                $label = 'Hộ dân ký gửi';
                                $class = 'badge-ky-gui';
                            }
                        ?>
                        <tr class="text-center">
                            <td class="text-start fw-bold text-dark"><?= $row['ma_so_tai'] ?></td>
                            <td>
                                <span class="badge-pill-custom <?= $class ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td><?= number_format($von) ?> đ</td>
                            <td><?= number_format($cp) ?> đ</td>
                            <td class="fw-bold text-primary"><?= number_format($gia_ban) ?> đ</td>
                            <td class="text-end fw-bold <?= $lai >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($lai >= 0 ? '+' : '') . number_format($lai) ?> đ
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