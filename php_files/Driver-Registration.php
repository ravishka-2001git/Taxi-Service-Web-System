<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Step 1: Database connection
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

// Step 2: Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullName = $_POST['fullName'];
    $nic = $_POST['nic'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $birthday = $_POST['Birthday'];
    $gender = $_POST['Gender'];
    $Username = $_POST['Username'];
    $Password = $_POST['Password'];
    $vehicleType = $_POST['VehicleType'];
    $vehicleModel = $_POST['VehicleModel'];
    $licenseNumber = $_POST['LicenseNumber'];
    $carPlateNumber = $_POST['CarPlateNumber'];

    // Start MySQL transaction
    $conn->begin_transaction();
    try {
        // Insert into drivers table
        $stmt = $conn->prepare("INSERT INTO drivers (full_name, nic, age, address, email, mobile, birthday, gender, Username, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement for drivers: " . $conn->error);
        }
        $stmt->bind_param("ssisssssss", $fullName, $nic, $age, $address, $email, $mobile, $birthday, $gender, $Username, $Password);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement for drivers: " . $stmt->error);
        }
        $driverId = $conn->insert_id; // Get the last inserted driver ID
        $stmt->close();

        // Insert into vehicles table
        $stmt = $conn->prepare("INSERT INTO vehicles (driver_id, vehicle_type, vehicle_model, license_number, car_plate_number) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement for vehicles: " . $conn->error);
        }
        $stmt->bind_param("issss", $driverId, $vehicleType, $vehicleModel, $licenseNumber, $carPlateNumber);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement for vehicles: " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();
        echo "Driver registration is successful!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Close the connection
$conn->close();
?>
