<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// SQL: Lấy danh sách hóa đơn và tên khách hàng
$sql = "SELECT p.*, k.ten_khach_hang, k.loai_khach 
        FROM phieu_xuat_ban p 
        JOIN khach_hang k ON p.ma_khach_hang = k.id 
        ORDER BY p.id DESC";
$result = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0"><i class="bi bi-receipt me-2"></i>Lịch Sử Hóa Đơn Bán Bò</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày bán</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th class="text-end">Đã trả</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): 
                                    $status_class = ($row['trang_thai_thanh_toan'] == 'da_thanh_toan') ? 'bg-success' : 'bg-warning text-dark';
                                    $status_text = ($row['trang_thai_thanh_toan'] == 'da_thanh_toan') ? 'Đã thanh toán' : 'Còn nợ';
                                ?>
                                <tr>
                                    <td class="ps-3 fw-bold">#HD-<?= $row['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $row['ten_khach_hang'] ?></div>
                                        <small class="badge bg-light text-dark border"><?= $row['loai_khach'] ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['ngay_ban'])) ?></td>
                                    <td class="text-end fw-bold text-primary"><?= number_format($row['tong_tien']) ?> đ</td>
                                    <td class="text-end text-success"><?= number_format($row['so_tien_tra_truoc']) ?> đ</td>
                                    <td class="text-center">
                                        <span class="badge <?= $status_class ?> rounded-pill px-3"><?= $status_text ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="chi-tiet-hoa-don.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                            <a href="in-hoa-don.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-printer"></i> In
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include './includes/footer.php'; ?>