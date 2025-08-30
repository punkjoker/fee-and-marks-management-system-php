<?php
$host = "localhost";
$user = "root";   // change if you have another username
$pass = "";       // change if you set a password
$db   = "matendeni";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
?>
