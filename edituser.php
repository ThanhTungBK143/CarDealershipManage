<?php
include "connection.php";
include "auth_check.php";

$current_user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
if ($current_user_role !== 'admin') {
    echo "<script>alert('Access Denied! You are not an Admin.'); window.location='homepage.php';</script>";
    exit();
}

// 2. GET ID TO EDIT
if (!isset($_GET['id'])) {
    header('location:employee.php');
    exit();
}
$id = intval($_GET["id"]);

$message = '';
$message_type = '';

// 3. HANDLE UPDATE REQUEST
if (isset($_POST["update"])) {
    $username = mysqli_real_escape_string($link, $_POST["username"]);
    $email = mysqli_real_escape_string($link, $_POST["email"]);
    $phone = mysqli_real_escape_string($link, $_POST["phone"]);
    $role_update = mysqli_real_escape_string($link, $_POST["role"]);
    $password_input = $_POST["password"];

    // PASSWORD LOGIC:
    // If input is NOT empty => User wants to change pass => Hash MD5 & Update
    // If input IS empty => Keep old pass => Do not update password column
    $password_sql = "";
    if (!empty($password_input)) {
        $new_pass_md5 = md5($password_input);
        $password_sql = ", password='$new_pass_md5'";
    }

    // Dynamic Update Query
    $sql_update = "UPDATE users 
                   SET username='$username', 
                       email='$email', 
                       phone='$phone', 
                       role='$role_update'
                       $password_sql 
                   WHERE id = $id";

    if (mysqli_query($link, $sql_update)) {
        $message = "Successfully updated account <b>$username</b>!";
        $message_type = 'success';
    } else {
        // Catch duplicate username error (Error 1062)
        if (mysqli_errno($link) == 1062) {
             $message = "Error: Username '$username' already exists. Please choose another.";
        } else {
             $message = "Update Failed: " . mysqli_error($link);
        }
        $message_type = 'danger';
    }
}

// 4. FETCH EXISTING DATA TO SHOW IN FORM
$res = mysqli_query($link, "SELECT * FROM users WHERE id = $id");
if (mysqli_num_rows($res) == 0) {
    header('location:employee.php');
    exit();
}
$row = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User Information</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #fff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); margin-top: 50px; }
        .card-header-custom { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 20px; font-weight: 700; text-align: center; border-radius: 15px 15px 0 0; }
        .btn-update { background-color: #4e73df; color: white; border-radius: 50px; padding: 10px 30px; font-weight: bold; width: 100%; }
        .btn-update:hover { background-color: #2e59d9; color: white; box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4); }
        .btn-back { border-radius: 50px; width: 100%; border: 2px solid #858796; color: #858796; font-weight: bold; padding: 10px 30px;}
        .btn-back:hover { background-color: #858796; color: white; text-decoration: none;}
        .input-group-text { background-color: #f8f9fc; color: #4e73df; border: 1px solid #ced4da; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand text-primary font-weight-bold" href="employee.php">
            <i class="fas fa-arrow-left mr-2"></i> BACK TO LIST
        </a>
    </div>
</nav>

<div class="container" style="margin-top: 80px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <i class="fas fa-user-cog mr-2"></i> COMPREHENSIVE EDIT
                </div>
                <div class="card-body p-4">
                    <form action="" method="post">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-danger">New Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-lock"></i></span></div>
                                <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                            </div>
                            <small class="text-muted">Only enter text here if you want to reset this user's password.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
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
                            <label class="font-weight-bold">Role (Permission)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tag"></i></span></div>
                                <select name="role" class="form-control">
                                    <option value="sale" <?php if($row['role'] == 'sale') echo 'selected'; ?>>Sale Staff</option>
                                    <option value="admin" <?php if($row['role'] == 'admin') echo 'selected'; ?>>Administrator</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-6"><a href="employee.php" class="btn btn-back text-center">Cancel</a></div>
                            <div class="col-6"><button type="submit" name="update" class="btn btn-update">Update</button></div>
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