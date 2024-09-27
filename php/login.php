<?php
session_start();
include 'db.php'; // Include your updated db.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a SQL statement using MySQLi
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    
    // Bind the username parameter to the statement
    $stmt->bind_param('s', $username);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Store user info in session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the user's role
        if ($user['role'] == 'donor') {
            header('Location: ../donor_dashboard.html');
        } elseif ($user['role'] == 'recipient') {
            header('Location: ../recipient_dashboard.html');
        } elseif ($user['role'] == 'admin') {
            header('Location: ../admin_dashboard.html');
        } elseif ($user['role'] == 'delivery') {
            header('Location: ../deliveryMan_dashboard.html');
        }
        exit();
    } else {
        // If login credentials are invalid
        echo "<script>alert('Invalid login credentials');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
