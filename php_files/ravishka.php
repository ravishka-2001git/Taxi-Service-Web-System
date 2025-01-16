<?php
// Step 1: Connect to the database
$servername = "localhost";
$username = "root";
$password = "Ravi1030#Mysql";
$dbname = "city_taxi";
$conn = "";

// Create a connection
try{
    $conn = mysqli_connect($servername, 
                            $username, 
                            $password, 
                            $dbname);

}
catch(mysql_sql_exception){
    echo"not connected";
}


if($conn){
    echo"You are Connected";
}

?>
