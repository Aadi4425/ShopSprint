<?php
session_start();
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] == false) {
    echo "Unauthorized Attempt";
    die;
}


include "../shared/connection.php";

$userid = $_SESSION["userid"];
$sql = "
    SELECT oi.order_item_id, oi.quantity, oi.price, o.order_date, 
           p.name AS product_name, p.impath AS product_image 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN product p ON oi.pid = p.pid
    WHERE o.userid = $userid
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>My Orders</title>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>My Orders</h2>
            <a href="home.php" class="btn btn-secondary">Back</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td><img src='".htmlspecialchars($row['product_image'])."' width='100'></td>
                            <td>".htmlspecialchars($row['product_name'])."</td>
                            <td>".htmlspecialchars($row['quantity'])."</td>
                            <td>".htmlspecialchars($row['price'])." Rs</td>
                            <td>".htmlspecialchars($row['order_date'])."</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
