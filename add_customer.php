<?php
include "connection.php";
include "auth_check.php";

$message = '';
$message_type = '';

// 2. XỬ LÝ THÊM KHÁCH HÀNG
if (isset($_POST["add_customer"])) {
    // Làm sạch dữ liệu đầu vào
    $full_name = mysqli_real_escape_string($link, $_POST["full_name"]);
    $email = mysqli_real_escape_string($link, $_POST["email"]);
    $phone = mysqli_real_escape_string($link, $_POST["phone"]);
    $address = mysqli_real_escape_string($link, $_POST["address"]);

    // Kiểm tra xem Email hoặc SĐT đã tồn tại chưa (Tránh trùng lặp)
    $check_query = "SELECT * FROM customers WHERE email = '$email' OR phone = '$phone'";
    $check_res = mysqli_query($link, $check_query);

    if (mysqli_num_rows($check_res) > 0) {
        $message = "Lỗi: Email hoặc Số điện thoại này đã tồn tại trong hệ thống!";
        $message_type = 'danger';
    } else {
        // Thực hiện Insert
        $sql_insert = "INSERT INTO customers (full_name, email, phone, address) 
                       VALUES ('$full_name', '$email', '$phone', '$address')";

        if (mysqli_query($link, $sql_insert)) {
            $message = "Thêm thành công khách hàng: <b>$full_name</b>!";
            $message_type = 'success';
        } else {
            $message = "Lỗi hệ thống: " . mysqli_error($link);
            $message_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thêm Khách Hàng Mới</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { 
            background-color: #fff; 
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); 
        }
        .navbar-brand { 
            color: #4e73df !important; 
            font-weight: 800; 
        }
        .card-custom { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); 
            margin-top: 50px; 
        }
        
        .card-header-custom { 
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); 
            color: white; 
            padding: 20px; 
            font-weight: 700; 
            text-align: center; 
            border-radius: 15px 15px 0 0; 
        }
        .btn-add { 
            background-color: #f6c23e; 
            color: white; border-radius: 50px; 
            padding: 10px 30px; 
            font-weight: bold; 
            width: 100%; 
            border: none;
        }
        .btn-add:hover { 
            background-color: #dda20a; 
            color: white; 
            box-shadow: 0 4px 10px rgba(246, 194, 62, 0.4); 
            transform: translateY(-2px); 
            transition: all 0.3s;
        }
        
        .btn-back { 
            border-radius: 50px; 
            width: 100%; 
            border: 2px solid #858796; 
            color: #858796; 
            font-weight: bold; 
            padding: 10px 30px; 
            text-align: center; 
            display: block;}
        .btn-back:hover { 
            background-color: #858796; 
            color: white; 
            text-decoration: none;}

        .input-group-text { 
            background-color: #f8f9fc; 
            color: #f6c23e; 
            border: 1px solid #ced4da; }
        .form-control:focus { 
            border-color: #f6c23e; 
            box-shadow: 0 0 0 0.2rem rgba(246, 194, 62, 0.25); 
        }
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
                    <i class="fas fa-user-plus mr-2"></i> ADD NEW CUSTOMER
                </div>
                <div class="card-body p-4">
                    <form action="" method="post">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                <input type="text" class="form-control" name="full_name" placeholder="Enter customer's name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                <input type="text" class="form-control" name="phone" placeholder="Ex: 0901234567" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                <input type="email" class="form-control" name="email" placeholder="Ex: customer@gmail.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span></div>
                                <textarea class="form-control" name="address" rows="2" placeholder="Enter full address" required></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-6"><a href="customer.php" class="btn btn-back">Cancel</a></div>
                            <div class="col-6"><button type="submit" name="add_customer" class="btn btn-add">Save Customer</button></div>
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