<?php
$uname = $_POST['username'];
$upass = $_POST['password'];
$utype = $_POST['usertype'];

include "connection.php";

$sql_status = mysqli_query($conn, "INSERT INTO user (username, password, usertype) VALUES ('$uname', '$upass', '$utype')");

if ($sql_status) {
    echo "
    <script>
        alert('Signup successful');
        window.location.href = 'login.html';
    </script>";
} else {
    echo "Error while inserting: " . mysqli_error($conn);
}
?>
