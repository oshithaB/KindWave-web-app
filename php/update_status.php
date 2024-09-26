<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the 'donor' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header('Location: index.html'); // Redirect to login if not authenticated
    exit;
}

// Check if the status update is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'], $_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Update the status of the request
    $sql = "UPDATE requests SET status = ? WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $request_id);

    if ($stmt->execute()) {
        // Redirect back to the requests page
        header('Location: requests.php?success=Status updated successfully');
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
}

$conn->close(); // Close the database connection
?>
