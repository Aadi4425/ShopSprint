<?php
session_start();
include "../shared/connection.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Ensure the order_id is passed in the URL
if (!isset($_GET['order_id'])) {
    echo "Order ID is missing!";
    die;
}

$order_id = $_GET['order_id'];
$userid = $_SESSION['userid'];
$membership = isset($_SESSION['plus_member']) ? $_SESSION['plus_member'] : 0; // 0 for regular, 1 for Silver, 2 for Gold

// Fetch order details
$order_query = "SELECT * FROM orders WHERE order_id=$order_id AND userid=$userid";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

// Fetch ordered items
$order_items_query = "SELECT oi.*, p.name FROM order_items oi JOIN product p ON oi.pid = p.pid WHERE oi.order_id=$order_id";
$order_items_result = mysqli_query($conn, $order_items_query);

// Fetch user email
$user_query = "SELECT email FROM user WHERE userid=$userid";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$user_email = $user['email'];

// Prepare bill content for email
$total_price = 0;
$order_details = "<h3>Order Details:</h3><table border='1' cellpadding='5'><tr><th>Product</th><th>Quantity</th><th>Price</th></tr>";

while ($item = mysqli_fetch_assoc($order_items_result)) {
    $total_price += $item['price'] * $item['quantity'];
    $order_details .= "<tr>
                            <td>{$item['name']}</td>
                            <td>{$item['quantity']}</td>
                            <td>{$item['price']}</td>
                       </tr>";
}

$order_details .= "</table><br><strong>Total Price: </strong>$total_price Rs";

// Apply discount based on membership level
$discount = 0;
if ($membership == 1) {
    // Silver member discount (5%)
    $discount = 0.05 * $total_price;
    $final_price = $total_price - $discount;
    $order_details .= "<br><strong>Silver Member Discount: </strong>5% (You saved Rs " . number_format($discount, 2) . ")<br>";
} elseif ($membership == 2) {
    // Gold member discount (10%)
    $discount = 0.10 * $total_price;
    $final_price = $total_price - $discount;
    $order_details .= "<br><strong>Gold Member Discount: </strong>10% (You saved Rs " . number_format($discount, 2) . ")<br>";
} else {
    // Regular user, no discount
    $final_price = $total_price;
}

$order_details .= "<strong>  Final Amount: </strong>$final_price Rs";

// Send email with the bill
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'oswalaadi@gmail.com'; // Your SMTP username
    $mail->Password = 'wcrz idhi ukrf paus'; // Your SMTP password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Email settings
    $mail->setFrom('oswalaadi@gmail.com', 'E-commerce Website');
    $mail->addAddress($user_email);  // Send to user's email

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Order Bill - Order ID: ' . $order_id;
    $mail->Body = "Thank you for your purchase! Here are your order details:<br>" . $order_details;

    $mail->send();
    echo "<h4>Order confirmation email sent successfully!</h4>";
} catch (Exception $e) {
    echo "<h4>Order email failed to send. Mailer Error: {$mail->ErrorInfo}</h4>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Order Placed Successfully</h1>
        <h3>Thank you for your purchase! A copy of the bill has been sent to your email.</h3>

        <div class="card">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <?php echo $order_details; ?>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <a href="home.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pbH6JHLY2TQ0kwV9fI5eoGCmYwP0lUJk5y3qJ5tj4+6l8vE5Hg3zzFmjzT0tnYER" crossorigin="anonymous"></script>
</body>
</html>
