<?php
// logout.php
session_start();
include "connection.php";

// 1. Xóa Token trong Database (Để token cũ không còn tác dụng)
if (isset($_SESSION['user_id_from_db'])) {
    $uid = $_SESSION['user_id_from_db'];
    // Xóa token và set ngày hết hạn về 0
    mysqli_query($link, "UPDATE users SET login_token = NULL, token_expire = 0 WHERE id = $uid");
}

// 2. Hủy Session
session_unset();
session_destroy();

// 3. Hủy Cookie trình duyệt (Set thời gian về quá khứ)
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/");
}

// 4. Quay về trang đăng nhập
header('location: login.php');
exit();
?>