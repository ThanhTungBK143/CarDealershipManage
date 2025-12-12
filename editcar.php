<?php
include "connection.php";
include "auth_check.php";

// 1. CHECK PERMISSION (ONLY ADMIN CAN EDIT)
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale';
if ($role !== 'admin') {
    echo "<script>alert('Access Denied! Only Administrators can edit car details.'); window.location='car.php';</script>";
    exit();
}

// Get ID
if (!isset($_GET["id"])) {
    echo "<script>alert('Error: No product ID provided.'); window.location='car.php';</script>";
    exit();
}
$product_id = intval($_GET["id"]);

$message = "";
$message_type = "";

// Handle Update
if (isset($_POST["update"])) {
    $make     = mysqli_real_escape_string($link, $_POST["make"]);
    $model    = mysqli_real_escape_string($link, $_POST["model"]);
    $year     = intval($_POST["year"]);
    $color    = mysqli_real_escape_string($link, $_POST["color"]);
    $quantity = intval($_POST["quantity"]);
    $price    = floatval($_POST["price"]);

<<<<<<< Updated upstream
    // --- XỬ LÝ ẢNH (LOGIC MỚI) ---
    // Mặc định lấy tên ảnh cũ từ input hidden
    $image_filename = $_POST['current_image']; 

    // Kiểm tra nếu người dùng có chọn ảnh mới
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $filename = $_FILES["car_image"]["name"];
        $filesize = $_FILES["car_image"]["size"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(!array_key_exists($ext, $allowed)) {
            $message = "Lỗi: Chỉ chấp nhận file JPG hoặc PNG.";
            $message_type = "danger";
        } elseif($filesize > 5 * 1024 * 1024) {
            $message = "Lỗi: Dung lượng file quá lớn (>5MB).";
            $message_type = "danger";
        } else {
            // Upload ảnh mới
            $new_filename = time() . "_" . rand(1000, 9999) . "." . $ext;
            if(move_uploaded_file($_FILES["car_image"]["tmp_name"], "uploads/" . $new_filename)){
                $image_filename = $new_filename; // Cập nhật tên file mới để lưu vào DB
            }
        }
    }

    if ($message_type !== "danger") {
        $update_query = "UPDATE cars SET 
                         make='$make', 
                         model='$model', 
                         year='$year', 
                         color='$color', 
                         quantity='$quantity', 
                         price='$price',
                         image='$image_filename' 
                         WHERE product_id = $product_id";

        if (mysqli_query($link, $update_query)) {
            // [CẬP NHẬT] Thông báo xong nhảy về trang car.php luôn
            echo "<script>alert('Car details updated successfully!'); window.location='car.php';</script>";
            exit(); 
        } else {
            $message = "Update failed: " . mysqli_error($link);
            $message_type = "danger";
        }
=======
    // --- IMAGE HANDLING ---
    $image_filename = $_POST['current_image']; 

    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $filename = basename($_FILES["car_image"]["name"]); 
        $filesize = $_FILES["car_image"]["size"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(!array_key_exists(strtolower($ext), $allowed)) {
            $message = "Error: JPG or PNG files only.";
            $message_type = "danger";
        } elseif($filesize > 5 * 1024 * 1024) {
            $message = "Error: File size too large (>5MB).";
            $message_type = "danger";
        } else {
            // Check for duplicate filename
            $target_path = "uploads/" . $filename;

            if (file_exists($target_path)) {
                $message = "Error: File named <b>'$filename'</b> already exists. Please rename your file.";
                $message_type = "danger";
            } else {
                // Upload new file
                if(move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_path)){
                    // Optional: Delete old image
                    $old_file = "uploads/" . $image_filename;
                    if(file_exists($old_file) && $image_filename != 'default.jpg' && !empty($image_filename)){
                        unlink($old_file);
                    }
                    $image_filename = $filename; // Update to new filename
                } else {
                    $message = "Error: Failed to upload file.";
                    $message_type = "danger";
                }
            }
        }
>>>>>>> Stashed changes
    }

    // Only update DB if no error
    if ($message_type !== "danger") {
        $update_query = "UPDATE cars SET 
                         make='$make', 
                         model='$model', 
                         year='$year', 
                         color='$color', 
                         quantity='$quantity', 
                         price='$price',
                         image='$image_filename' 
                         WHERE product_id = $product_id";

        if (mysqli_query($link, $update_query)) {
            echo "<script>alert('Car details updated successfully!'); window.location='car.php';</script>";
            exit(); 
        } else {
            $message = "Update failed: " . mysqli_error($link);
            $message_type = "danger";
        }
    }
} // <--- THIS WAS THE MISSING BRACE

