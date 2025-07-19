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
    <title>Customer Home - ShopSprint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0c3fc, #8ec5fc);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 1rem 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            margin: 1rem 1rem 2rem 1rem;
        }

        .top-bar h2 {
            margin: 0;
            color: #2d0063;
        }

        .membership-btn {
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            background: linear-gradient(to right, #ff9a9e, #fad0c4);
            color: #4a148c;
        }

        .membership-btn.gold {
            background: linear-gradient(to right, #ffd700, #ffb347);
            color: #000;
        }

        .membership-btn.silver {
            background: linear-gradient(to right, #c0c0c0, #eeeeee);
            color: #000;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin: 0 1rem 2rem 1rem;
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

        .logout {
            position: fixed;
            top: 10px;
            right: 20px;
            z-index: 99;
        }

        .logout a {
            background: linear-gradient(to right, #fc466b, #3f5efb);
            color: white;
            padding: 8px 10px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-group-custom {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 1rem 2rem 1rem;
        }
    </style>
</head>
<body>
<div class="logout">
    <a href="../shared/logout.php">Logout</a>
</div>

<div class="container">
    <div class="top-bar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <?php
        $membership = $_SESSION["plus_member"] ?? 0;
        if ($membership == 1) {
            echo '<button class="membership-btn silver" disabled>Silver Member</button>';
        } elseif ($membership == 2) {
            echo '<button class="membership-btn gold" disabled>Gold Member</button>';
        } else {
            echo '<button class="membership-btn" disabled>Regular Member</button>';
        }
        ?>
    </div>

    <div class="btn-group-custom">
        <a href="home.php" class="btn btn-modern">View Products</a>
        <a href="viewcart.php" class="btn btn-modern">View Cart</a>
        <a href="viewpurchases.php" class="btn btn-modern">My Orders</a>
    </div>

    <div class="glass-card">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-8 position-relative">
                    <input type="text" class="form-control" id="search-input" placeholder="Search products..." name="search" value="<?php echo $_GET['search'] ?? ''; ?>" oninput="fetchSuggestions(this.value)">
                    <div id="suggestions" class="list-group position-absolute w-100" style="z-index:1000;"></div>
                </div>
                <div class="col-md-2">
                    <input type="range" name="price_range" min="0" max="1000000" step="1000" value="<?php echo $_GET['price_range'] ?? 650000; ?>" class="form-range" oninput="priceOutput.innerHTML = this.value + ' Rs'">
                    <div id="priceOutput"><?php echo $_GET['price_range'] ?? 650000; ?> Rs</div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-modern w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    <div class="row mt-4">
        <?php
        $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
        $max_price = intval($_GET['price_range'] ?? 650000);
        $result = mysqli_query($conn, "SELECT * FROM product WHERE name LIKE '%$search%' AND price <= $max_price");

        while ($row = mysqli_fetch_assoc($result)) {
            $price = $row['price'];
            if ($membership == 1) {
                $price *= 0.95;
            } elseif ($membership == 2) {
                $price *= 0.90;
            }

            echo "
            <div class='col-md-3'>
                <div class='product-card'>
                    <img src='" . htmlspecialchars($row['impath']) . "' alt='Product'>
                    <div class='name'>" . htmlspecialchars($row['name']) . "</div>
                    <div class='price'>" . number_format($price, 2) . " Rs</div>
                    <a href='addcart.php?pid=" . $row['pid'] . "'>
                        <button class='btn btn-modern mt-2'>Add to Cart</button>
                    </a>
                </div>
            </div>";
        }
        ?>
    </div>
</div>

<script>
function fetchSuggestions(query) {
    if (query.length < 2) {
        document.getElementById('suggestions').innerHTML = "";
        return;
    }

    fetch(`suggestions.php?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const suggestionBox = document.getElementById('suggestions');
            suggestionBox.innerHTML = "";
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'list-group-item list-group-item-action';
                div.textContent = item.name;
                div.onclick = function() {
                    document.getElementById('search-input').value = item.name;
                    suggestionBox.innerHTML = "";
                    document.querySelector('form').submit();
                }
                suggestionBox.appendChild(div);
            });
        });
}
</script>
</body>
</html>
