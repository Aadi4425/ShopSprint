<?php
session_start();
include "../shared/connection.php";

if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] == false) {
    echo "Unauthorized Attempt";
    die;
}

if ($_SESSION["usertype"] != "Admin") {
    echo "Forbidden access type";
    die;
}

// Check if a delete request has been made
if (isset($_GET['delete_userid'])) {
    $delete_userid = $_GET['delete_userid'];

    // Set active_status to 0 to deactivate the user
    $delete_query = "UPDATE user SET active_status = 0 WHERE userid = $delete_userid";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "<script>alert('User deactivated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to deactivate user.');</script>";
    }
}

// Fetch users' data: total orders and classify based on usertype
$user_query = "
    SELECT u.userid, u.username, u.usertype, COUNT(o.order_id) AS order_count
    FROM user u
    LEFT JOIN orders o ON u.userid = o.userid
    WHERE u.active_status = 1  -- Fetch only active users
    GROUP BY u.userid
";
$user_result = mysqli_query($conn, $user_query);

$users_data = [];
while ($row = mysqli_fetch_assoc($user_result)) {
    $users_data[] = [
        'userid' => $row['userid'],
        'name' => $row['username'],
        'usertype' => $row['usertype'],
        'order_count' => $row['order_count']
    ];
}

// Classify users into Gold, Silver, Regular, and Vendors, and exclude Admin
$gold_members = [];
$silver_members = [];
$regular_members = [];
$vendors = [];

foreach ($users_data as $user) {
    if ($user['usertype'] == 'Admin') {
        continue;  // Exclude Admin users
    } elseif ($user['usertype'] == 'Vendor') {
         $vendors[] = $user;  // Classify as vendor if usertype is 'Vendor'
    } elseif ($user['order_count'] >= 5) {
        $gold_members[] = $user;
    } elseif ($user['order_count'] >= 2 && $user['order_count'] <= 4) {
        $silver_members[] = $user;
    } else {
        $regular_members[] = $user;
    }
}

// Fetch orders for a specific user if 'userid' is clicked
if (isset($_GET['userid'])) {
    $clicked_userid = $_GET['userid'];
    $orders_query = "SELECT * FROM orders WHERE userid=$clicked_userid";
    $orders_result = mysqli_query($conn, $orders_query);
    $orders = mysqli_fetch_all($orders_result, MYSQLI_ASSOC);
}

// Fetch orders for a specific vendor if 'vendorid' is clicked
if (isset($_GET['vendorid'])) {
    $clicked_vendorid = $_GET['vendorid'];
    $clicked_vendor = array_filter($vendors, function($vendor) use ($clicked_vendorid) {
        return $vendor['userid'] == $clicked_vendorid;
    });
    $clicked_vendor = reset($clicked_vendor); // Get the first matching vendor

    // Fetch products for the clicked vendor
    $products_query = "SELECT p.pid, p.name, p.price FROM product p WHERE p.owner = {$clicked_vendor['userid']}";
    $products_result = mysqli_query($conn, $products_query);
    $vendor_products = mysqli_fetch_all($products_result, MYSQLI_ASSOC);

    // Fetch orders for the clicked vendor
    $orders_query = "
        SELECT o.order_id, o.total_amount, o.order_date, oi.quantity, p.name
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.pid = p.pid
        WHERE p.owner = {$clicked_vendor['userid']}
    ";
    $orders_result = mysqli_query($conn, $orders_query);
    $vendor_orders = mysqli_fetch_all($orders_result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Admin Dashboard</h1>

        <!-- Logout button -->
        <div class="text-end mb-3">
            <a href="../shared/logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="row">
            <!-- Vendors Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Vendors</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($vendors as $vendor): ?>
                            <li class="list-group-item">
                                <a href="home.php?vendorid=<?php echo $vendor['userid']; ?>" class="vendor-link" data-target="#vendor-products-section">
                                    <strong><?php echo $vendor['name']; ?></strong> (Vendor)
                                </a>
                                <a href="home.php?delete_userid=<?php echo $vendor['userid']; ?>" class="btn btn-danger btn-sm float-end">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Gold Members Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h3 class="card-title">Gold Members</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($gold_members as $member): ?>
                            <li class="list-group-item">
                                <a href="home.php?userid=<?php echo $member['userid']; ?>#orders-section"><?php echo $member['name']; ?></a>
                                <span class="badge bg-success float-end"><?php echo $member['order_count']; ?> Orders</span>
                                <a href="home.php?delete_userid=<?php echo $member['userid']; ?>" class="btn btn-danger btn-sm float-end me-2">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Silver Members Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="card-title">Silver Members</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($silver_members as $member): ?>
                            <li class="list-group-item">
                                <a href="home.php?userid=<?php echo $member['userid']; ?>#orders-section"><?php echo $member['name']; ?></a>
                                <span class="badge bg-info float-end"><?php echo $member['order_count']; ?> Orders</span>
                                <a href="home.php?delete_userid=<?php echo $member['userid']; ?>" class="btn btn-danger btn-sm float-end me-2">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Regular Members Section -->
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title">Regular Members</h3>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($regular_members as $member): ?>
                    <li class="list-group-item">
                        <a href="home.php?userid=<?php echo $member['userid']; ?>#orders-section"><?php echo $member['name']; ?></a>
                        <span class="badge bg-secondary float-end"><?php echo $member['order_count']; ?> Orders</span>
                        <a href="home.php?delete_userid=<?php echo $member['userid']; ?>" class="btn btn-danger btn-sm float-end me-2">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Display Orders for the Selected User -->
        <?php if (isset($orders)): ?>
            <div id="orders-section" class="mt-5">
                <h3>Orders for User ID: <?php echo $clicked_userid; ?></h3>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['total_amount']; ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Display Products and Orders for the Selected Vendor -->
        <?php if (isset($vendor_products) && isset($vendor_orders)): ?>
            <div id="vendor-products-section" style="max-height: 400px; overflow-y: auto;">
    <h3>Products for Vendor: <?php echo $clicked_vendor['name']; ?></h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($vendor_products as $product): ?>
            <tr>
                <td><?php echo $product['pid']; ?></td>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['price']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


                <h3>Orders for Vendor: <?php echo $clicked_vendor['name']; ?></h3>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vendor_orders as $vendor_order): ?>
                        <tr>
                            <td><?php echo $vendor_order['order_id']; ?></td>
                            <td><?php echo $vendor_order['name']; ?></td>
                            <td><?php echo $vendor_order['quantity']; ?></td>
                            <td><?php echo $vendor_order['total_amount']; ?></td>
                            <td><?php echo $vendor_order['order_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

