<?php
$servername = "localhost";
$username = "root"; // Default username in XAMPP
$password = ""; // No password by default
$dbname = "user_system"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
