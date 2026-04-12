<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function checkAdmin() {
    if ($_SESSION['vai_tro'] !== 'admin') {
        echo "Bạn không có quyền truy cập trang này!";
        exit();
    }
}
?>