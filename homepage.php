<?php
include "connection.php";
include "auth_check.php";
$username = htmlspecialchars($_SESSION['username']);
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale'; 

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Quản Lý</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { font-weight: 800; color: #4e73df !important; letter-spacing: 1px; }
        .nav-link { color: #858796 !important; font-weight: 600; }
        .welcome-card { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border: none; border-radius: 15px; padding: 30px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(78, 115, 223, 0.4); }
        .role-badge { background-color: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; text-transform: uppercase; }
        .dashboard-card { border: none; border-radius: 15px; background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); transition: all 0.3s ease-in-out; height: 100%; text-decoration: none !important; display: block; overflow: hidden; }
        .dashboard-card:hover { transform: translateY(-10px); box-shadow: 0 1rem 3rem rgba(58,59,69,.15); }
        .card-body { padding: 30px; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 200px; }
        .icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; transition: transform 0.3s; }
        .dashboard-card:hover .icon-circle { transform: scale(1.1); }
        .card-title { font-weight: 800; font-size: 1.1rem; text-transform: uppercase; margin: 0; color: #5a5c69; }
        .card-car .icon-circle { background-color: #e3f2fd; color: #4e73df; }
        .card-car:hover .card-title { color: #4e73df; }
        .card-customer .icon-circle { background-color: #fff3cd; color: #f6c23e; }
        .card-customer:hover .card-title { color: #f6c23e; }
        .card-employee .icon-circle { background-color: #d4edda; color: #1cc88a; }
        .card-employee:hover .card-title { color: #1cc88a; }
        .card-trans .icon-circle { background-color: #f8d7da; color: #e74a3b; }
        .card-trans:hover .card-title { color: #e74a3b; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            <i class="fas fa-laugh-wink"></i> CAR MANAGER
        </a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">Hello, <b><?php echo $username; ?></b></span>
                    <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=<?php echo $username; ?>&background=4e73df&color=fff" width="30">
                </span>
            </li>

            <li class="nav-item ml-3">
                <a class="nav-link text-primary" href="index.php" title="Go to Public Website">
                    <i class="fas fa-home fa-sm fa-fw mr-2"></i> Website
                </a>
            </li>

            <li class="nav-item ml-3">
                <a class="nav-link text-danger" href="homepage.php?logout=true" onclick="return confirm('Ready to Leave?');">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12">
            <div class="welcome-card text-center text-md-left d-md-flex justify-content-between align-items-center">
                <div>
                    <h2 class="font-weight-bold mb-2">Welcome Back, <?php echo $username; ?>!</h2>
                    <p class="mb-0 op-8">Here is your management dashboard overview.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="role-badge">
                        <i class="fas fa-user-tag mr-2"></i> <?php echo strtoupper($role); ?> ACCOUNT
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="car.php" class="dashboard-card card-car">
                <div class="card-body">
                    <div class="icon-circle"><i class="fas fa-car"></i></div>
                    <h5 class="card-title">Cars Inventory</h5>
                    <small class="text-muted mt-2">Manage stocks & prices</small>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="customer.php" class="dashboard-card card-customer">
                <div class="card-body">
                    <div class="icon-circle"><i class="fas fa-users"></i></div>
                    <h5 class="card-title">Customers</h5>
                    <small class="text-muted mt-2">View client list</small>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="transactions.php" class="dashboard-card card-trans">
                <div class="card-body">
                    <div class="icon-circle"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h5 class="card-title">Transactions</h5>
                    <small class="text-muted mt-2">View sales history</small>
                </div>
            </a>
        </div>

        <?php if ($role === 'admin'): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="employee.php" class="dashboard-card card-employee">
                <div class="card-body">
                    <div class="icon-circle"><i class="fas fa-user-tie"></i></div>
                    <h5 class="card-title">Employees</h5>
                    <small class="text-muted mt-2">Manage staff access</small>
                </div>
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>