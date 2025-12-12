<?php
include "connection.php";
include "auth_check.php";
$sales_user_id = isset($_SESSION['user_id_from_db']) ? $_SESSION['user_id_from_db'] : 1; 

// 2. GET CUSTOMER INFO
if (!isset($_GET['customer_id']) && !isset($_POST['customer_id'])) {
    echo "<script>alert('Please select a customer from the list!'); window.location='customer.php';</script>";
    exit();
}

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : intval($_POST['customer_id']);

// Get customer name
$cust_res = mysqli_query($link, "SELECT full_name FROM customers WHERE customer_id = $customer_id");
$cust_row = mysqli_fetch_assoc($cust_res);
$customer_name = $cust_row['full_name'];

$message = '';
$message_type = '';

// 3. SAVE CONTRACT LOGIC
if (isset($_POST['save_contract'])) {
    $product_id = intval($_POST['product_id']);
    $qty_to_sell = intval($_POST['quantity']);
    
    // A. Check Stock
    $check_stock = mysqli_query($link, "SELECT quantity, make, model FROM cars WHERE product_id = $product_id");
    $car_data = mysqli_fetch_assoc($check_stock);
    $current_stock = $car_data['quantity'];

    if ($qty_to_sell > $current_stock) {
        $message = "Error: Stock only has <b>$current_stock</b> cars left. Not enough to sell.";
        $message_type = 'danger';
    } elseif ($qty_to_sell <= 0) {
        $message = "Quantity must be greater than 0.";
        $message_type = 'danger';
    } else {
        // B. Start Transaction
        mysqli_begin_transaction($link);
        try {
            $date = date('Y-m-d H:i:s');
            
            // B1. Save to sales_transactions
            $insert_query = "INSERT INTO sales_transactions (customer_id, product_id, sales_user_id, quantity, transaction_date) 
                             VALUES ('$customer_id', '$product_id', '$sales_user_id', '$qty_to_sell', '$date')";
            
            if (!mysqli_query($link, $insert_query)) {
                throw new Exception("Error creating transaction: " . mysqli_error($link));
            }
            
            // B2. Deduct Stock
            $new_stock = $current_stock - $qty_to_sell;
            $update_stock = "UPDATE cars SET quantity = '$new_stock' WHERE product_id = '$product_id'";
            
            if (!mysqli_query($link, $update_stock)) {
                throw new Exception("Error updating stock: " . mysqli_error($link));
            }

            mysqli_commit($link);
            $message = "✅ Transaction completed successfully!";
            $message_type = 'success';

        } catch (Exception $e) {
            mysqli_rollback($link);
            $message = "Failed: " . $e->getMessage();
            $message_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create New Sale</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); margin-top: 80px; }
        .card-header-custom {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white; padding: 20px; font-weight: 700; text-align: center; border-radius: 15px 15px 0 0;
        }
        .btn-submit {
            background-color: #4e73df; color: white; border-radius: 50px; padding: 12px; font-weight: bold; width: 100%; border: none;
        }
        .btn-submit:hover { background-color: #2e59d9; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4); color: white;}
        .total-box { background: #eaecf4; padding: 15px; text-align: center; border-radius: 10px; font-weight: bold; font-size: 1.2rem; color: #4e73df; margin-top: 15px;}
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> shadow-sm text-center">
                    <h4><?php echo $message; ?></h4>
                    <?php if ($message_type == 'success'): ?>
                        <hr>
                        <a href="customer.php" class="btn btn-secondary mt-2">Go Back to Customers</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($message_type != 'success'): ?>
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> CREATE NEW SALE
                </div>
                <div class="card-body p-4">
                    <form action="" method="post">
                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

                        <div class="form-group">
                            <label class="font-weight-bold">Customer</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($customer_name); ?>" readonly style="background-color: #fff3cd;">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Select Car</label>
                            <select name="product_id" id="product_select" class="form-control" required onchange="calcTotal()">
                                <option value="" data-price="0">-- Select available car --</option>
                                <?php
                                $cars = mysqli_query($link, "SELECT * FROM cars WHERE quantity > 0");
                                while($c = mysqli_fetch_assoc($cars)){
                                    echo "<option value='{$c['product_id']}' data-price='{$c['price']}'>
                                            {$c['make']} {$c['model']} ({$c['year']}) {$c['color']} - Stock: {$c['quantity']}
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Quantity</label>
                            <input type="number" name="quantity" id="qty" class="form-control" value="1" min="1" required oninput="calcTotal()">
                        </div>

                        <div class="total-box">
                            Total Amount: <span id="total_display">$0.00</span>
                        </div>

                        <hr class="my-4">
                        <div class="row">
                            <div class="col-4">
                                <a href="customer.php" class="btn btn-secondary btn-block" style="border-radius: 50px; padding: 12px;">Cancel</a>
                            </div>
                            <div class="col-8">
                                <button type="submit" name="save_contract" class="btn btn-submit">
                                    <i class="fas fa-check mr-2"></i> CONFIRM SALE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function calcTotal() {
        var sel = document.getElementById("product_select");
        var price = parseFloat(sel.options[sel.selectedIndex].getAttribute("data-price")) || 0;
        var qty = parseInt(document.getElementById("qty").value) || 0;
        var total = price * qty;
        
        // Format Currency
        document.getElementById("total_display").innerText = "$" + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
</script>

</body>
</html>