<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "Ravi1030#Mysql";
$dbname = "city_taxi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$user_fullname = $_POST['full_Name'];
$tripDate = $_POST['tripDate'];
$pickupTime = $_POST['pickupTime'];
$pickupLocation = $_POST['pickupLocation'];
$dropoffLocation = $_POST['dropoffLocation'];
$Charging = $_POST['Charging']; // "Rs.0.00" or similar
$VehicleType = $_POST['VehicleType'];

// Step 1: Check if user exists
$user_check_query = "SELECT user_id FROM users WHERE full_name = ?";
$stmt = $conn->prepare($user_check_query);
$stmt->bind_param("s", $user_fullname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Please register to the City Taxi system.";
    exit();
} else {
    $user_data = $result->fetch_assoc();
    $user_id = $user_data['user_id'];
}
$stmt->close();

// Step 2: Find available vehicle and driver
$vehicle_query = "
    SELECT v.vehicle_id, d.driver_id 
    FROM vehicles v
    JOIN drivers d ON v.driver_id = d.driver_id
    WHERE v.vehicle_type = ? AND d.status = 'AVAILABLE'
    LIMIT 1
";
$stmt = $conn->prepare($vehicle_query);
$stmt->bind_param("s", $VehicleType);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No available vehicle and driver for the selected type. Please try again later.";
    exit();
} else {
    $vehicle_data = $result->fetch_assoc();
    $vehicle_id = $vehicle_data['vehicle_id'];
    $driver_id = $vehicle_data['driver_id'];
}
$stmt->close();

// Clean the Charging value (remove non-numeric characters)
$Charging = str_replace(['Rs.', ' ', ','], '', $Charging); // Removes 'Rs.', spaces, and commas
$Charging = floatval($Charging); // Convert it to a numeric value

// Debugging: Print Charging value to verify
// echo "Charging: " . $Charging; // Uncomment for debugging purposes

// Step 4: Insert the booking
$insert_booking_query = "
    INSERT INTO bookings (user_id, driver_id, vehicle_id, date, time, pickup_location, dropoff_location, status, total_cost) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)
";
$stmt = $conn->prepare($insert_booking_query);
$stmt->bind_param("iiissssd", $user_id, $driver_id, $vehicle_id, $tripDate, $pickupTime, $pickupLocation, $dropoffLocation, $Charging);

if ($stmt->execute()) {
    echo "Booking successful!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
