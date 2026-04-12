<?php 
include '../config/db.php'; 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Truy vấn: Join bảng để lấy tên hộ dân, sắp xếp ngày mới nhất lên đầu
$sql = "SELECT p.*, h.ten_ho_dan 
        FROM phieu_thu_mua p 
        JOIN ho_dan h ON p.ma_ho_dan = h.id 
        ORDER BY p.ngay_mua DESC, p.id DESC";
$result = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="bi bi-journal-text me-2"></i>Danh Sách Phiếu Thu Mua</h3>
            <a href="them-phieu-nhap.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Lập phiếu mới
            </a>
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
                                    <th class="ps-4">Ngày nhập</th>
                                    <th>Hộ dân</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Tiền xe</th>
                                    <th class="text-end text-warning">Tổng thanh toán</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-secondary">
                                                <?= date('d/m/Y', strtotime($row['ngay_mua'])) ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= $row['ten_ho_dan'] ?></div>
                                                <small class="text-muted">ID: #<?= $row['id'] ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark px-3 rounded-pill"><?= $row['so_luong'] ?> con</span>
                                            </td>
                                            <td class="text-end text-muted">
                                                <?= number_format($row['phi_van_chuyen']) ?> đ
                                            </td>
                                            <td class="text-end fw-bold text-danger fs-5">
                                                <?= number_format($row['tong_tien_phieu']) ?> đ
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group shadow-sm" role="group">
                                                    <a href="sua-phieu-nhap.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Chỉnh sửa phiếu">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete(<?= $row['id'] ?>, 'phieu_nhap')" 
                                                            title="Xóa phiếu">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted italic">
                                            <i class="bi bi-database-exclamation fs-1 d-block mb-2 opacity-50"></i>
                                            Chưa có dữ liệu phiếu nhập nào.
                                        </td>
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

<script src="../../assets/js/admin-scripts.js"></script>
<?php include 'includes/footer.php'; ?>