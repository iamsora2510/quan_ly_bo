<?php
session_start();
include 'config/db.php';
include 'includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-success">THÔNG TIN LIÊN HỆ</h2>
        <p class="text-muted">Kết nối với TD Cattle Farm để được tư vấn tốt nhất</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-4 rounded-4">
                <div class="mx-auto bg-light-success text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #e8f5e9;">
                    <i class="bi bi-telephone-outbound-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Hotline / Zalo</h5>
                <p class="text-muted">Hỗ trợ tư vấn 24/7</p>
                <a href="https://zalo.me/0335789697" target="_blank" class="text-decoration-none fw-bold fs-5 text-success">0333333333</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-4 rounded-4">
                <div class="mx-auto bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #e3f2fd;">
                    <i class="bi bi-facebook fs-3"></i>
                </div>
                <h5 class="fw-bold">Facebook</h5>
                <p class="text-muted">Theo dõi hoạt động của trại</p>
                <a href="Link face" target="_blank" class="text-decoration-none fw-bold fs-5" style="color: #1877F2;">TD Cattle Farm</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center p-4 rounded-4">
                <div class="mx-auto bg-light-danger text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background-color: #ffebee;">
                    <i class="bi bi-geo-alt-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Địa chỉ trại</h5>
                <p class="text-muted">Ghé thăm trực tiếp</p>
                <p class="fw-bold text-dark mb-0">196 Cao Lỗ, P.4, Q.8, HCM</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center mb-4">
            <h4 class="fw-bold"><i class="bi bi-map me-2"></i>Vị trí trên bản đồ</h4>
        </div>
        <div class="col-12">
            <div class="rounded-4 overflow-hidden shadow border">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.9427031788846!2d106.67585701048657!3d10.73889948936339!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752face344104f%3A0x81ea29218c31f618!2zMTk2IENhbyBM4buXLCBQaMaw4budbmcgNCwgQ2jDoW5oIEjGsG5nLCBI4buTIENow60gTWluaCwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1776151011278!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>