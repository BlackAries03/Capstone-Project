<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

$cID = $_POST["cID"];

$sql = "DELETE FROM chathistory WHERE cID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cID);
$stmt->execute();

$conn->close();

