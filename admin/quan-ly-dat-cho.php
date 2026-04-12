<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. Xử lý cập nhật trạng thái khi Admin bấm nút
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action == 'confirm') ? 'da_lien_he' : 'huy';

    $sql_update = "UPDATE dat_cho_bo SET trang_thai = '$new_status' WHERE id = $id";
    mysqli_query($conn, $sql_update);
    echo "<script>window.location='quan-ly-dat-cho.php';</script>";
}

// 2. Truy vấn danh sách đặt chỗ (Bắc cầu qua bảng khach_hang để lấy đúng ID giao dịch)
$sql = "SELECT d.*, k.ten_khach_hang AS fullname, k.so_dien_thoai AS sdt, k.id AS id_kh_chuan, b.ma_so_tai, g.ten_giong 
        FROM dat_cho_bo d
        JOIN khach_hang k ON d.ma_khach_hang = k.user_id
        JOIN danh_sach_bo b ON d.ma_bo = b.id
        JOIN giong_bo g ON b.ma_giong = g.id
        ORDER BY d.trang_thai ASC, d.ngay_dat DESC";
$res = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <h3 class="fw-bold text-primary"><i class="bi bi-telephone-inbound me-2"></i>Quản Lý Yêu Cầu Giữ Chỗ</h3>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Khách hàng</th>
                                    <th>Thông tin bò</th>
                                    <th>Ngày đặt</th>
                                    <th>Ghi chú khách</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold"><?= $row['fullname'] ?></div>
                                            <div class="small text-success"><i class="bi bi-phone"></i> <?= $row['sdt'] ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">#<?= $row['ma_so_tai'] ?></span>
                                            Bò <?= $row['ten_giong'] ?>
                                        </td>
                                        <td class="small"><?= date('d/m/Y H:i', strtotime($row['ngay_dat'])) ?></td>
                                        <td class="small text-muted" style="max-width: 200px;"><?= $row['ghi_chu_khach'] ?></td>
                                        <td>
                                            <?php if ($row['trang_thai'] == 'cho_xac_nhan'): ?>
                                                <span class="badge bg-warning text-dark">Đang chờ xử lý</span>
                                            <?php elseif ($row['trang_thai'] == 'da_lien_he'): ?>
                                                <span class="badge bg-success">Đã liên hệ chốt</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Đã hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php
                                            // Truy vấn kiểm tra trạng thái con bò hiện tại trong DB
                                            $id_bo_check = $row['ma_bo'];
                                            $check_bo = mysqli_query($conn, "SELECT trang_thai FROM danh_sach_bo WHERE id = $id_bo_check");
                                            $bo_info = mysqli_fetch_assoc($check_bo);
                                            $trang_thai_bo = $bo_info['trang_thai'];

                                            if ($trang_thai_bo == 'da_ban'): ?>
                                                <span class="badge bg-secondary px-3 py-2"><i class="bi bi-check2-all"></i> Đã xuất bán</span>
                                            <?php else: ?>
                                                <?php if ($row['trang_thai'] == 'cho_xac_nhan'): ?>
                                                    <a href="?action=confirm&id=<?= $row['id'] ?>" class="btn btn-sm btn-success shadow-sm">
                                                        <i class="bi bi-check-lg"></i>
                                                    </a>
                                                    <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Hủy yêu cầu này?')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </a>
                                                <?php elseif ($row['trang_thai'] == 'da_lien_he'): ?>
                                                    <a href="lap-hoa-don.php?ma_bo=<?= $row['ma_bo'] ?>&ma_kh=<?= $row['id_kh_chuan'] ?>&id_dat_cho=<?= $row['id'] ?>"
                                                        class="btn btn-sm btn-info text-white">
                                                        Bán bò
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <a href="tel:<?= $row['sdt'] ?>" class="btn btn-sm btn-primary shadow-sm ms-1">
                                                <i class="bi bi-telephone-fill"></i>
                                            </a>
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