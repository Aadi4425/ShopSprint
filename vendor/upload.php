<?php
session_start();

// Check if user is logged in and form is submitted
if (!isset($_SESSION["userid"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Unauthorized or invalid access.";
    die;
}

// Validate all inputs
if (
    !isset($_FILES["pdtimg"]) || $_FILES["pdtimg"]["error"] !== 0 ||
    !isset($_POST["name"]) || !isset($_POST["price"]) || !isset($_POST["detail"])
) {
    echo "Missing product details or image.";
    die;
}

$conn = new mysqli("localhost", "root", "", "ecommerce", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Save image
$source_path = $_FILES["pdtimg"]["tmp_name"];
$image_name = basename($_FILES["pdtimg"]["name"]);
$target_path = "../shared/images/" . $image_name;

// Move uploaded file
if (!move_uploaded_file($source_path, $target_path)) {
    echo "Failed to upload image.";
    die;
}

// Escape user input to prevent SQL injection
$name = $conn->real_escape_string($_POST["name"]);
$price = (float)$_POST["price"];
$detail = $conn->real_escape_string($_POST["detail"]);
$owner = (int)$_SESSION["userid"];
$imagePathDB = $conn->real_escape_string($target_path);

// Insert product into DB
$que = "INSERT INTO product(name, price, detail, impath, owner) 
        VALUES ('$name', $price, '$detail', '$imagePathDB', $owner)";

if (mysqli_query($conn, $que)) {
    header("Location: view.php");
} else {
    echo "Error adding product: " . mysqli_error($conn);
}
?>
