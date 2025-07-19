<?php
session_start();
if (!isset($_SESSION["login_status"])) {
    echo "Login Failed";
    die;
}
if ($_SESSION["login_status"] == false) {
    echo "Unauthorized Attempt";
    die;
}
if ($_SESSION["usertype"] != "Customer") {
    echo "Forbidden access type";
    die;
}
include "../shared/connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Cart - ShopSprint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0c3fc, #8ec5fc);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .header-buttons {
            display: flex;
            justify-content: start;
            align-items: center;
            gap: 15px;
            padding: 20px;
        }

        .header-buttons a {
            background: linear-gradient(to right, #36d1dc, #5b86e5);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease-in-out;
        }

        .header-buttons a:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .logout {
            position: fixed;
            top: 20px;
            right: 120px;
            z-index: 99;
        }

        .logout a {
            background: linear-gradient(to right, #fc466b, #3f5efb);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .product-card {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: contain;
            object-position: center;
            border-radius: 10px;
        }

        .product-card .name {
            font-weight: 600;
            color: #2e005c;
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .product-card .price {
            font-weight: bold;
            font-size: 1.1rem;
            color: #5c0a0a;
        }

        .btn-modern {
            background: linear-gradient(to right, #8360c3, #2ebf91);
            border: none;
            color: white;
            font-weight: 500;
            border-radius: 30px;
            padding: 8px 18px;
            transition: all 0.3s ease-in-out;
        }

        .btn-modern:hover {
            transform: scale(1.05);
            opacity: 0.95;
        }

        .place-order {
            text-align: center;
            margin: 40px 0 20px 0;
        }
    </style>
</head>
<body>
<div class="logout">
    <a href="../shared/logout.php">Logout</a>
</div>

<div class="header-buttons">
    <a href="home.php">View Products</a>
    <a href="viewcart.php">View Cart</a>
    <a href="viewpurchases.php">My Orders</a>
</div>

<div class="container mt-3">
    <div class="row">
        <?php
        $sql_result = mysqli_query($conn, "SELECT * FROM cart JOIN product ON cart.pid=product.pid WHERE cart.userid=$_SESSION[userid]");
        $total = 0;
        while ($dbrow = mysqli_fetch_assoc($sql_result)) {
            $total += $dbrow['price'];
            echo "<div class='col-md-3'>
                    <div class='product-card'>
                        <img src='" . htmlspecialchars($dbrow['impath']) . "' alt='Product Image'>
                        <div class='name'>" . htmlspecialchars($dbrow['name']) . "</div>
                        <div class='price'>" . number_format($dbrow['price'], 2) . " Rs</div>
                        <div class='detail'>" . htmlspecialchars($dbrow['detail']) . "</div>
                        <div class='mt-2'>
                            <a href='deletecart.php?cartid=" . $dbrow['cartid'] . "'>
                                <button class='btn btn-modern'>Remove from Cart</button>
                            </a>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </div>

    <div class="place-order">
        <a href='order.php'>
            <button class='btn btn-modern btn-lg'>Place Order ₹<?php echo number_format($total, 2); ?></button>
        </a>
    </div>
</div>
</body>
</html>