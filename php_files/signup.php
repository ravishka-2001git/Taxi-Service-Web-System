<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "Ravi1030#Mysql";
$dbname = "city_taxi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $fullName = $_POST['fullName'];
    $userName = $_POST['Username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Step 1: Insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, Username, email, phone_number, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $userName, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        // If registration successful, send the email
        sendEmail($fullName, $userName, $email, $password);
        echo "Registration successful! An email has been sent.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Step 2: Send email function
function sendEmail($fullName, $userName, $email, $password) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'fernandozravishka30@gmail.com';  // Your Gmail account
        $mail->Password = 'aowfidnazpoaunvw';  // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('fernandozravishka30@gmail.com', 'City Taxi Service');
        $mail->addAddress($email);  // The user's email address

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your City Taxi Account Details';
        $mail->Body = "
            <p>Hello Customer,</p>
            <p>Mr or Miss/Mrs: <strong>{$fullName}</strong></p>
            <p>Username: <strong>{$userName}</strong></p>
            <p>Password: <strong>{$password}</strong></p>
            <br/>
            <p>Thank you.<br/>City Taxi Service team.</p>
        ";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
