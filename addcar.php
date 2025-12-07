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

    // Check if car exists
    $check_query = "SELECT * FROM cars WHERE make='$make' AND model='$model' AND year='$year' AND color='$color'";
    $res = mysqli_query($link, $check_query);
    
    if(mysqli_num_rows($res) > 0) {
        // Update quantity if exists
        $row = mysqli_fetch_array($res);
        $new_quantity = $row["quantity"] + $quantity;
        mysqli_query($link, "UPDATE cars SET quantity='$new_quantity', price='$price' WHERE product_id=" . $row['product_id']);
        $message = "Car quantity updated successfully!";
        $message_type = "success";
    } else {
        // Insert new car
        mysqli_query($link, "INSERT INTO cars (make, model, year, color, quantity, price) VALUES ('$make','$model','$year','$color','$quantity','$price')");
        $message = "New car added successfully!";
        $message_type = "success";
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
        .form-control { border-radius: 5px; height: 45px; font-size: 0.95rem; }
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
                    <form action="" method="post">
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
</body>
</html>