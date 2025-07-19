<?php
session_start();
include "../shared/connection.php";

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] != "Vendor") {
    echo "Unauthorized access!";
    die;
}

$vendor_id = $_SESSION["userid"]; // Assuming the vendor's ID is stored in 'userid' during login

// Fetch orders and their items for this vendor only
$sql = "
    SELECT 
        o.order_id, 
        o.order_date, 
        o.total_amount, 
        u.username, 
        oi.pid, 
        p.name AS product_name, 
        oi.quantity, 
        oi.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product p ON oi.pid = p.pid
    JOIN user u ON o.userid = u.userid
    WHERE p.owner = $vendor_id
    ORDER BY o.order_date DESC, o.order_id ASC
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "No orders found.";
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-back {
            background: linear-gradient(to right, #5e60ce, #7400b8);
            color: white;
            font-weight: 500;
            border-radius: 50px;
            padding: 0.5rem 1.3rem;
            text-decoration: none;
            transition: transform 0.2s ease, background 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-back:hover {
            background: linear-gradient(to right, #7400b8, #5e60ce);
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }

        .top-right {
            position: absolute;
            top: 20px;
            right: 30px;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Back button in top-right corner -->
    <div class="top-right">
        <a href="home.php" class="btn btn-back">← Back to Home</a>
    </div>

    <div class="container mt-5">
        <h2>Order Details</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (Rs)</th>
                    <th>Total Amount (Rs)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_order_id = null;
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($current_order_id != $row['order_id']) {
                        $current_order_id = $row['order_id'];
                        echo "<tr>
                                <td rowspan='1'>" . htmlspecialchars($row['order_id']) . "</td>
                                <td rowspan='1'>" . htmlspecialchars($row['order_date']) . "</td>
                                <td rowspan='1'>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['product_name']) . "</td>
                                <td>" . htmlspecialchars($row['quantity']) . "</td>
                                <td>" . htmlspecialchars($row['price']) . "</td>
                                <td rowspan='1'>" . htmlspecialchars($row['total_amount']) . "</td>
                            </tr>";
                    } else {
                        echo "<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>" . htmlspecialchars($row['product_name']) . "</td>
                                <td>" . htmlspecialchars($row['quantity']) . "</td>
                                <td>" . htmlspecialchars($row['price']) . "</td>
                                <td></td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
