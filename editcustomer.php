<?php
include "connection.php";
include "auth_check.php";

// [ĐÃ XÓA] PHẦN KIỂM TRA QUYỀN ADMIN
// Bây giờ Sale cũng được phép vào đây để sửa thông tin

// 3. LẤY ID KHÁCH HÀNG
if (!isset($_GET['id'])) {
    header('location:customer.php');
    exit();
}
$id = intval($_GET["id"]); 

$message = '';
$message_type = '';

// 4. XỬ LÝ CẬP NHẬT
if (isset($_POST["update_customer"])) {
    $full_name = mysqli_real_escape_string($link, $_POST["full_name"]);
    $email = mysqli_real_escape_string($link, $_POST["email"]);
    $phone = mysqli_real_escape_string($link, $_POST["phone"]);
    $address = mysqli_real_escape_string($link, $_POST["address"]);

    $sql_update = "UPDATE customers 
                   SET full_name='$full_name', email='$email', phone='$phone', address='$address' 
                   WHERE customer_id = $id";

    if (mysqli_query($link, $sql_update)) {
        $message = "Cập nhật thành công khách hàng: <b>$full_name</b>!";
        $message_type = 'success';
    } else {
        $message = "Lỗi cập nhật: " . mysqli_error($link);
        $message_type = 'danger';
    }
}

// 5. LẤY DỮ LIỆU CŨ
$res = mysqli_query($link, "SELECT * FROM customers WHERE customer_id = $id");
if (mysqli_num_rows($res) == 0) {
    header('location:customer.php');
    exit();
}
$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sửa Thông Tin Khách Hàng</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #fff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); margin-top: 50px; }
        .card-header-custom { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); color: white; padding: 20px; font-weight: 700; text-align: center; border-radius: 15px 15px 0 0; }
        .btn-update { background-color: #f6c23e; color: white; border-radius: 50px; padding: 10px 30px; font-weight: bold; width: 100%; border: none; }
        .btn-update:hover { background-color: #dda20a; color: white; box-shadow: 0 4px 10px rgba(246, 194, 62, 0.4); transform: translateY(-2px); transition: all 0.3s; }
        .btn-back { border-radius: 50px; width: 100%; border: 2px solid #858796; color: #858796; font-weight: bold; padding: 10px 30px; text-align: center; display: block;}
        .btn-back:hover { background-color: #858796; color: white; text-decoration: none;}
        .input-group-text { background-color: #f8f9fc; color: #f6c23e; border: 1px solid #ced4da; }
        .form-control:focus { border-color: #f6c23e; box-shadow: 0 0 0 0.2rem rgba(246, 194, 62, 0.25); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="customer.php">
            <i class="fas fa-arrow-left mr-2"></i> BACK TO LIST
        </a>
        <span class="navbar-text ml-auto">User: <b><?php echo $_SESSION['username']; ?></b></span>
    </div>
</nav>

<div class="container" style="margin-top: 80px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <?php if($message_type=='success') echo '<i class="fas fa-check-circle mr-2"></i>'; else echo '<i class="fas fa-exclamation-triangle mr-2"></i>'; ?>
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <i class="fas fa-user-edit mr-2"></i> EDIT CUSTOMER
                </div>
                <div class="card-body p-4">
                    <form action="" method="post">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Full Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($row['full_name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Phone Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Address</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                                <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-6"><a href="customer.php" class="btn btn-back">Cancel</a></div>
                            <div class="col-6"><button type="submit" name="update_customer" class="btn btn-update">Update Info</button></div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>