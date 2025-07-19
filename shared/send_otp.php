<?php
session_start(); 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["usertype"]) && isset($_POST["email"])) {
    
    $otp = rand(100000, 999999);


    $_SESSION['otp'] = $otp;
    $_SESSION['signup_data'] = $_POST;

    // Email 
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '###########@gmail.com';
        $mail->Password = '############';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        
        $mail->setFrom('oswalaadi@gmail.com', 'Welcome to ShopSprint');
        $mail->addAddress($_POST["email"]);

        
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is <b>$otp</b>. Please use this code to complete your registration. Welcome Abroad";

        
        $mail->send();
        echo "<script>
                alert('OTP sent successfully');
                window.location.href='verify_otp.php';
              </script>";
    } catch (Exception $e) {
        echo "<script>
                alert('Error sending email: {$mail->ErrorInfo}');
              </script>";
    }
}
?>
