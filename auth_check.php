<?php
// auth_check.php
// File này sẽ được include ở đầu TẤT CẢ các trang nội bộ (homepage, car, customer...)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Dùng include_once để tránh lỗi nếu connection đã được gọi
include_once "connection.php"; 

// 1. Nếu chưa có Session (Người dùng chưa đăng nhập hoặc đã tắt trình duyệt)
if (!isset($_SESSION['username'])) {
    
    // 2. Kiểm tra xem có Cookie "Ghi nhớ" không
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $current_time = time();
        
        // 3. Kiểm tra Token này có tồn tại trong Database và còn hạn không
        // Sử dụng Prepared Statement để bảo mật tuyệt đối
        $sql = "SELECT id, username, role FROM users WHERE login_token = ? AND token_expire > ?";
        $stmt = mysqli_prepare($link, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $token, $current_time);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            // 4. Nếu Token hợp lệ -> Tự động đăng nhập (Tạo lại Session)
            if ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['user_id_from_db'] = $row['id'];
                
                // (Tùy chọn) Có thể gia hạn thêm cookie tại đây nếu muốn
            } else {
                // Token sai hoặc hết hạn -> Đuổi về trang login
                header('location: login.php');
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            // Lỗi query -> Về login
            header('location: login.php');
            exit();
        }
    } else {
        // Không có Session, cũng không có Cookie -> Về login
        header('location: login.php');
        exit();
    }
}
// Nếu chạy đến đây nghĩa là Đã có Session hoặc Đã tự động đăng nhập thành công -> Cho phép vào trang.
?>