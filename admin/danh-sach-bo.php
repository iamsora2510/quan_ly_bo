<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// 1. LẤY GIÁ TRỊ LỌC TỪ URL
$f_chuong = $_GET['chuong'] ?? '';
$f_ho = $_GET['ho'] ?? '';
$f_giong = $_GET['giong'] ?? '';

// 2. XÂY DỰNG SQL NÂNG CẤP CÓ BỘ LỌC
$sql = "SELECT b.*, g.ten_giong, c.ten_chuong, p.ngay_mua, h.ten_ho_dan 
        FROM danh_sach_bo b
        INNER JOIN giong_bo g ON b.ma_giong = g.id
        INNER JOIN chuong_nuoi c ON b.ma_chuong = c.id
        LEFT JOIN phieu_thu_mua p ON b.ma_phieu_nhap = p.id
        LEFT JOIN ho_dan h ON p.ma_ho_dan = h.id
        WHERE 1=1"
        ; // Mẹo nối chuỗi WHERE

if ($f_chuong != '') $sql .= " AND b.ma_chuong = " . intval($f_chuong);
if ($f_ho != '')     $sql .= " AND p.ma_ho_dan = " . intval($f_ho);
if ($f_giong != '')  $sql .= " AND b.ma_giong = " . intval($f_giong);

// Sắp xếp: Đang nuôi lên đầu, sau đó mới đến Đã bán. Trong mỗi nhóm thì con mới nhập hiện trước.
$sql .= " ORDER BY FIELD(b.trang_thai, 'dang_nuoi', 'da_ban') ASC, b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0"><i class="bi bi-list-stars me-2"></i>Quản lý Đàn Bò Trong Kho</h3>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="them-bo.php" class="btn btn-primary shadow-sm rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> Thêm bò mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <form method="GET" action="" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="small fw-bold">Chuồng nuôi:</label>
                            <select name="chuong" class="form-select">
                                <option value="">-- Tất cả chuồng --</option>
                                <?php 
                                $q_chuong = mysqli_query($conn, "SELECT * FROM chuong_nuoi");
                                while($c = mysqli_fetch_assoc($q_chuong)) {
                                    echo "<option value='{$c['id']}' ".($f_chuong == $c['id'] ? 'selected':'').">{$c['ten_chuong']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Hộ dân:</label>
                            <select name="ho" class="form-select">
                                <option value="">-- Tất cả hộ dân --</option>
                                <?php 
                                $q_ho = mysqli_query($conn, "SELECT * FROM ho_dan");
                                while($h = mysqli_fetch_assoc($q_ho)) {
                                    echo "<option value='{$h['id']}' ".($f_ho == $h['id'] ? 'selected':'').">{$h['ten_ho_dan']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Giống bò:</label>
                            <select name="giong" class="form-select">
                                <option value="">-- Tất cả giống --</option>
                                <?php 
                                $q_giong = mysqli_query($conn, "SELECT * FROM giong_bo");
                                while($g = mysqli_fetch_assoc($q_giong)) {
                                    echo "<option value='{$g['id']}' ".($f_giong == $g['id'] ? 'selected':'').">{$g['ten_giong']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter"></i> Lọc</button>
                            <a href="danh-sach-bo.php" class="btn btn-outline-secondary w-100">Xóa lọc</a>
                        </div>
                    </form>
                </div>
            </div>

            <form action="ban-nhieu-bo.php" method="POST">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4">
                            <i class="bi bi-cart-check me-2"></i>Bán các mục đã chọn
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-3" style="width: 40px;"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                        <th>Hình</th>
                                        <th>Mã số tai</th>
                                        <th>Giống / Chuồng</th>
                                        <th class="text-center">Cân nặng</th>
                                        <th class="text-end">Giá mua</th>
                                        <th class="text-center">Ngày nhập</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $path = "../assets/uploads/" . $row['hinh_anh'];
                                            $img_src = (!empty($row['hinh_anh']) && file_exists($path)) ? $path : '../assets/img/no-image.png';
                                            $status_class = ($row['trang_thai'] == 'dang_nuoi') ? 'bg-success' : 'bg-secondary';
                                            $status_text = ($row['trang_thai'] == 'dang_nuoi') ? 'Đang nuôi' : 'Đã bán';
                                    ?>
                                            <tr>
                                                <td class="ps-3">
                                                    <?php if ($row['trang_thai'] == 'dang_nuoi'): ?>
                                                        <input type="checkbox" name="selected_bo[]" value="<?= $row['id'] ?>" class="form-check-input checkItem">
                                                    <?php endif; ?>
                                                </td>
                                                <td><img src="<?= $img_src ?>" class="rounded shadow-sm" style="width: 60px; height: 50px; object-fit: cover;"></td>
                                                <td>
                                                    <span class="fw-bold text-primary"><?= $row['ma_so_tai'] ?></span><br>
                                                    <small class="text-muted">Hộ: <?= $row['ten_ho_dan'] ?: 'N/A' ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= $row['ten_giong'] ?></div>
                                                    <span class="badge bg-light text-dark border"><?= $row['ten_chuong'] ?></span>
                                                </td>
                                                <td class="text-center fw-bold"><?= $row['can_nang_hien_tai'] ?> kg</td>
                                                <td class="text-end text-success fw-bold"><?= number_format($row['gia_mua_vao']) ?> đ</td>
                                                <td class="text-center text-muted"><small><?= $row['ngay_mua'] ? date('d/m/Y', strtotime($row['ngay_mua'])) : 'N/A' ?></small></td>
                                                <td class="text-center"><span class="badge <?= $status_class ?> rounded-pill px-3"><?= $status_text ?></span></td>
                                                <td class="text-center">
                                                    <div class="btn-group shadow-sm">
                                                        <a href="sua-bo.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                                        <a href="cap-nhat-suc-khoe.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-heart-pulse"></i></a>
                                                        <a href="lich-su-bo.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-person"></i></a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $row['id'] ?>, 'bo')"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    } else { ?>
                                        <tr><td colspan="9" class="text-center py-5 text-muted">Không tìm thấy con bò nào khớp với bộ lọc.</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>