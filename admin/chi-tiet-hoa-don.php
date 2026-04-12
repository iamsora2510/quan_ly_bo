<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. Lấy mã hóa đơn từ URL
$id_phieu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn thông tin chung của hóa đơn (Bảng Cha)
$sql_phieu = "SELECT p.*, k.ten_khach_hang, k.so_dien_thoai, k.dia_chi 
              FROM phieu_xuat_ban p 
              JOIN khach_hang k ON p.ma_khach_hang = k.id 
              WHERE p.id = $id_phieu";
$res_phieu = mysqli_query($conn, $sql_phieu);
$phieu = mysqli_fetch_assoc($res_phieu);

if (!$phieu) {
    echo "<script>alert('Hóa đơn không tồn tại!'); window.location='danh-sach-hoa-don.php';</script>";
    exit();
}

// 3. Truy vấn danh sách bò trong hóa đơn này (Bảng Con)
$sql_ct = "SELECT ct.*, b.ma_so_tai, g.ten_giong,b.can_nang_hien_tai
           FROM chi_tiet_phieu_xuat ct 
           JOIN danh_sach_bo b ON ct.ma_bo = b.id 
           JOIN giong_bo g ON b.ma_giong = g.id 
           WHERE ct.ma_phieu_xuat = $id_phieu";
$res_ct = mysqli_query($conn, $sql_ct);
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">CHI TIẾT HÓA ĐƠN #HD-<?= $phieu['id'] ?></h5>
                <a href="danh-sach-hoa-don.php" class="btn btn-sm btn-secondary">Quay lại danh sách</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted text-uppercase small fw-bold">Khách hàng mua</p>
                        <h5 class="fw-bold"><?= $phieu['ten_khach_hang'] ?></h5>
                        <p class="mb-0 text-muted"><i class="bi bi-telephone me-1"></i> <?= $phieu['so_dien_thoai'] ?></p>
                        <p class="mb-0 text-muted"><i class="bi bi-geo-alt me-1"></i> <?= $phieu['dia_chi'] ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1 text-muted text-uppercase small fw-bold">Ngày lập hóa đơn</p>
                        <h5><?= date('d/m/Y', strtotime($phieu['ngay_ban'])) ?></h5>
                        <span class="badge <?= $phieu['trang_thai_thanh_toan'] == 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark' ?> px-3">
                            <?= $phieu['trang_thai_thanh_toan'] == 'da_thanh_toan' ? 'Đã thanh toán' : 'Còn nợ' ?>
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">STT</th>
                                <th>Số tai bò</th>
                                <th>Giống bò</th>
                                <th class="text-center">Cân nặng (kg)</th>
                                <th class="text-end">Đơn giá bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1;
                            while($row = mysqli_fetch_assoc($res_ct)): 
                            ?>
                            <tr>
                                <td class="text-center"><?= $stt++ ?></td>
                                <td class="fw-bold"><?= $row['ma_so_tai'] ?></td>
                                <td><?= $row['ten_giong'] ?></td>
                                <td class="text-center"><?= $row['can_nang_ban'] ?> kg</td>
                                <td class="text-end fw-bold"><?= number_format($row['gia_ban_con_nay']) ?> đ</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">TỔNG CỘNG ĐƠN HÀNG:</td>
                                <td class="text-end fw-bold text-primary fs-5"><?= number_format($phieu['tong_tien']) ?> đ</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Khách đã trả trước:</td>
                                <td class="text-end text-success"><?= number_format($phieu['so_tien_tra_truoc']) ?> đ</td>
                            </tr>
                            <?php if($phieu['tong_tien'] > $phieu['so_tien_tra_truoc']): ?>
                            <tr class="table-warning">
                                <td colspan="4" class="text-end fw-bold text-danger">SỐ TIỀN CÒN NỢ:</td>
                                <td class="text-end fw-bold text-danger"><?= number_format($phieu['tong_tien'] - $phieu['so_tien_tra_truoc']) ?> đ</td>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-success shadow-sm">
                            <div class="card-header bg-success text-white py-1">
                                <small class="fw-bold"><i class="bi bi-clock-history me-1"></i> LỊCH SỬ TRẢ TIỀN</small>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-2">Ngày thu</th>
                                            <th class="text-end pe-2">Số tiền thu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ls_res = mysqli_query($conn, "SELECT * FROM lich_su_thu_no WHERE ma_phieu_xuat = $id_phieu ORDER BY ngay_thu DESC");
                                        if(mysqli_num_rows($ls_res) > 0):
                                            while($ls = mysqli_fetch_assoc($ls_res)):
                                        ?>
                                        <tr>
                                            <td class="ps-2 small"><?= date('d/m/Y H:i', strtotime($ls['ngay_thu'])) ?></td>
                                            <td class="text-end pe-2 fw-bold text-success"><?= number_format($ls['so_tien_thu']) ?> đ</td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-muted small py-2">Chưa có lịch sử thu thêm.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!empty($phieu['ghi_chu'])): ?>
                <div class="alert alert-light border mt-3">
                    <strong>Ghi chú:</strong> <?= $phieu['ghi_chu'] ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <button onclick="window.print()" class="btn btn-outline-dark px-4 shadow-sm">
                    <i class="bi bi-printer me-2"></i> In hóa đơn này
                </button>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>