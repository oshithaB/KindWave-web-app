<?php
include 'db.php'; // Include your updated db.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['regUsername'];
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT); // Hash the password
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username already exists!');</script>";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, address, role) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $username, $password, $email, $phone, $address, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location = 'index.html';</script>";
        } else {
            echo "<script>alert('Registration failed!');</script>";
        }
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
