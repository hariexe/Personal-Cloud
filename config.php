<?php
$servername = "localhost";
$username = "drive_user";
$password = "your_password";
$dbname = "drive";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
