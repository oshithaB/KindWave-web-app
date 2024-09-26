<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the sender's ID and the message details
    $senderId = $_SESSION['user_id']; // Assuming the user ID is stored in session
    $receiverUsername = $_POST['receiver'];
    $message = $_POST['message'];

    // Validate input
    if (!empty($message) && !empty($receiverUsername)) {
        // Fetch the receiver's ID based on the username
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $receiverUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $receiver = $result->fetch_assoc();
            $receiverId = $receiver['user_id'];

            // Insert the message into the database
            $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $senderId, $receiverId, $message);

            if ($stmt->execute()) {
                echo "Message sent successfully.";
            } else {
                echo "Error: Could not send message.";
            }
        } else {
            echo "Error: Receiver not found.";
        }
    } else {
        echo "Error: Message cannot be empty.";
    }
} else {
    echo "Invalid request.";
}
?>
