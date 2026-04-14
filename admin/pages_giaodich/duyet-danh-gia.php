<?php
include '../config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Xử lý khi Admin bấm Duyệt hoặc Xóa
if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'duyet') {
        mysqli_query($conn, "UPDATE danh_gia SET trang_thai = 1 WHERE id = $id");
    } elseif ($_GET['action'] == 'xoa') {
        mysqli_query($conn, "DELETE FROM danh_gia WHERE id = $id");
    }
    echo "<script>window.location='duyet-danh-gia.php';</script>";
}

// Sửa lại câu SQL nối qua cột user_id
$sql = "SELECT dg.*, kh.ten_khach_hang, b.ma_so_tai 
        FROM danh_gia dg 
        LEFT JOIN khach_hang kh ON dg.ma_khach_hang = kh.user_id 
        LEFT JOIN danh_sach_bo b ON dg.ma_bo = b.id 
        ORDER BY dg.trang_thai ASC, dg.ngay_danh_gia DESC";

$result = mysqli_query($conn, $sql);

?>

<main class="app-main">
    <div class="container-fluid pt-4">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-chat-left-heart me-2"></i>Quản Lý Phản Hồi Khách Hàng</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Khách hàng</th>
                            <th>Mã bò</th>
                            <th>Nội dung</th>
                            <th class="text-center">Số sao</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= $row['ten_khach_hang'] ?></td>
                            <td><span class="badge bg-secondary"><?= $row['ma_so_tai'] ?? 'N/A' ?></span></td>
                            <td style="max-width: 300px;"><?= $row['noi_dung'] ?></td>
                            <td class="text-center text-warning">
                                <?php for($i=1; $i<=$row['so_sao']; $i++) echo "★"; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['trang_thai'] == 0): ?>
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-success text-white">Đã hiện trang chủ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <?php if($row['trang_thai'] == 0): ?>
                                    <a href="?action=duyet&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success rounded-pill">Duyệt</a>
                                <?php endif; ?>
                                <a href="?action=xoa&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Xóa vĩnh viễn phản hồi này?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>