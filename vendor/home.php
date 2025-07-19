<?php
session_start();
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] === false || $_SESSION["usertype"] !== "Vendor") {
    echo "Unauthorized Access";
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Vendor Home - ShopSprint</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e0c3fc, #8ec5fc);
      min-height: 100vh;
      padding-bottom: 50px;
    }

    .navbar {
      display: flex;
      justify-content: flex-end;
      gap: 2rem;
      padding: 1rem 3rem;
      background: #ffffffaa;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .navbar form {
      margin: 0;
    }

    .navbar button {
      padding: 10px 24px;
      border: none;
      border-radius: 30px;
      background: linear-gradient(to right, #00bcd4, #3f51b5);
      color: white;
      font-weight: 500;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .navbar button:hover {
      transform: scale(1.05);
    }

    .main-content {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 60px;
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.25);
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      margin-top: 40px;
      backdrop-filter: blur(12px);
    }

    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 0.75rem;
      margin-top: 1rem;
      border-radius: 12px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    .form-container label {
      font-weight: 500;
      margin-top: 1rem;
      display: block;
      color: #333;
    }

    .form-container button {
      margin-top: 2rem;
      padding: 0.75rem 2rem;
      border: none;
      border-radius: 30px;
      background: linear-gradient(to right, #8360c3, #2ebf91);
      color: white;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
    }

    .form-container button:hover {
      transform: scale(1.03);
      opacity: 0.95;
    }
  </style>
</head>
<body>

  <!-- Modern Navbar Buttons -->
  <div class="navbar">
    <form action="home.php" method="get"><button type="submit">Add Product</button></form>
    <form action="view.php" method="get"><button type="submit">View Product</button></form>
    <form action="orders.php" method="get"><button type="submit">View Orders</button></form>
    <form action="../shared/logout.php" method="post"><button type="submit" style="background: linear-gradient(to right, #fc466b, #3f5efb);">Logout</button></form>
  </div>

  <!-- Add Product Card -->
  <div class="main-content">
    <form class="form-container" action="upload.php" method="post" enctype="multipart/form-data">
      <label for="name">Product name</label>
      <input type="text" name="name" id="name" required placeholder="Enter product name">

      <label for="price">Product price</label>
      <input type="number" name="price" id="price" required placeholder="Enter product price">

      <label for="detail">Product Description</label>
      <textarea name="detail" id="detail" rows="4" placeholder="Describe your product" required></textarea>

      <label for="pdtimg">Upload Product Image</label>
      <input type="file" name="pdtimg" id="pdtimg" accept=".jpg,.png" required>

      <button type="submit">Upload Product</button>
    </form>
  </div>

</body>
</html>