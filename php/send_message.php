<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender = $_SESSION['username'];
    $receiver = $_POST['receiver'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message) VALUES ((SELECT user_id FROM users WHERE username = ?), (SELECT user_id FROM users WHERE username = ?), ?)");
    $stmt->bind_param('sss', $sender, $receiver, $message);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Message sent!';
    } else {
        echo 'Error sending message.';
    }
}
?>
