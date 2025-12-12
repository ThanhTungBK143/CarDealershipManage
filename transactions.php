<?php
include "connection.php";
include "auth_check.php";

// Lấy thông tin Role và User ID
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'sale';
$current_user_id = isset($_SESSION['user_id_from_db']) ? intval($_SESSION['user_id_from_db']) : 0;

// [ĐÃ XÓA] Đoạn code chặn quyền truy cập của Sale
// Bây giờ cả Admin và Sale đều vào được trang này.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lịch Sử Giao Dịch</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; color: #5a5c69; }
        .navbar-custom { background-color: #fff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .navbar-brand { color: #4e73df !important; font-weight: 800; }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); background-color: #fff; overflow: hidden; margin-top: 30px; }
        .card-header {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white; padding: 20px;
        }
        .card-header h4 { margin: 0; font-weight: 700; }
        .table thead th { border-top: none; border-bottom: 2px solid #e3e6f0; background-color: #f8f9fc; color: #5a5c69; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; }
        .table tbody td { vertical-align: middle; color: #5a5c69; font-size: 0.95rem; }
        .badge-sale-person { background-color: #36b9cc; color: white; padding: 5px 10px; border-radius: 10px; font-size: 0.8rem; }
        .text-price { color: #1cc88a; font-weight: 800; }
        .total-row { background-color: #f8f9fc; font-weight: bold; color: #e74a3b; font-size: 1.1rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            <i class="fas fa-arrow-left mr-2"></i> BACK TO DASHBOARD
        </a>
        <span class="navbar-text ml-auto">
            User: <b><?php echo $_SESSION['username']; ?></b> (<?php echo strtoupper($role); ?>)
        </span>
    </div>
</nav>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    
    <div class="card card-table">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-file-invoice-dollar mr-2"></i> Sales Transaction History</h4>
                <small>
                    <?php 
                        if($role === 'admin') echo "Viewing All Records"; 
                        else echo "Viewing Your Personal Sales Records"; 
                    ?>
                </small>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Car Details</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total</th>
                            <th>Sales Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($link)) {
                            // 1. CÂU QUERY CƠ BẢN
                            $sql = "
                                SELECT 
                                    st.transaction_id, 
                                    st.transaction_date, 
                                    st.quantity as sold_qty,
                                    c.full_name as customer_name,
                                    car.make, car.model, car.year, car.price,
                                    u.username as sales_person
                                FROM sales_transactions st
                                JOIN customers c ON st.customer_id = c.customer_id
                                JOIN cars car ON st.product_id = car.product_id
                                JOIN users u ON st.sales_user_id = u.id
                            ";

                            // 2. LOGIC PHÂN QUYỀN (QUAN TRỌNG)
                            // Nếu KHÔNG phải Admin -> Chỉ lấy dòng nào có sales_user_id trùng với ID người đang đăng nhập
                            if ($role !== 'admin') {
                                $sql .= " WHERE st.sales_user_id = $current_user_id ";
                            }

                            // 3. Sắp xếp giảm dần theo ngày
                            $sql .= " ORDER BY st.transaction_date DESC";
                            
                            $res = mysqli_query($link, $sql);
                            $grand_total = 0;

                            if ($res && mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $t_id = $row['transaction_id'];
                                    $date = date("d/m/Y H:i", strtotime($row['transaction_date']));
                                    $cust = htmlspecialchars($row['customer_name']);
                                    $car_info = "{$row['make']} {$row['model']} ({$row['year']})";
                                    $qty = isset($row['sold_qty']) ? $row['sold_qty'] : 1; 
                                    $price = $row['price'];
                                    $total_row = $price * $qty;
                                    $grand_total += $total_row;

                                    echo "<tr>";
                                    echo "<td class='text-center'>{$t_id}</td>";
                                    echo "<td>{$date}</td>";
                                    echo "<td><b>{$cust}</b></td>";
                                    echo "<td>{$car_info}</td>";
                                    echo "<td class='text-center'><span class='badge badge-light border'>{$qty}</span></td>";
                                    echo "<td class='text-right text-muted'>$" . number_format($price, 2) . "</td>";
                                    echo "<td class='text-right text-price'>$" . number_format($total_row, 2) . "</td>";
                                    echo "<td><span class='badge badge-sale-person'><i class='fas fa-user-tag mr-1'></i> {$row['sales_person']}</span></td>";
                                    echo "</tr>";
                                }
                                
                                echo "<tr class='total-row'>";
                                echo "<td colspan='6' class='text-right'>TOTAL REVENUE:</td>";
                                echo "<td class='text-right'>$" . number_format($grand_total, 2) . "</td>";
                                echo "<td></td>";
                                echo "</tr>";

                            } else {
                                echo "<tr><td colspan='8' class='text-center py-5'>No transactions found.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small text-center">
            * Revenue is calculated based on the current price of the car model.
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>