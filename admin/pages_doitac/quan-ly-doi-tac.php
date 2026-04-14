<?php
include '../config/db.php';
include './includes/header.php';
include './includes/sidebar.php';

// Lấy danh sách Hộ dân
$res_ho_dan = mysqli_query($conn, "SELECT * FROM ho_dan ORDER BY ten_ho_dan ASC");
// Lấy danh sách Khách hàng
$res_khach = mysqli_query($conn, "SELECT * FROM khach_hang ORDER BY ten_khach_hang ASC");
?>

<main class="app-main">
    <div class="container-fluid pt-3">
        <h3 class="mb-3"><i class="bi bi-person-rolodex me-2"></i>Quản Lý Đối Tác</h3>

        <ul class="nav nav-pills mb-3 shadow-sm p-2 bg-white rounded" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="pills-hodan-tab" data-bs-toggle="pill" data-bs-target="#pills-hodan">
                    <i class="bi bi-house-door me-1"></i> Hộ Dân (Người bán)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="pills-khach-tab" data-bs-toggle="pill" data-bs-target="#pills-khach">
                    <i class="bi bi-cart4 me-1"></i> Khách Hàng (Người mua)
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-hodan">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hồ sơ Hộ dân</h5>
                        <a href="them-ho-dan.php" class="btn btn-sm btn-light fw-bold text-success">+ Thêm Hộ Dân</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tên Hộ Dân</th>
                                    <th>Số điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($hd = mysqli_fetch_assoc($res_ho_dan)): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= $hd['ten_ho_dan'] ?></td>
                                    <td><?= $hd['so_dien_thoai'] ?></td>
                                    <td><?= $hd['dia_chi'] ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="modules/sua-ho-dan.php" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="modules/xoa-doi-tac.php?= $hd['id'] ?>&type=ho" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa hộ dân này?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pills-khach">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hồ sơ Khách hàng</h5>
                        <a href="them-khach-hang.php" class="btn btn-sm btn-light fw-bold text-primary">+ Thêm Khách Hàng</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tên Khách Hàng</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Loại khách</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($kh = mysqli_fetch_assoc($res_khach)): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= $kh['ten_khach_hang'] ?></td>
                                    <td><?= $kh['so_dien_thoai'] ?></td>
                                    <td><?= $kh['dia_chi'] ?></td>
                                    <td><span class="badge bg-info text-dark"><?= $kh['loai_khach'] ?></span></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="sua-doi-tac.php?id=<?= $kh['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="xoa-doi-tac.php?id=<?= $kh['id'] ?>&type=khach" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa khách hàng này?')"><i class="bi bi-trash"></i></a>
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