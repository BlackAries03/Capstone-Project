<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMail/PHPMailer.php';
require 'PHPMail/SMTP.php';
require 'PHPMail/Exception.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit();
    }

    $otp = rand(100000, 999999);
    $_SESSION["generatedOTP"] = $otp;

    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wkamloke@gmail.com';
        $mail->Password = 'wgro vtct puxp cfru';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('wkamloke@gmail.com', 'Instakilogram');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body = "Your OTP is: $otp\n\nUse this OTP to reset your password.";

        if ($mail->send()) {
            echo json_encode(["status" => "success", "message" => "OTP sent"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP"]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
    }
}