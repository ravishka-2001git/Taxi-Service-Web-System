<?php
$servername = "localhost";
$username = "root";
$password = "Ravi1030#Mysql"; // replace with your MySQL root password, if any
$dbname = "city_taxi"; // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