// Fetch Data
$query = "SELECT * FROM cars WHERE product_id = $product_id";
$res = mysqli_query($link, $query);
if (!$res || mysqli_num_rows($res) == 0) {
    echo "<script>alert('Error: Car not found.'); window.location='car.php';</script>";
    exit();
}
$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Car Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #fff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); margin-top: 50px; }
        .card-header-custom { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 20px; font-weight: 700; text-align: center; border-radius: 15px 15px 0 0; }
        .form-control, .custom-file-label { border-radius: 5px; height: 45px; display: flex; align-items: center; }
        .input-group-text { background-color: #f8f9fc; color: #4e73df; border: 1px solid #ced4da; }
        .btn-update { background-color: #4e73df; color: white; border-radius: 50px; padding: 10px 30px; font-weight: bold; width: 100%; border: none; }
        .btn-update:hover { background-color: #2e59d9; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4); }
        .btn-back { border-radius: 50px; width: 100%; border: 2px solid #858796; color: #858796; font-weight: bold; padding: 10px 30px; text-align: center; display: block; }
        .btn-back:hover { background-color: #858796; color: white; text-decoration: none; }
        .current-img-preview { width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid #eaecf4; margin-bottom: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="car.php"><i class="fas fa-arrow-left mr-2"></i> BACK TO INVENTORY</a>
        <span class="navbar-text ml-auto">Admin: <b><?php echo $_SESSION['username']; ?></b></span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card card-custom">
                <div class="card-header card-header-custom"><i class="fas fa-edit mr-2"></i> EDIT CAR INFORMATION</div>
                <div class="card-body p-4 p-md-5">
                    <form action="" method="post" enctype="multipart/form-data">
                        
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($row['image']); ?>">

                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <label class="font-weight-bold d-block text-left">Current Image</label>
                                <?php 
                                    $img_path = "uploads/" . $row['image'];
<<<<<<< Updated upstream
=======
                                    // Check if image is empty OR file doesn't exist
>>>>>>> Stashed changes
                                    if(empty($row['image']) || !file_exists($img_path)) {
                                        $img_path = "https://via.placeholder.com/600x300.png?text=No+Image";
                                    }
                                ?>
                                <img src="<?php echo $img_path; ?>" class="current-img-preview" alt="Current Car Image">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Make</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-industry"></i></span></div>
                                    <input type="text" class="form-control" name="make" value="<?php echo htmlspecialchars($row['make']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Model</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-car"></i></span></div>
                                    <input type="text" class="form-control" name="model" value="<?php echo htmlspecialchars($row['model']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Year</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                    <input type="number" class="form-control" name="year" value="<?php echo htmlspecialchars($row['year']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Color</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-palette"></i></span></div>
                                    <input type="text" class="form-control" name="color" value="<?php echo htmlspecialchars($row['color']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Quantity</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-cubes"></i></span></div>
                                    <input type="number" class="form-control" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Price (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-dollar-sign"></i></span></div>
                                    <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Update Image (Optional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="car_image" id="carImg">
                                <label class="custom-file-label" for="carImg">Choose new file...</label>
                            </div>
                            <small class="text-muted">Leave blank if you want to keep the current image.</small>
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-6"><a href="car.php" class="btn btn-back">Cancel</a></div>
                            <div class="col-6"><button type="submit" name="update" class="btn btn-update">Save Changes</button></div>
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
<script>
<<<<<<< Updated upstream
    // Hiện tên file khi chọn
=======
    // Show selected filename
>>>>>>> Stashed changes
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
</body>
</html>