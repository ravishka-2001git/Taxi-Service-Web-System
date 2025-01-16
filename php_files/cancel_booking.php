<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Delete the booking
    $delete_query = "DELETE FROM bookings WHERE booking_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        header("Location: Dashboard_Users.php?message=Booking+cancelled+successfully");
    } else {
        echo "Error cancelling booking.";
    }
}
?>
