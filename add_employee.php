<?php
include "connection.php";
include "auth_check.php";

// 2. CHECK ADMIN PERMISSION
$current_user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';// isset() kiểm tra xem biến có tồn tại không 

if ($current_user_role !== 'admin') {
    echo "<script>alert('Access Denied! Only Administrators can add employees.'); window.location='homepage.php';</script>";
    exit();
}

$message = '';
$message_type = '';

// 3. HANDLE FORM SUBMISSION
// 3. HANDLE FORM SUBMISSION
if (isset($_POST["add_new"])) {
    // 1. Get and sanitize input data
    $username = mysqli_real_escape_string($link, $_POST["username"]);
    $password = mysqli_real_escape_string($link, $_POST["password"]);
    $email = mysqli_real_escape_string($link, $_POST["email"]);
    $phone = mysqli_real_escape_string($link, $_POST["phone"]);
    
    $role = 'sale'; 
    $password_hash = md5($password);

     // Check if any user exists with the same Username OR Email OR Phone
    $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email' OR phone = '$phone'";
    $check_result = mysqli_query($link, $check_sql);

    // If at least 1 row is found -> Duplicate information exists
    if (mysqli_num_rows($check_result) > 0) {
        // Fetch the existing user data to compare and find out exactly what is duplicated
        $existing_user = mysqli_fetch_assoc($check_result);
        
        if ($existing_user['username'] === $username) {
            $message = "Error: Username <b>'$username'</b> is already taken!";
        } elseif ($existing_user['email'] === $email) {
            $message = "Error: Email <b>'$email'</b> already exists in the system!";
        } elseif ($existing_user['phone'] === $phone) {
            $message = "Error: Phone number <b>'$phone'</b> is already registered!";
        } else {
            // Fallback for rare cases
            $message = "Error: The entered information already exists in the system!";
        }
        
        $message_type = 'danger'; // Red alert

    } else {
        // --- IF NO DUPLICATES FOUND, PROCEED TO INSERT ---

        $sql_insert = "INSERT INTO users (username, password, email, phone, role) 
                       VALUES ('$username', '$password_hash', '$email', '$phone', '$role')";

        if (mysqli_query($link, $sql_insert)) {
            $message = "Successfully added new Sale Staff: <b>$username</b>!";
            $message_type = 'success'; // Green alert
        } else {
            $message = "System Error: " . mysqli_error($link);
            $message_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Employee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fc; 
            font-family: 'Nunito', sans-serif; 
            color: #5a5c69; 
        }
        .navbar-custom {
             background-color: #fff; 
             box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); 
            }
        .card-custom { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); 
            margin-top: 50px; 
        }
        .card-header-custom { 
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); 
            color: white; 
            padding: 20px; 
            font-weight: 700; 
            text-align: center; 
            border-radius: 15px 15px 0 0; 
        }
        .btn-add { 
            background-color: #1cc88a; 
            color: white; 
            border-radius: 50px; 
            padding: 10px 30px; 
            font-weight: bold; 
            width: 100%; 
            border: none;
        }
        .btn-add:hover { 
            background-color: #17a673; 
            color: white; box-shadow: 0 4px 10px rgba(28, 200, 138, 0.4); 
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
            display: block;
        }
        .btn-back:hover { 
            background-color: #858796; 
            color: white; 
            text-decoration: none;
        }
        .input-group-text { 
            background-color: #f8f9fc; 
            color: #1cc88a; 
            border: 1px solid #ced4da; 
        }
        .form-control:focus { 
            border-color: #1cc88a; 
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25); 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand text-success font-weight-bold" href="employee.php">
            <i class="fas fa-arrow-left mr-2"></i> BACK TO LIST
        </a>
        <span class="navbar-text ml-auto">Admin: <b><?php echo $_SESSION['username']; ?></b></span>
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
                    <i class="fas fa-user-plus mr-2"></i> CREATE NEW SALE STAFF
                </div>
                <div class="card-body p-4">
                    <form action="" method="post">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                <input type="email" class="form-control" name="email" placeholder="Ex: employee@company.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                <input type="text" class="form-control" name="phone" placeholder="Ex: 0912345678" required>
                            </div>
                        </div>

                        <div class="alert alert-info text-center small mb-4">
                            <i class="fas fa-info-circle"></i> New accounts are created with <b>SALE</b> permission by default.
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-6"><a href="employee.php" class="btn btn-back">Cancel</a></div>
                            <div class="col-6"><button type="submit" name="add_new" class="btn btn-add">Add Employee</button></div>
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