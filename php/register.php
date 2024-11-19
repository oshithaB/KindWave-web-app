<?php
session_start();
include 'db.php'; // Include the database connection

// Process the registration form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['regUsername'];
    $password = password_hash($_POST['regPassword'], PASSWORD_DEFAULT); // Hash password
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    // Check if username, email, or phone already exist in the users table
    $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ? OR phone = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: Username, email, or phone already exists.";
        exit;
    }

    // File upload handling
    $profile_picture = null;
    $proof_document = null;

    if ($role == 'donor') {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $profile_picture = "uploads/" . basename($_FILES['profile_picture']['name']);
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
        }

        // Save Donor to users table
        $sql = "INSERT INTO users (username, password, email, phone, address, role, profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $password, $email, $phone, $address, $role, $profile_picture);
    } else {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $profile_picture = "uploads/" . basename($_FILES['profile_picture']['name']);
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
        }

        if (isset($_FILES['proof_document']) && $_FILES['proof_document']['error'] == 0) {
            $proof_document = "uploads/" . basename($_FILES['proof_document']['name']);
            move_uploaded_file($_FILES['proof_document']['tmp_name'], $proof_document);
        }

        // Save Recipient to temp_user table
        $sql = "INSERT INTO temp_user (username, password, email, phone, address, role, profile_picture, proof_document) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $username, $password, $email, $phone, $address, $role, $profile_picture, $proof_document);
    }

    if ($stmt->execute()) {
        header('Location: index.html?success=Registration successful');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
