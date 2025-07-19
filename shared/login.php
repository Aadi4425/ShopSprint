<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$_SESSION["login_status"] = false;

// Connect to database
$conn = new mysqli("localhost", "root", "", "ecommerce", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$username = $conn->real_escape_string($_POST['username']);
$password = $conn->real_escape_string($_POST['password']);

// Run query
$sql = "SELECT * FROM user WHERE username='$username' AND password='$password' AND active_status=1";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    // Optional: redirect to login with error
    header("Location: ../login.html?error=invalid");
    exit;
}

// Success: fetch user
$user = $result->fetch_assoc();
$_SESSION["login_status"] = true;
$_SESSION["userid"] = $user["userid"];
$_SESSION["username"] = $user["username"];
$_SESSION["usertype"] = $user["usertype"];

// Handle membership if Customer
if ($user["usertype"] == "Customer") {
    $userid = $user["userid"];
    $countResult = $conn->query("SELECT COUNT(*) AS order_count FROM orders WHERE userid = $userid");
    $order_count = $countResult->fetch_assoc()["order_count"];

    if ($order_count >= 2 && $order_count <= 4) {
        if ($user["plus_member"] != 1) {
            $conn->query("UPDATE user SET plus_member = 1 WHERE userid = $userid");
        }
        $_SESSION["plus_member"] = 1;
    } elseif ($order_count > 4) {
        if ($user["plus_member"] != 2) {
            $conn->query("UPDATE user SET plus_member = 2 WHERE userid = $userid");
        }
        $_SESSION["plus_member"] = 2;
    } else {
        $_SESSION["plus_member"] = 0;
    }

    header("Location: ../customer/home.php");
    exit;
}

// Redirect based on role
if ($user["usertype"] == "Vendor") {
    header("Location: ../vendor/home.php");
    exit;
} elseif ($user["usertype"] == "Admin") {
    header("Location: ../admin/home.php");
    exit;
}
