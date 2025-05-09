<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["otp"])) {
    $userOTP = trim($_POST["otp"]);

    if (!isset($_SESSION["generatedOTP"])) {
        echo json_encode(["status" => "error", "message" => "Session expired. Please request a new OTP."]);
        exit();
    }

    $storedOTP = $_SESSION["generatedOTP"];

    if ((string) $userOTP === (string) $storedOTP) {
        unset($_SESSION["generatedOTP"]);
        echo json_encode(["status" => "success", "message" => "OTP verified"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid OTP. Please try again.",
        ]);
    }
}
?>