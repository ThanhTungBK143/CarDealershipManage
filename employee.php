<?php
include "connection.php";
include "auth_check.php";

// 2. CHECK ADMIN PERMISSION
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
if ($role !== 'admin') {
    echo "<script>alert('Access Denied!'); window.location='homepage.php';</script>";
    exit();
}

// 3. DELETE LOGIC (UPDATED: Cannot delete Admin)
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    
    // Check role of the target user before deleting
    $check_query = mysqli_query($link, "SELECT role FROM users WHERE id = $del_id");
    $target = mysqli_fetch_assoc($check_query);
    
    if ($target && strtolower($target['role']) === 'admin') {
        echo "<script>alert('ERROR: You cannot delete another Administrator account!'); window.location='employee.php';</script>";
    } else {
        // Safe to delete (It's a Sale)
        mysqli_query($link, "DELETE FROM users WHERE id = $del_id");
        echo "<script>alert('Employee deleted successfully!'); window.location='employee.php';</script>";
    }
    exit();
}

// Sorting logic
$sort_order = "DESC"; 
$active_filter = "desc";
if (isset($_GET['sort']) && $_GET['sort'] == 'asc') {
    $sort_order = "ASC";
    $active_filter = "asc";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Management</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); background-color: #fff; overflow: hidden; }
        .card-header { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .card-header h4 { margin: 0; font-weight: 700; }
        .img-profile { height: 40px; width: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #eaecf4; }
        .table thead th { border-top: none; border-bottom: 2px solid #e3e6f0; background-color: #f8f9fc; color: #5a5c69; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; vertical-align: middle; }
        .table tbody td { vertical-align: middle; color: #5a5c69; font-size: 0.95rem; }
        
        .badge-role-admin { background-color: #4e73df; color: white; padding: 5px 10px; border-radius: 10px; }
        .badge-role-sale { background-color: #f6c23e; color: white; padding: 5px 10px; border-radius: 10px; }
        .badge-sales-count { font-size: 1rem; padding: 8px 15px; border-radius: 30px; background-color: #e3f2fd; color: #4e73df; font-weight: 800; }

        .btn-sort { background-color: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); font-size: 0.9rem; }
        .btn-sort:hover { background-color: rgba(255,255,255,0.4); color: white; text-decoration: none; }
        .btn-sort.active { background-color: white; color: #13855c; font-weight: bold; }
        .btn-add-emp { background-color: white; color: #13855c; font-weight: 800; border-radius: 50px; padding: 8px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-add-emp:hover { transform: translateY(-2px); text-decoration: none; color: #0e6645; box-shadow: 0 6px 8px rgba(0,0,0,0.15); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php"><i class="fas fa-arrow-left mr-2"></i> BACK TO DASHBOARD</a>
        <span class="navbar-text ml-auto">Admin Area: <b><?php echo $_SESSION['username']; ?></b></span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    
    <div class="card card-table">
        <div class="card-header">
            <div>
                <h4><i class="fas fa-user-tie mr-2"></i> Employee Management</h4>
                <small>View staff details & performance</small>
            </div>
            <div class="d-flex align-items-center">
                <a href="add_employee.php" class="btn btn-add-emp mr-3"><i class="fas fa-plus-circle mr-1"></i> Add Employee</a>
                <div class="btn-group">
                    <span class="mr-3 align-self-center font-weight-bold d-none d-md-block">Sort:</span>
                    <a href="employee.php?sort=asc" class="btn btn-sort <?php echo ($active_filter == 'asc') ? 'active' : ''; ?>"><i class="fas fa-sort-amount-up"></i></a>
                    <a href="employee.php?sort=desc" class="btn btn-sort <?php echo ($active_filter == 'desc') ? 'active' : ''; ?>"><i class="fas fa-sort-amount-down"></i></a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Employee Info</th>
                            <th>Contact</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Signed Contracts</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($link)) {
                            $query = "SELECT u.id, u.username, u.email, u.phone, u.role, COUNT(st.transaction_id) as total_contracts
                                      FROM users u LEFT JOIN sales_transactions st ON u.id = st.sales_user_id
                                      GROUP BY u.id ORDER BY total_contracts $sort_order";
                            $res = mysqli_query($link, $query);
                            
                            if (mysqli_num_rows($res) > 0) {
                                $count = 1;
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $user_id = $row['id'];
                                    $u_name = htmlspecialchars($row['username']);
                                    $u_role = strtolower($row['role']);
                                    // ... other vars ...

                                    echo "<tr>";
                                    echo "<td class='text-center'>{$count}</td>";
                                    echo "<td>
                                            <div class='d-flex align-items-center'>
                                                <img class='img-profile mr-3' src='https://ui-avatars.com/api/?name={$u_name}&background=random'>
                                                <div>
                                                    <div class='font-weight-bold text-primary'>{$u_name}</div>
                                                    <small class='text-muted'>ID: {$user_id}</small>
                                                </div>
                                            </div>
                                          </td>";
                                    echo "<td><div>{$row['email']}</div><div class='small text-muted'>{$row['phone']}</div></td>";
                                    
                                    echo "<td class='text-center'>";
                                    if ($u_role == 'admin') echo "<span class='badge badge-role-admin'>ADMIN</span>";
                                    else echo "<span class='badge badge-role-sale'>SALE STAFF</span>";
                                    echo "</td>";
                                    
                                    echo "<td class='text-center'><span class='badge-sales-count'>{$row['total_contracts']}</span></td>";
                                    
                                    echo "<td class='text-center'>";
                                    echo "<a href='edituser.php?id={$user_id}' class='btn btn-info btn-sm shadow-sm mr-1'><i class='fas fa-user-edit'></i></a>";
                                    
                                    // [LOGIC MỚI] Nút Delete
                                    // Nếu dòng này là Admin -> Disable nút xóa
                                    if ($u_role === 'admin') {
                                        echo "<button class='btn btn-secondary btn-sm shadow-sm' disabled title='Cannot delete Admin'><i class='fas fa-trash'></i></button>";
                                    } else {
                                        // Nếu là Sale -> Cho phép xóa
                                        echo "<a href='employee.php?delete_id={$user_id}' 
                                                 class='btn btn-danger btn-sm shadow-sm' 
                                                 onclick=\"return confirm('Are you sure you want to delete this employee?');\">
                                                 <i class='fas fa-trash'></i>
                                              </a>";
                                    }
                                    echo "</td>";
                                    
                                    echo "</tr>";
                                    $count++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4'>No employees found.</td></tr>";
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