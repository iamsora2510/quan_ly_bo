<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

// 1. Lấy 3 con bò mới nhất
$sql_hot = "SELECT b.*, g.ten_giong FROM danh_sach_bo b 
            JOIN giong_bo g ON b.ma_giong = g.id 
            WHERE b.trang_thai = 'dang_nuoi' ORDER BY b.id DESC LIMIT 3";
$res_hot = mysqli_query($conn, $sql_hot);

$sql_feedback = "SELECT dg.*, kh.ten_khach_hang 
                 FROM danh_gia dg 
                 JOIN khach_hang kh ON dg.ma_khach_hang = kh.user_id 
                 WHERE dg.trang_thai = 1 AND dg.so_sao = 5 
                 ORDER BY dg.id DESC LIMIT 3";
$res_feedback = mysqli_query($conn, $sql_feedback);
?>

<link rel="stylesheet" href="assets/css/home-style.css">

<div class="container">
    <div class="hero-section p-5 mb-4 shadow-sm" style="background: url('assets/img/hero-beef.jpg') no-repeat center; background-size: cover; min-height: 450px;">
        <div class="container-fluid py-5 text-white h-100 d-flex flex-column justify-content-center" style="background: rgba(0,0,0,0.4); border-radius: 15px; position: absolute; top:0; left:0; width:100%; height:100%;">
            <div class="ps-md-5">
                <h1 class="display-4 fw-bold">TD Cattle Farm</h1>
                <p class="col-md-8 fs-4">Đại lý cung cấp bò giống chất lượng. <br>Đồng hành cùng hộ dân trong quy trình chăn nuôi hiện đại.</p>
                <a href="danh-muc.php" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg mt-3">Khám Phá Ngay</a>
            </div>
        </div>
    </div>

    <div class="py-5 text-center">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-patch-check-fill fs-1 text-success"></i>
                    <h4 class="mt-3 fw-bold">Nguồn Gốc Rõ Ràng</h4>
                    <p class="text-muted small">Bò được kiểm định y tế và tiêm ngừa đầy đủ trước khi xuất chuồng.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-truck fs-1 text-success"></i>
                    <h4 class="mt-3 fw-bold">Hỗ Trợ Vận Chuyển</h4>
                    <p class="text-muted small">Giao hàng tận nơi, đảm bảo sức khỏe bò giống trong suốt quá trình di chuyển.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-graph-up-arrow fs-1 text-success"></i>
                    <h4 class="mt-3 fw-bold">Tối Ưu Lợi Nhuận</h4>
                    <p class="text-muted small">Hệ thống minh bạch chi phí nuôi, giúp hộ dân kiểm soát lợi nhuận tốt nhất.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="bi bi-stars text-warning me-2"></i>BÒ MỚI NHẬP TRẠI</h3>
            <a href="danh-muc.php" class="btn btn-sm btn-outline-success rounded-pill px-3">Tất cả sản phẩm <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="row">
            <?php while($row = mysqli_fetch_assoc($res_hot)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 card-bo rounded-4 overflow-hidden border-0">
                    <div class="position-relative">
                        <img src="assets/uploads/<?= $row['hinh_anh'] ?: 'no-image.png' ?>" class="card-img-top" style="height: 230px; object-fit: cover;">
                        <span class="position-absolute top-0 start-0 m-3 badge bg-success shadow-sm">Mới về</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <small class="text-uppercase text-muted fw-bold"><?= $row['ten_giong'] ?></small>
                        <h5 class="fw-bold my-2 text-dark">#<?= $row['ma_so_tai'] ?></h5>
                        <p class="text-success fw-bold fs-5 mb-3">Giá: Liên hệ</p>
                        <a href="chi-tiet-bo.php?id=<?= $row['id'] ?>" class="btn btn-success w-100 rounded-pill">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="py-5 bg-light rounded-4 px-4 mb-5">
        <h3 class="text-center fw-bold mb-5">KHÁCH HÀNG NÓI GÌ VỀ CHÚNG TÔI</h3>
        <div class="row g-4">
            <?php if(mysqli_num_rows($res_feedback) > 0): ?>
                <?php while($item = mysqli_fetch_assoc($res_feedback)): ?>
                <div class="col-md-4">
                    <div class="testimonial-card shadow-sm h-100">
                        <div class="star-color mb-2 text-warning">
                            <?php for($i=1; $i<=$item['so_sao']; $i++) echo '★'; ?>
                        </div>
                        <p class="fst-italic text-muted">"<?= $item['noi_dung'] ?>"</p>
                        <hr>
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                <?= strtoupper(substr($item['ten_khach_hang'], 0, 1)) ?>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold"><?= $item['ten_khach_hang'] ?></h6>
                                <small class="text-muted">Đã giao dịch thành công</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">Chưa có đánh giá nào được hiển thị.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>