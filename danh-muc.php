<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// 1. Lấy tham số lọc và tìm kiếm
$filter_giong = isset($_GET['giong']) ? (int)$_GET['giong'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. Truy vấn danh sách bò đang bán
$sql = "SELECT b.*, g.ten_giong FROM danh_sach_bo b 
        JOIN giong_bo g ON b.ma_giong = g.id 
        WHERE b.trang_thai = 'dang_nuoi'";

if ($filter_giong > 0) {
    $sql .= " AND b.ma_giong = $filter_giong";
}
if (!empty($search)) {
    $sql .= " AND (b.ma_so_tai LIKE '%$search%' OR g.ten_giong LIKE '%$search%')";
}

$sql .= " ORDER BY b.id DESC";
$res = mysqli_query($conn, $sql);
?>

<style>
    /* Hiệu ứng zoom nhẹ cho ảnh và đổ bóng cho Card */
    .card-hover { transition: all 0.3s ease; border: 1px solid #eee !important; }
    .card-hover:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
    .filter-chip { transition: all 0.2s; text-decoration: none; }
    .filter-chip.active { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
    .search-box { border-radius: 50px; padding-left: 20px; }
</style>

<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold text-success mb-1">DANH MỤC BÒ GIỐNG</h2>
            <p class="text-muted">Chọn lựa những giống bò chất lượng nhất tại STU Beef</p>
        </div>
        <div class="col-lg-6">
            <form action="" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden">
                <input type="text" name="search" class="form-control border-0 search-box" 
                       placeholder="Tìm mã số tai hoặc giống bò..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-success px-4 rounded-pill m-1">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="index.php" class="btn btn-outline-success rounded-pill filter-chip <?= $filter_giong == 0 ? 'active' : '' ?>">Tất cả</a>
        <?php 
        $giongs = mysqli_query($conn, "SELECT * FROM giong_bo");
        while($g = mysqli_fetch_assoc($giongs)): ?>
            <a href="index.php?giong=<?= $g['id'] ?>" 
               class="btn btn-outline-success rounded-pill filter-chip <?= $filter_giong == $g['id'] ? 'active' : '' ?>">
                Bò <?= $g['ten_giong'] ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($res) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($res)): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden card-hover">
                    <div class="position-relative">
                        <img src="assets/uploads/<?= $row['hinh_anh'] ?: 'no-image.png' ?>" 
                             class="card-img-top" style="height: 240px; object-fit: cover;">
                        <div class="position-absolute top-0 start-0 m-3">
                             <span class="badge bg-white text-dark shadow-sm py-2 px-3 rounded-pill fw-bold">
                                 <i class="bi bi-tag-fill text-success me-1"></i><?= $row['ten_giong'] ?>
                             </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">#<?= $row['ma_so_tai'] ?></h5>
                            <span class="text-muted small"><i class="bi bi-speedometer2 me-1"></i><?= $row['can_nang_hien_tai'] ?> kg</span>
                        </div>
                        
                        <p class="text-muted small mb-4">Bò khỏe mạnh, đã tiêm chủng đầy đủ, phù hợp nuôi vỗ béo hoặc làm giống.</p>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="text-danger fw-bold fs-4">Giá: Liên hệ</span>
                            <i class="bi bi-telephone-outbound ms-auto text-success"></i>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="chi-tiet-bo.php?id=<?= $row['id'] ?>" class="btn btn-light w-100 rounded-pill border fw-semibold">Chi tiết</a>
                            </div>
                            <div class="col-6">
                                <a href="dat-cho.php?id=<?= $row['id'] ?>" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">Giữ chỗ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                <p class="text-muted mt-3">Rất tiếc, không tìm thấy con bò nào phù hợp yêu cầu của bạn.</p>
                <a href="index.php" class="btn btn-success rounded-pill px-4">Xem tất cả bò</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>