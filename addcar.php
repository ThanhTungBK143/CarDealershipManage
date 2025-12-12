<?php
include "connection.php";
include "auth_check.php";

// 2. CHECK PERMISSION (ONLY ADMIN CAN ACCESS)
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale';
if ($role !== 'admin') {
    echo "<script>alert('Access Denied! Only Administrators can add new cars.'); window.location='car.php';</script>";
    exit();
}

$message = ''; 
$message_type = ''; 

if(isset($_POST["insert"])) {
    $make = mysqli_real_escape_string($link, $_POST['make']);
    $model = mysqli_real_escape_string($link, $_POST['model']);
    $year = (int)$_POST['year'];
    $color = mysqli_real_escape_string($link, $_POST['color']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    
    // --- XỬ LÝ UPLOAD ẢNH ---
    $image_filename = "default.jpg"; // Ảnh mặc định nếu không upload
    
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $filename = $_FILES["car_image"]["name"];
        $filetype = $_FILES["car_image"]["type"];
        $filesize = $_FILES["car_image"]["size"];

        // Kiểm tra đuôi file
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) die("Lỗi: Vui lòng chọn định dạng JPG hoặc PNG.");

        // Giới hạn dung lượng (ví dụ 5MB)
        if($filesize > 5 * 1024 * 1024) die("Lỗi: Dung lượng file quá lớn.");

        // Đặt tên file mới để tránh trùng lặp (Time + Random)
        $new_filename = time() . "_" . rand(1000, 9999) . "." . $ext;
        
        // Di chuyển file vào thư mục uploads
        if(move_uploaded_file($_FILES["car_image"]["tmp_name"], "uploads/" . $new_filename)){
            $image_filename = $new_filename;
        } else {
            $message = "Lỗi: Không thể tải file lên server.";
            $message_type = "danger";
        }
    }

    // Insert new car with Image
    $sql = "INSERT INTO cars (make, model, year, color, quantity, price, image) 
            VALUES ('$make','$model','$year','$color','$quantity','$price', '$image_filename')";
            
    if(mysqli_query($link, $sql)) {
        $message = "New car added successfully with image!";
        $message_type = "success";
    } else {
        $message = "Database Error: " . mysqli_error($link);
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Car</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); overflow: hidden; }
        .card-header-custom { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; text-align: center; }
        .form-control, .custom-file-label { border-radius: 5px; height: 45px; font-size: 0.95rem; display: flex; align-items: center;}
        .input-group-text { background-color: #f8f9fc; border-right: none; color: #4e73df; }
        .btn-submit { background-color: #4e73df; border-color: #4e73df; color: white; font-weight: 700; padding: 12px; border-radius: 50px; transition: all 0.3s; }
        .btn-submit:hover { background-color: #2e59d9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php"><i class="fas fa-arrow-left mr-2"></i> BACK TO DASHBOARD</a>
        <span class="navbar-text ml-auto">User: <b><?php echo $_SESSION['username']; ?></b></span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card card-custom">
                <div class="card-header card-header-custom"><i class="fas fa-car mr-2"></i> Add New Vehicle</div>
                <div class="card-body p-4 p-md-5">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Car Make</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-industry"></i></span></div>
                                    <input type="text" class="form-control" name="make" placeholder="e.g. Toyota" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Car Model</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-car-side"></i></span></div>
                                    <input type="text" class="form-control" name="model" placeholder="e.g. Camry" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Year</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                    <input type="number" class="form-control" name="year" placeholder="e.g. 2024" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Color</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-palette"></i></span></div>
                                    <input type="text" class="form-control" name="color" placeholder="e.g. White" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Quantity</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-cubes"></i></span></div>
                                    <input type="number" class="form-control" name="quantity" placeholder="Quantity" min="1" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Price (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-dollar-sign"></i></span></div>
                                    <input type="number" step="0.01" class="form-control" name="price" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Car Image <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="car_image" id="carImg" required>
                                <label class="custom-file-label" for="carImg">Choose file...</label>
                            </div>
                            <small class="text-muted">Supported formats: JPG, PNG. Max size: 5MB.</small>
                        </div>

                        <hr class="my-4">
                        <button type="submit" name="insert" class="btn btn-submit btn-block"><i class="fas fa-plus-circle mr-2"></i> ADD TO INVENTORY</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    // Hiển thị tên file đã chọn
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
</body>
</html>