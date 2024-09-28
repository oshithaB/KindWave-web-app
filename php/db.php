<?php
$servername = "localhost:3307";
$username = 'root';
$password = '';
$db='rotrac';

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Close connection

?>