<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parakalan_dbs";

// Create a connection to the database
$con = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}else{
	// echo"Database connected successfully!";
}

?>
