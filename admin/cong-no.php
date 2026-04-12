<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// SQL: Lấy các hóa đơn còn nợ và tính số tiền còn lại phải thu
$sql = "SELECT p.*, k.ten_khach_hang, k.so_dien_thoai,
        (p.tong_tien - p.so_tien_tra_truoc) as so_tien_no
        FROM phieu_xuat_ban p
        JOIN khach_hang k ON p.ma_khach_hang = k.id
        WHERE p.trang_thai_thanh_toan = 'con_no'
        ORDER BY p.ngay_ban ASC"; // Nợ cũ hiện lên trước để đòi
$result = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0 text-danger"><i class="bi bi-exclamation-octagon me-2"></i>Quản lý Nợ Phải Thu</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-warning">
                                <tr>
                                    <th class="ps-3">Hóa đơn</th>
                                    <th>Khách hàng</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th class="text-end">Đã trả</th>
                                    <th class="text-end text-danger">Còn nợ</th>
                                    <th class="text-center">Ngày bán</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold">#HD-<?= $row['id'] ?></td>
                                            <td>
                                                <div class="fw-bold"><?= $row['ten_khach_hang'] ?></div>
                                                <small class="text-muted"><?= $row['so_dien_thoai'] ?></small>
                                            </td>
                                            <td class="text-end"><?= number_format($row['tong_tien']) ?> đ</td>
                                            <td class="text-end text-success"><?= number_format($row['so_tien_tra_truoc']) ?> đ</td>
                                            <td class="text-end fw-bold text-danger"><?= number_format($row['so_tien_no']) ?> đ</td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['ngay_ban'])) ?></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="chi-tiet-hoa-don.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết đơn này">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="moModalThuNo(<?= $row['id'] ?>, '<?= $row['ten_khach_hang'] ?>', <?= $row['so_tien_no'] ?>)">
                                                        <i class="bi bi-cash-coin"></i> Thu nợ
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Tuyệt vời! Hiện tại không có công nợ nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalThuNo" tabindex="-1">
    <div class="modal-dialog">
        <form action="xu-ly-thu-no.php" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Cập nhật thu tiền nợ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_phieu" id="modal_id_phieu">
                <p>Khách hàng: <strong id="modal_ten_kh"></strong></p>
                <p>Số nợ hiện tại: <strong class="text-danger" id="modal_so_no"></strong> đ</p>
                <div class="mb-3">
                    <label class="fw-bold">Số tiền thu thêm hôm nay:</label>
                    <input type="number" name="so_tien_thu" class="form-control border-primary fs-5" required>
                    <small class="text-muted">Nhập đúng số tiền khách vừa trả thêm.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận thu tiền</button>
            </div>
        </form>
    </div>
</div>

<script>
    function moModalThuNo(id, ten, no) {
        document.getElementById('modal_id_phieu').value = id;
        document.getElementById('modal_ten_kh').innerText = ten;
        document.getElementById('modal_so_no').innerText = new Intl.NumberFormat('vi-VN').format(no);
        new bootstrap.Modal(document.getElementById('modalThuNo')).show();
    }
</script>

<?php include './includes/footer.php'; ?>