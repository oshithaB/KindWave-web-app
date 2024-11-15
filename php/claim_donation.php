<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = intval($_POST['donation_id']);
    $quantity = intval($_POST['quantity']);
    $recipient_id = $_SESSION['user_id'];

    // Validate donation and quantity
    $sql = "SELECT quantity FROM donations WHERE donation_id = $donation_id";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        die("Invalid Donation");
    }
    $donation = $result->fetch_assoc();

    if ($quantity > $donation['quantity']) {
        die("Error: Quantity exceeds availability");
    }

    // Update requests table
    $address = ""; // Address will be prompted via a popup or form
    $sql = "INSERT INTO requests (donation_id, recipient_id, quantity, address) 
            VALUES ($donation_id, $recipient_id, $quantity, '$address')";
    if ($conn->query($sql)) {
        echo "<script>alert('Donation claimed successfully! Provide location details.');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
