<?php
include "connection.php";
include "auth_check.php";

// Lấy Role từ session (mặc định là sale nếu lỗi)
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Quản Lý Khách Hàng</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); background-color: #fff; overflow: hidden; margin-top: 30px; }
        .card-header { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .card-header h4 { margin: 0; font-weight: 700; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        .btn-add-new { background-color: white; color: #dda20a; font-weight: bold; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.2s; }
        .btn-add-new:hover { transform: translateY(-2px); text-decoration: none; color: #b38300; }
        .table thead th { border-top: none; border-bottom: 2px solid #e3e6f0; background-color: #f8f9fc; color: #5a5c69; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; }
        .table tbody td { vertical-align: middle; color: #5a5c69; font-size: 0.95rem; }
        .btn-action { border-radius: 5px; font-size: 0.85rem; margin: 2px; }
        .avatar-initial { width: 35px; height: 35px; background-color: #eaecf4; color: #5a5c69; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            <i class="fas fa-arrow-left mr-2"></i> BACK TO DASHBOARD
        </a>
        <span class="navbar-text ml-auto">
            Logged in as: <b><?php echo $_SESSION['username']; ?></b> (<?php echo strtoupper($role); ?>)
        </span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    
    <div class="card card-table">
        <div class="card-header">
            <h4><i class="fas fa-users mr-2"></i> Customer List</h4>
            <a href="add_customer.php" class="btn btn-add-new">
                <i class="fas fa-user-plus mr-1"></i> Add New Customer
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">ID</th>
                            <th>Full Name</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th class="text-center" width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($link)) {
                            $query = "SELECT * FROM customers ORDER BY customer_id DESC";
                            $res = mysqli_query($link, $query);
                            
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $c_id = $row['customer_id'];
                                    $name = htmlspecialchars($row['full_name']);
                                    $email = htmlspecialchars($row['email']);
                                    $phone = htmlspecialchars($row['phone']);
                                    $addr = htmlspecialchars($row['address']);
                                    $initial = substr($name, 0, 1);

                                    echo "<tr>";
                                    echo "<td class='text-center'>{$c_id}</td>";
                                    echo "<td><div class='d-flex align-items-center'><div class='avatar-initial'>{$initial}</div><span class='font-weight-bold'>{$name}</span></div></td>";
                                    echo "<td><div><i class='fas fa-envelope text-gray-400 mr-1'></i> {$email}</div><div class='small text-muted'><i class='fas fa-phone text-gray-400 mr-1'></i> {$phone}</div></td>";
                                    echo "<td>{$addr}</td>";
                                    
                                    echo "<td class='text-center'>";
                                    
                                    // 1. NÚT TẠO GIAO DỊCH
                                    echo "<a href='create_contract.php?customer_id={$c_id}' class='btn btn-success btn-sm btn-action' title='Create Transaction'><i class='fas fa-file-invoice-dollar'></i> Deal</a>";

                                    // 2. NÚT SỬA THÔNG TIN (ĐÃ BỎ ĐIỀU KIỆN IF ADMIN)
                                    // Bây giờ Sale cũng thấy nút này
                                    echo "<a href='editcustomer.php?id={$c_id}' 
                                            class='btn btn-warning btn-sm btn-action text-white' 
                                            title='Edit Info'>
                                            <i class='fas fa-user-edit'></i> Edit
                                          </a>";
                                    
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4'>No customers found. Please add a new customer.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>