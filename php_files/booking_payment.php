<?php
require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

$sid = 'AC7697988d5d911522c69dc45417d071f';
$authToken = '56512d1126b3f0acd77f1208e87d27a2';
$twilioNumber = '+12028758559';

$mobileNumber = $_POST['mobile'] ?? null; 

if (!$mobileNumber) {
    echo "Mobile number is missing!";
    exit;
}

// Initialize the Twilio client
$client = new Client($sid, $authToken);

try {
    // Send SMS
    $client->messages->create(
        $mobileNumber, 
        [
            'from' => $twilioNumber,
            'body' => 'Booking payment successful! Thank You..! We are City Taxi services. '
        ]
    );
    echo "SMS sent successfully!";
} catch (Exception $e) {
    echo "Failed to send SMS: " . $e->getMessage();
}
?>
