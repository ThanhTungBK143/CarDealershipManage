<?php
include "connection.php";
include "auth_check.php";
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale';

// 2. XỬ LÝ XÓA XE (Chỉ Admin mới được xóa)
if ($role === 'admin' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Sử dụng Prepared Statement để xóa an toàn
    $stmt = mysqli_prepare($link, "DELETE FROM cars WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Car deleted successfully!'); window.location='car.php';</script>";
    } else {
        echo "<script>alert('Error deleting car.'); window.location='car.php';</script>";
    }
    mysqli_stmt_close($stmt);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory - Car Management</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Navbar style */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        }
        .navbar-brand {
            color: #4e73df !important;
            font-weight: 800;
        }

        /* Card Container */
        .card-table {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-top: 30px;
            background-color: #fff;
            overflow: hidden;
        }

        .card-header {
            background-color: #4e73df;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .card-header h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        /* Table Styling */
        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e3e6f0;
            background-color: #f8f9fc;
            color: #5a5c69;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        
        .table tbody td {
            vertical-align: middle;
            color: #5a5c69;
            font-size: 0.95rem;
        }

        /* Action Buttons */
        .btn-action {
            margin: 0 2px;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        .badge-stock-low {
            background-color: #e74a3b;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .badge-stock-ok {
            background-color: #1cc88a;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            <i class="fas fa-arrow-left"></i> BACK TO DASHBOARD
        </a>
        <span class="navbar-text ml-auto">
            Logged in as: <b><?php echo $_SESSION['username']; ?></b> (<?php echo strtoupper($role); ?>)
        </span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    
    <div class="card card-table">
        <div class="card-header">
            <h4><i class="fas fa-car"></i> Cars Available in Store</h4>
            
            <?php if ($role === 'admin'): ?>
                <a href="addcar.php" class="btn btn-light btn-sm text-primary font-weight-bold">
                    <i class="fas fa-plus-circle"></i> ADD NEW CAR
                </a>
            <?php endif; ?>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Company / Make</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Color</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-right">Price</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-center">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($link)) {
                            // Truy vấn dữ liệu xe
                            $query = "SELECT * FROM cars ORDER BY product_id DESC";
                            $res = mysqli_query($link, $query);
                            
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $p_id = $row['product_id'];
                                    $make = htmlspecialchars($row['make']);
                                    $model = htmlspecialchars($row['model']);
                                    $year = $row['year'];
                                    $color = htmlspecialchars($row['color']);
                                    $qty = $row['quantity'];
                                    $price = $row['price'];

                                    echo "<tr>";
                                    echo "<td class='text-center'>{$p_id}</td>";
                                    echo "<td><strong>{$make}</strong></td>";
                                    echo "<td>{$model}</td>";
                                    echo "<td><span class='badge badge-secondary'>{$year}</span></td>";
                                    
                                    // Hiển thị màu sắc (có thể thêm chấm màu nếu muốn)
                                    echo "<td>{$color}</td>";
                                    
                                    // Logic hiển thị tồn kho
                                    echo "<td class='text-center'>";
                                    if ($qty < 5) {
                                        echo "<span class='badge-stock-low'>Low: {$qty}</span>";
                                    } else {
                                        echo "<span class='badge-stock-ok'>In Stock: {$qty}</span>";
                                    }
                                    echo "</td>";

                                    // Định dạng giá tiền
                                    $formatted_price = number_format($price, 2);
                                    echo "<td class='text-right'><strong>$ {$formatted_price}</strong></td>";
                                    
                                    // [QUAN TRỌNG] Chỉ Admin mới thấy các nút Edit/Delete
                                    if ($role === 'admin') {
                                        echo "<td class='text-center'>";
                                        
                                        // Nút Edit
                                        echo "<a href='editcar.php?id={$p_id}' class='btn btn-info btn-action' title='Edit'>
                                                <i class='fas fa-pencil-alt'></i>
                                              </a>";

                                        // Nút Delete
                                        echo "<a href='car.php?delete_id={$p_id}' 
                                                class='btn btn-danger btn-action' 
                                                title='Delete'
                                                onclick=\"return confirm('Are you sure you want to delete {$make} {$model}?');\">
                                                <i class='fas fa-trash'></i>
                                              </a>";
                                        
                                        echo "</td>";
                                    }
                                    
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4'>No cars found in database.</td></tr>";
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