<?php
// MySQL server connection settings
$servername = "localhost";
$username = "root";
$password = "";
$database = "pro_db"; // ← your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to pro_db!";
?>
