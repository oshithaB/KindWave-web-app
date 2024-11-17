<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the 'donor' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header('Location: index.html'); // Redirect to login if not authenticated
    exit;
}

// Check if the rating is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'], $_POST['rating'])) {
    $request_id = $_POST['request_id'];
    $rating = $_POST['rating'];
    $donor_id = $_SESSION['user_id']; // Get the logged-in donor's ID

    // Update the recipient's rating based on the request
    $sql = "UPDATE users 
            SET rating = (SELECT AVG(rating) FROM ratings WHERE recipient_id = (SELECT recipient_id FROM requests WHERE request_id = ?)) 
            WHERE user_id = (SELECT recipient_id FROM requests WHERE request_id = ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Handle error if the query fails to prepare
        die('Error in SQL prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("ii", $request_id, $request_id);

    if ($stmt->execute()) {
        // Insert the new rating into the ratings table
        $sql = "INSERT INTO ratings (recipient_id, donor_id, rating) 
                VALUES ((SELECT recipient_id FROM requests WHERE request_id = ?), ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Handle error if the query fails to prepare
            die('Error in SQL prepare statement: ' . $conn->error);
        }

        $stmt->bind_param("iii", $request_id, $donor_id, $rating);
        
        if ($stmt->execute()) {
            // Redirect back to the requests page with success message
            header('Location: requests.php?success=Rating submitted successfully');
        } else {
            echo "Error submitting rating: " . $stmt->error;
        }
    } else {
        echo "Error updating recipient rating: " . $stmt->error;
    }
    
    $stmt->close(); // Close the prepared statement
}

$conn->close(); // Close the database connection
?>
