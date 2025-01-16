<?php
session_start(); // Start the session at the beginning

// Step 1: Connect to the database
$servername = "localhost";
$username = "root";
$password = "Ravi1030#Mysql";
$dbname = "city_taxi";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the username and password from the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare and execute the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE Username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, fetch user_id and set session variable
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id']; // Set session user_id

        echo "success"; // Send success response
    } else {
        // User not found
        echo "fail";
    }

    $stmt->close();
}
$conn->close();
?>
