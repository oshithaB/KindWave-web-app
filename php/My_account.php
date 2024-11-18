<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// Determine user role and fetch role-specific data
$role = $user['role'];
$additional_data = [];

if ($role === 'donor') {
    $stmt = $conn->prepare("SELECT COUNT(*) as donation_count FROM donations WHERE donor_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $donation_data = $stmt->get_result()->fetch_assoc();
    $donation_count = $donation_data['donation_count'];
    $batch = $donation_count > 20 ? "Gold" : ($donation_count > 10 ? "Silver" : "Basic");
    $additional_data = ['donation_count' => $donation_count, 'batch' => $batch];
} elseif ($role === 'delivery') {
    $stmt = $conn->prepare("SELECT COUNT(*) as delivery_count FROM deliveries WHERE delivery_man_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $delivery_data = $stmt->get_result()->fetch_assoc();
    $additional_data = ['delivery_count' => $delivery_data['delivery_count']];
}

// Update user data if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_picture = $user['profile_picture'];

    // Handle file upload for profile picture
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("sssssi", $username, $email, $phone, $address, $profile_picture, $user_id);
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        header("Location: my_account.php");
        exit;
    } else {
        echo "Failed to update profile.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($user['username']); ?></h1>
    <img src="<?= htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="150">
    <p>Role: <?= htmlspecialchars(ucfirst($role)); ?></p>
    <p>Rating: <?= htmlspecialchars($user['rating']); ?></p>

    <?php if ($role === 'donor'): ?>
        <p>Donations: <?= htmlspecialchars($additional_data['donation_count']); ?></p>
        <p>Batch: <?= htmlspecialchars($additional_data['batch']); ?></p>
    <?php elseif ($role === 'delivery'): ?>
        <p>Deliveries: <?= htmlspecialchars($additional_data['delivery_count']); ?></p>
    <?php endif; ?>

    <h2>Personal Details</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br>
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required><br>
        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?= htmlspecialchars($user['address']); ?></textarea><br>
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture"><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
