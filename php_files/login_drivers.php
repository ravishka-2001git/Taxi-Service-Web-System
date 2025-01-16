<?php
session_start();
include('db_connection.php'); // Adjust path as needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT driver_id, full_name FROM drivers WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $driver = $result->fetch_assoc();
        
        // Store driver information in session
        $_SESSION['driver_id'] = $driver['driver_id'];
        $_SESSION['driver_name'] = $driver['full_name'];
        
        // Redirect to Dashboard_Drivers.html
        header("Location: ../Dashboard_Drivers.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password.'); window.location.href = '../Login_Drivers.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
