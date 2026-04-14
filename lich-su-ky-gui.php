<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$res_kh = mysqli_query($conn, "SELECT id FROM khach_hang WHERE user_id = $user_id");
$row_kh = mysqli_fetch_assoc($res_kh);
$ma_kh = $row_kh['id'];

// Lấy danh sách ký gửi
$sql = "SELECT kg.*, g.ten_giong 
        FROM ky_gui_bo kg 
        JOIN giong_bo g ON kg.ma_giong = g.id 
        WHERE kg.ma_khach_hang = $ma_kh 
        ORDER BY kg.id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <h3 class="fw-bold text-success mb-4">DANH SÁCH BÒ ĐÃ KÝ GỬI</h3>
    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Giống bò</th>
                    <th>Cân nặng</th>
                    <th>Ngày gửi</th>
                    <th>Số điện thoại</th> <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="assets/uploads/<?= $row['hinh_anh'] ?>" width="60" class="rounded border"></td>
                    <td class="fw-bold">Bò <?= $row['ten_giong'] ?></td>
                    <td><?= $row['can_nang_ky_gui'] ?> kg</td>
                    <td><?= date('d/m/Y', strtotime($row['ngay_gui'])) ?></td>
                    
                    <td class="text-primary fw-bold"><?= $row['so_dien_thoai_lien_he'] ?></td>
                    
                    <td>
                        <?php 
                        if($row['trang_thai'] == 'dang_cho_duyet' || $row['trang_thai'] == NULL) 
                            echo '<span class="badge bg-warning text-dark px-3 rounded-pill">Chờ duyệt</span>';
                        elseif($row['trang_thai'] == 'da_nhan_bo') 
                            echo '<span class="badge bg-success px-3 rounded-pill">Đã nhận bò</span>';
                        else 
                            echo '<span class="badge bg-danger px-3 rounded-pill">Từ chối</span>';
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted small italic">Bạn chưa có đơn ký gửi nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>