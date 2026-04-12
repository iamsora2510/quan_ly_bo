<?php
$host = "localhost";
$user = "root";
$pass = ""; // Mặc định WAMP để trống
$db   = "quan_ly_dai_ly_bo"; // Đảm bảo tên này khớp với database trong phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
// Giúp hiển thị tiếng Việt không bị lỗi font
mysqli_set_charset($conn, "utf8");
?>