<?php
session_start();

if (isset($_POST['verify'])) {
    $user_otp = $_POST['otp'];
    if ($user_otp == $_SESSION['otp']) {
        include "connection.php";

        
        $signup_data = $_SESSION['signup_data'];
        $uname = $signup_data['username'];
        $upass = $signup_data['password'];   
        $utype = $signup_data['usertype'];
        $email = $signup_data['email'];

        
        $sql_status = mysqli_query($conn, "INSERT INTO user (username, password, usertype, email) VALUES ('$uname', '$upass', '$utype', '$email')");

        if ($sql_status) {
            echo "<script>
                    alert('Signup successful');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            echo "Error while inserting: " . mysqli_error($conn);
        }

        unset($_SESSION['otp']);
        unset($_SESSION['signup_data']);
    } else {
        echo "<script>
                alert('Invalid OTP, please try again');
                window.location.href='verify_otp.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <form method="post" action="verify_otp.php" class="w-50 bg-warning p-4">
            <h4 class="text-center">Verify Your OTP</h4>
            <input required class="form-control mt-3" type="number" placeholder="Enter OTP" name="otp">
            <div class="text-center mt-3">
                <button type="submit" name="verify" class="btn btn-danger">Verify</button>
            </div>
        </form>
    </div>
</body>
</html>
