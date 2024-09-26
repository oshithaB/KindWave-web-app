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

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donation_id = $_POST['donation_id'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    // Prepare SQL query for updating donation
    $sql = "UPDATE donations SET item_name = ?, category = ?, quantity = ? WHERE donation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $item_name, $category, $quantity, $donation_id);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Handle image upload if a new image is provided
        if (!empty($_FILES['image1']['name'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image1"]["name"]);
            move_uploaded_file($_FILES["image1"]["tmp_name"], $target_file);

            // Update image in the database
            $sql = "UPDATE donations SET image1 = ? WHERE donation_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $_FILES["image1"]["name"], $donation_id);
            $stmt->execute();
        }

        header("Location: live_donations.php"); // Redirect to live donations
        exit();
    } else {
        echo "Error updating donation: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
