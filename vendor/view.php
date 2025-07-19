<?php
session_start();
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== true || $_SESSION["usertype"] !== "Vendor") {
    echo "Unauthorized access.";
    die;
}
include "../shared/connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Products - ShopSprint</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #fdfbfb, #ebedee);
      padding: 30px 20px;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
    }

    .top-buttons {
      display: flex;
      gap: 16px;
    }

    .modern-btn {
      padding: 10px 24px;
      border-radius: 30px;
      border: none;
      background: linear-gradient(to right, #4a148c, #6a1b9a);
      color: white;
      font-weight: 500;
      font-size: 0.95rem;
      text-decoration: none;
      transition: 0.3s;
    }

    .modern-btn:hover {
      transform: scale(1.05);
      background: linear-gradient(to right, #6a1b9a, #4a148c);
    }

    .logout-btn {
      background: linear-gradient(to right, #ff1744, #d50000);
    }

    h2 {
      text-align: center;
      color: #2a003f;
      margin-bottom: 30px;
      font-size: 2rem;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 25px;
      max-width: 1200px;
      margin: auto;
    }

    .product-card {
      background: white;
      border-radius: 18px;
      padding: 18px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s;
    }

    .product-card:hover {
      transform: translateY(-6px);
    }

    .product-img {
      width: 100%;
      height: 240px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 12px;
    }

    .product-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #4a148c;
      margin-bottom: 8px;
    }

    .product-price {
      font-size: 1.1rem;
      color: #1a237e;
      margin-bottom: 10px;
    }

    .product-price::after {
      content: " ₹";
      font-size: 0.8rem;
      color: #333;
    }

    .product-detail {
      font-size: 0.95rem;
      color: #444;
      margin-bottom: 14px;
    }

    .btn-group {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .btn-modern {
      padding: 8px 14px;
      border-radius: 25px;
      border: none;
      font-size: 0.85rem;
      color: white;
      font-weight: 500;
      cursor: pointer;
      transition: 0.2s;
    }

    .btn-edit {
      background: linear-gradient(to right, #00c853, #64dd17);
    }

    .btn-delete {
      background: linear-gradient(to right, #ff1744, #d50000);
    }

    .btn-modern:hover {
      opacity: 0.9;
      transform: scale(1.03);
    }
  </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
  <div class="top-buttons">
    <a class="modern-btn" href="home.php">Add Product</a>
    <a class="modern-btn" href="view.php">View Products</a>
    <a class="modern-btn" href="cusorders.php">Your Orders</a>
  </div>
  <a class="modern-btn logout-btn" href="../shared/logout.php">Logout</a>
</div>

<h2>Your Uploaded Products</h2>

<div class="product-grid">
<?php
$userid = $_SESSION["userid"];
$sql_result = mysqli_query($conn, "SELECT * FROM product WHERE owner=$userid");

while ($dbrow = mysqli_fetch_assoc($sql_result)) {
    echo "
    <div class='product-card'>
        <img src='{$dbrow['impath']}' class='product-img' alt='Product Image'>
        <div class='product-name'>{$dbrow['name']}</div>
        <div class='product-price'>{$dbrow['price']}</div>
        <div class='product-detail'>{$dbrow['detail']}</div>
        <div class='btn-group'>
            <button class='btn-modern btn-edit'>Edit</button>
            <button class='btn-modern btn-delete'>Delete</button>
        </div>
    </div>";
}
?>
</div>

</body>
</html>
