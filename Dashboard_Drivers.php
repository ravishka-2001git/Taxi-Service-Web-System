<?php
// Start session and check if the driver is logged in
session_start();

// Correct the path based on your file structure
if (file_exists('php_files/db_connection.php')) {
    include('php_files/db_connection.php');
} else {
    die('Database connection file not found.');
}

// Check if the driver is logged in, else redirect to login page
if (!isset($_SESSION['driver_id'])) {
    header("Location: Login_Drivers.html");
    exit;
}

// Retrieve driver details from session
$driver_id = $_SESSION['driver_id'];

// Step 1: Fetch Driver's Personal Information
$driver_query = $conn->prepare("SELECT full_name, email, mobile FROM drivers WHERE driver_id = ?");
$driver_query->bind_param("i", $driver_id);
$driver_query->execute();
$driver_result = $driver_query->get_result();
$driver = $driver_result->fetch_assoc();

// Step 2: Fetch relevant bookings for the logged-in driver
$bookings_query = "
    SELECT u.full_name AS user_name, u.phone_number, b.time AS pickup_time, 
           b.pickup_location, b.dropoff_location, b.total_cost
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE b.driver_id = ? 
    AND b.status = 'pending' 
    AND v.vehicle_type = (SELECT vehicle_type FROM vehicles WHERE driver_id = ?)
    AND (SELECT status FROM drivers WHERE driver_id = ?) = 'AVAILABLE'
";
$bookings_stmt = $conn->prepare($bookings_query);
$bookings_stmt->bind_param("iii", $driver_id, $driver_id, $driver_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Taxi Services - User Dashboard</title>
    <link rel="stylesheet" href="Design/styles.css">
</head>
<body>

    <!-- Navigation Bar Start -->
    <div class="banner">
        <div class="navbar">
            <h1 class="text-uppercase text-primary mb-1">City Taxi Services</h1>
            <img src="Imgs/20240926_012106.png" alt="Logo" class="logo">
            <ul>
                <li><a href="Home.html" class="btn">HOME</a></li>
                
                <!-- Navbar - Booking Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link">BOOKING</a>
                    <div class="dropdown-menu">
                        <a href="Booking.html" class="dropdown-item">Book Taxi</a>
                        <a href="Services.html" class="dropdown-item">Additional Service</a>
                    </div>
                </div>

                <!-- Navbar - Driver Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link">DRIVER</a>
                    <div class="dropdown-menu">
                        <a href="Login_Drivers.html" class="dropdown-item">Driver Login</a>
                        <a href="Drivers.html" class="dropdown-item">Driver Registration</a>
                    </div>
                </div>
                <li><a href="About.html" class="btn">About Us</a></li>
                <li><a href="Contact.html" class="btn">Contact</a></li>
            </ul>
            <a href="Login.html"><button class="login-btn">Login</button></a>
        </div>
    </div>
    <!-- Navigation Bar End -->

                 <!-- Page Header Start -->
                 <div class="container-fluid page-header">
                <h1 class="display-3 text-uppercase text-white mb-3">Driver Dashboard</h1>
                <div class="d-inline-flex text-white">
                    <h6 class="text-uppercase m-0"><a class="text-white" href="">Home</a></h6>
                    <h6 class="text-body m-0 px-3">/</h6>
                    <h6 class="text-uppercase text-body m-0">Driver Dashboard</h6>
                </div>
            </div>
            <!-- Page Header Start -->

 <div class="user-dashboard">

    <!-- New Booking Details -->
    <section id="driver-approval">
        <h2>Driver Pending Approval List</h2>
        <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Phone Number</th>
                        <th>Pick-Up Time</th>
                        <th>Pick-Up Location</th>
                        <th>Drop-Off Location</th>
                        <th>Taxi Fare</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings_result->num_rows > 0): ?>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($booking['pickup_time']); ?></td>
                                <td><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
                                <td><?php echo htmlspecialchars($booking['dropoff_location']); ?></td>
                                <td><?php echo htmlspecialchars($booking['total_cost']); ?></td>
                                <td>
                                    <button onclick="approveDriver(this)" class="approve-btn">&#10003;</button>
                                    <button onclick="declineDriver(this)" class="decline-btn">&#10060;</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No active bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
    </section>
</div>

<!-- Users Dashboard Start -->
<div class="user-dashboard">
    <h2 class="dashboard-title">Driver Dashboard</h2>
    
    <div class="dashboard-content-User">
        <!-- Profile Section -->
        <div class="profile-section">
        <h3>Profile Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($driver['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($driver['mobile']); ?></p>
        </div>

         <!-- Status Toggle Section -->
         <div class="status-section">
            <p><strong>Status:</strong> <span id="driverStatus">AVAILABLE</span></p>
            <button onclick="toggleStatus()" class="status-btn">Toggle Availability</button>
        </div>

        <!-- Recent Ratings -->
        <div class="ratings-section">
            <h3>Recent Ratings</h3>
            <ul id="recentRatings">
                <li>★★★★☆ - "Great driver!"</li>
                 <li>★★★★★ - "Excellent service."</li>
            </ul>
        </div>
    </div>    
</div>



<!-- Footer and other static content here -->
     <!-- Footer Start -->
     <div class="container-fluid bg-dark py-4 px-sm-3 px-md-5">
        <p class="mb-2 text-center text-body">&copy; City Taxi Services. All Rights Reserved.</p>
        <p class="m-0 text-center text-body">Designed by Ravishka Fernando</p>
    </div>
    <!-- Footer End -->


<script src="script.js"></script>
</body>
</html>
