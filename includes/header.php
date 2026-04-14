<!DOCTYPE html>
<html lang="vi">
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đại Lý Bò TD Cattle Farm - Uy Tín Chất Lượng</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="assets/css/style-client.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-cow me-2"></i>TD Cattle Farm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang Chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="danh-muc.php">Danh Mục Bò</a></li>
                    <li class="nav-item"><a class="nav-link" href="ky-gui-bo.php">Ký Gửi Bò</a></li>
                    <li class="nav-item"><a class="nav-link" href="lien-he.php">Liên Hệ</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                Chào, <?= $_SESSION['user_name'] ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="thong-tin-ca-nhan.php">
                                        <i class="bi bi-person-gear me-2"></i>Thông tin tài khoản
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="ds-dat-cho.php">
                                        <i class="bi bi-journal-check me-2"></i>Bò đã giữ chỗ
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="lich-su-mua-hang.php">
                                        <i class="bi bi-clock-history me-2"></i>Lịch sử mua bò
                                    </a>
                                </li>
                                     <li>
                                    <a class="dropdown-item" href="lich-su-ky-gui.php">
                                        <i class="bi bi-clock-history me-2"></i>Lịch sử ký gửi
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary btn-sm px-3 rounded-pill">Đăng Nhập</a>
                        <a href="register.php" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm">Đăng Ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </nav>