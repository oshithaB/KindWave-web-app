<?php
// Start the session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.html"); // Redirect to login if not logged in
    exit();
}

// Check if the donation ID is provided
if (!isset($_GET['id'])) {
    header("Location: live_donations.php"); // Redirect if ID is not provided
    exit();
}

$donation_id = $_GET['id'];

// Prepare SQL query for deletion
$sql = "DELETE FROM donations WHERE donation_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);

if ($stmt->execute()) {
    header("Location: live_donations.php"); // Redirect to live donations after deletion
    exit();
} else {
    echo "Error deleting donation: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
