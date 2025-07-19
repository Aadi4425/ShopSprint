<?php
session_start();
include "../shared/connection.php";

if (!isset($_SESSION["userid"])) {
    echo "User not logged in!";
    die;
}

$userid = $_SESSION["userid"];
$membership = isset($_SESSION["plus_member"]) ? $_SESSION["plus_member"] : 0; // 0 for regular, 1 for Silver, 2 for Gold

$cart_query = "SELECT * FROM cart WHERE userid=$userid";
$cart_result = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_result) == 0) {
    echo "Cart is empty!";
    die;
}

// Calculate total order amount
$total_amount = 0;
while ($cart_item = mysqli_fetch_assoc($cart_result)) {
    $pid = $cart_item['pid'];
    $product_query = "SELECT price FROM product WHERE pid=$pid";
    $product_result = mysqli_query($conn, $product_query);

    // Check if the product query executed successfully
    if (!$product_result) {
        echo "Error fetching product details: " . mysqli_error($conn);
        die;
    }

    $product = mysqli_fetch_assoc($product_result);
    $total_amount += $product['price'];
}

// Apply discount based on membership level
$discount = 0;
if ($membership == 1) {
    // Silver member discount (5%)
    $discount = 0.05 * $total_amount;
    echo "5% discount applied. You saved Rs " . number_format($discount, 2) . ".<br>";
} elseif ($membership == 2) {
    // Gold member discount (10%)
    $discount = 0.10 * $total_amount;
    echo "10% discount applied. You saved Rs " . number_format($discount, 2) . ".<br>";
}

$total_amount -= $discount;

// Insert into orders table
$order_query = "INSERT INTO orders (userid, total_amount) VALUES ($userid, $total_amount)";
$order_status = mysqli_query($conn, $order_query);

if (!$order_status) {
    echo "Order failed: " . mysqli_error($conn);
    die;
}

$order_id = mysqli_insert_id($conn);

// Reset cart query pointer
mysqli_data_seek($cart_result, 0);

// Insert into order_items table
while ($cart_item = mysqli_fetch_assoc($cart_result)) {
    $pid = $cart_item['pid'];
    $product_query = "SELECT price FROM product WHERE pid=$pid";
    $product_result = mysqli_query($conn, $product_query);

    // Ensure product query succeeds
    if (!$product_result) {
        echo "Error fetching product details: " . mysqli_error($conn);
        die;
    }

    $product = mysqli_fetch_assoc($product_result);
    $price = $product['price'];
    
    $order_item_query = "INSERT INTO order_items (order_id, pid, quantity, price) VALUES ($order_id, $pid, 1, $price)";
    mysqli_query($conn, $order_item_query);
}

// Clear user's cart after order is placed
$clear_cart_query = "DELETE FROM cart WHERE userid=$userid";
mysqli_query($conn, $clear_cart_query);

// Redirect to order confirmation page or success message
header("Location: orderplaced.php?order_id=$order_id");
?>
