<?php
session_start();
include 'config/db.php';
include 'includes/header.php'; // Thêm header để có Menu

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra tham số ID hóa đơn
if (!isset($_GET['id'])) {
    header('Location: lich-su-mua-hang.php');
    exit();
}

$id_hd = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Bảo mật: Chỉ cho khách xem đơn của chính họ
$check_owner = mysqli_query($conn, "SELECT h.id FROM phieu_xuat_ban h 
                                    JOIN khach_hang k ON h.ma_khach_hang = k.id 
                                    WHERE h.id = $id_hd AND k.user_id = $user_id");

if (mysqli_num_rows($check_owner) == 0) {
    echo "<div class='container mt-5 alert alert-danger text-center'>Bạn không có quyền xem đơn hàng này!</div>";
    include 'includes/footer.php';
    exit();
}

// Truy vấn lấy danh sách bò kèm theo đánh giá của khách (nếu có)
$sql = "SELECT ct.*, b.ma_so_tai, g.ten_giong, b.hinh_anh, dg.noi_dung as da_danh_gia, dg.so_sao as sao_da_gui
        FROM chi_tiet_phieu_xuat ct
        JOIN danh_sach_bo b ON ct.ma_bo = b.id
        JOIN giong_bo g ON b.ma_giong = g.id
        LEFT JOIN danh_gia dg ON (dg.ma_bo = b.id AND dg.ma_khach_hang = $user_id)
        WHERE ct.ma_phieu_xuat = $id_hd";
$result = mysqli_query($conn, $sql);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-search me-2"></i>CHI TIẾT ĐƠN HÀNG #<?= $id_hd ?>
        </h4>
        <a href="lich-su-mua-hang.php" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-body bg-white p-4">
            <div class="row">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($bo = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4" style="background: #fdfdfd; border: 1px solid #f1f1f1 !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/uploads/<?= $bo['hinh_anh'] ?: 'no-image.png' ?>"
                                            class="rounded-3 shadow-sm me-3"
                                            style="width: 100px; height: 80px; object-fit: cover; border: 1px solid #eee;">

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">#<?= $bo['ma_so_tai'] ?></h6>
                                            <p class="small mb-1 text-muted">Giống: <span class="text-dark"><?= $bo['ten_giong'] ?></span></p>
                                            <p class="small mb-0 text-success fw-bold">
                                                <?= number_format($bo['gia_ban_con_nay']) ?> đ
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 border-top pt-2">
                                        <?php if ($bo['da_danh_gia']): ?>
                                            <div class="p-2 bg-light rounded-3">
                                                <div class="text-warning small mb-1">
                                                    <?php for ($i = 1; $i <= $bo['sao_da_gui']; $i++) echo "★"; ?>
                                                </div>
                                                <p class="small mb-0 text-muted italic">"<?= $bo['da_danh_gia'] ?>"</p>
                                                <span class="badge bg-secondary mt-1" style="font-size: 10px;">Đã gửi phản hồi</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDanhGia"
                                                    onclick="setModalData('<?= $bo['ma_bo'] ?>', '<?= $bo['ma_so_tai'] ?>')">
                                                    <i class="bi bi-star-fill me-1"></i> Đánh giá
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        Không tìm thấy dữ liệu bò cho đơn hàng này.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer bg-light text-muted small px-4 py-3 border-0">
            <i class="bi bi-info-circle me-1"></i> Đây là danh sách bò thực tế đã bàn giao theo hóa đơn điện tử.
        </div>
    </div>
</div>

<div class="modal fade" id="modalDanhGia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="bi bi-chat-heart text-danger me-2"></i>Phản hồi bò #<span id="display_ma_so_tai"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="xu-ly-danh-gia.php" method="POST">
                <div class="modal-body py-4">
                    <input type="hidden" name="ma_bo" id="input_ma_bo">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1">Mức độ hài lòng</label>
                        <select name="so_sao" class="form-select border-0 bg-light rounded-pill px-4 py-2">
                            <option value="5">⭐⭐⭐⭐⭐ Rất hài lòng</option>
                            <option value="4">⭐⭐⭐⭐ Tốt</option>
                            <option value="3">⭐⭐⭐ Bình thường</option>
                            <option value="2">⭐⭐ Không hài lòng</option>
                            <option value="1">⭐ Rất tệ</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1">Nội dung đánh giá</label>
                        <textarea name="noi_dung" class="form-control border-0 bg-light rounded-3" rows="4"
                            placeholder="Bò rất khỏe, trại STU phục vụ nhiệt tình..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Để sau</button>
                    <button type="submit" name="btn_gui_dg" class="btn btn-success rounded-pill px-5 shadow">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setModalData(id_bo, ma_so) {
        document.getElementById('input_ma_bo').value = id_bo;
        document.getElementById('display_ma_so_tai').innerText = ma_so;
    }
</script>

<?php include 'includes/footer.php'; ?>