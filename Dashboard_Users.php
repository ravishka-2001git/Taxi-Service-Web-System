<?php
session_start();
include('php_files/db_connection.php'); // Ensure the path is correct

// Retrieve the session user_id if set, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in!";
    header("Location: Login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Adjust SQL to correctly access the table
$stmt = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc() ?? []; // Set default empty array to avoid null issues

// Fetch latest booking details
$booking_query = "
    SELECT b.*, d.full_name AS driver_name, d.mobile AS driver_phone, 
           v.vehicle_type, v.car_plate_number AS number_plate
    FROM bookings AS b
    JOIN drivers AS d ON b.driver_id = d.driver_id
    JOIN vehicles AS v ON b.vehicle_id = v.vehicle_id
    WHERE b.user_id = ?
    ORDER BY b.date DESC, b.time DESC
    LIMIT 1";

$booking_stmt = $conn->prepare($booking_query);
$booking_stmt->bind_param("i", $user_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
$booking_info = $booking_result->fetch_assoc() ?? []; // Set default empty array to avoid null issues

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

<div class="user-dashboard">

    <!-- New Booking Details -->
    <section id="driver-approval">
        <h2>Driver Pending Approval List</h2>
        <table>
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Phone Number</th>
                    <th>Vehicle Type</th>
                    <th>Number Plate</th>
                    <th>Status</th>
                    <th>Cancel Booking</th>
                    <th>Destination</th>
                </tr>
            </thead>
            <tbody id="approvalList" class="decline">
                <?php if ($booking_info): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking_info['driver_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking_info['driver_phone']); ?></td>
                    <td><?php echo htmlspecialchars($booking_info['vehicle_type']); ?></td>
                    <td><?php echo htmlspecialchars($booking_info['number_plate']); ?></td>
                    <td><?php echo htmlspecialchars($booking_info['status']); ?></td>
                    <td>
                        <form method="post" action="cancel_booking.php">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_info['booking_id']; ?>">
                            <button type="submit" onclick="declineDriver(this)" class="decline-btn">&#10060;</button>
                        </form>
                    </td>
                    <td><a href="Rating.html"><button class="confirm-btn">confirm</button></a></td>
                </tr>
                <?php else: ?>
                <tr><td colspan="7">No current booking found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Users Dashboard Start -->
<div class="user-dashboard">
    <h2 class="dashboard-title">User Dashboard</h2>
    
    <div class="dashboard-content-User">
        <!-- Profile Section -->
        <div class="profile-section">
        <h3>Profile Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        </div>

        <!-- Account Settings Section -->
        <div class="account-settings">
            <h3>Account Settings</h3>
            <button class="settings-btn">Change Password</button>
            <button class="settings-btn">Update Profile</button>
        </div>

        <!-- Booking History Section -->
        <div class="booking-history">
            <h3>Booking History</h3>
            <table>
                <thead>
                    <tr>
                    <th>Driver Name</th>
                    <th>Vehicle Type</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($booking_info): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking_info['driver_name']); ?></td>
                <td><?php echo htmlspecialchars($booking_info['vehicle_type']); ?></td>
                <td><?php echo htmlspecialchars($booking_info['date']); ?></td>
                <td><?php echo htmlspecialchars($booking_info['status']); ?></td>
            </tr>
            <?php else: ?>
            <tr><td colspan="4">No booking history found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>    
</div>

<!-- Footer and other static content here -->
     <!-- Footer Start -->
     <div class="container-fluid bg-dark py-4 px-sm-3 px-md-5">
        <p class="mb-2 text-center text-body">&copy; <a href="https://www.facebook.com/ravishka.fernandoz">City Taxi Services</a>. All Rights Reserved.</p>
        <p class="m-0 text-center text-body">Designed by Ravishka Fernando</p>
    </div>
    <!-- Footer End -->


<script src="script.js"></script>
</body>
</html>

