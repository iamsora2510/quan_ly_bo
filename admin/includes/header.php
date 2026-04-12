<?php
session_start();

// Nếu chưa đăng nhập HOẶC đăng nhập rồi nhưng KHÔNG PHẢI admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Đuổi ngay ra trang login bên ngoài
    header("Location: ./login.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Hệ thống Quản lý Bò | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />


<link rel="stylesheet" href="../../assets/plugins/adminlte/adminlte.min.css">
<link rel="stylesheet" href="../../assets/css/admin-style.css">
<link rel="stylesheet" href="../../assets/css/admin-custom.css">
</style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
                    </li>
                    <li class="nav-item d-none d-md-block"><a href="index.php" class="nav-link">Trang chủ</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="../../assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image" />
                            <span class="d-none d-md-inline">Duy Admin</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>