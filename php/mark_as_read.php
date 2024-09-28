<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverUsername = $_POST['receiver'];
    $currentUserId = $_SESSION['user_id'];

    // Get receiver ID
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param('s', $receiverUsername);
    $stmt->execute();
    $stmt->bind_result($receiverId);
    $stmt->fetch();
    $stmt->close();

    // Mark messages as read
    $stmt = $conn->prepare("UPDATE chats SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $stmt->bind_param('ii', $receiverId, $currentUserId);
    $stmt->execute();
    $stmt->close();
}
?>
