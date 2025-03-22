<?php
$servername = "localhost"; // Change if using a remote server
$username = "efficient_db"; // Change to your database username
$password = "Q&mpf2.Q*9!6"; // Change to your database password
$database = "efficient_db"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
