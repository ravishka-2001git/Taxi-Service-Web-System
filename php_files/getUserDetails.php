<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $response = array(
        "full_name" => $_SESSION['full_name'],
        "email" => $_SESSION['email'],
        "mobile" => $_SESSION['mobile']
    );
    echo json_encode($response);
} else {
    echo json_encode(array("error" => "User not logged in"));
}
?>
