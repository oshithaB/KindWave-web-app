<?php
session_start();
include 'db.php'; // Include your database connection file

// Fetch chat messages between the logged-in user and another user
if (isset($_GET['username'])) {
    $chatUser = $_GET['username'];
    $currentUser = $_SESSION['username']; // Assuming the username is stored in session

    // Fetch chat messages
    $stmt = $conn->prepare("
        SELECT c.message, c.sent_at, u1.username AS sender, u2.username AS receiver
        FROM chats c
        JOIN users u1 ON c.sender_id = u1.user_id
        JOIN users u2 ON c.receiver_id = u2.user_id
        WHERE (c.sender_id = (SELECT user_id FROM users WHERE username = ?) 
        AND c.receiver_id = (SELECT user_id FROM users WHERE username = ?)) 
        OR (c.sender_id = (SELECT user_id FROM users WHERE username = ?) 
        AND c.receiver_id = (SELECT user_id FROM users WHERE username = ?))
        ORDER BY c.sent_at ASC
    ");
    $stmt->bind_param("ssss", $currentUser, $chatUser, $chatUser, $currentUser);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            background-color: #001f3f; /* Dark blue background */
            color: white; /* White text */
            font-family: Arial, sans-serif; /* Font style */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px; /* Maximum width for chat container */
            margin: 50px auto; /* Center the container */
            background-color: #002b5c; /* Card background */
            padding: 20px; /* Inner padding */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Shadow effect */
        }
        h2 {
            text-align: center; /* Centered title */
        }
        .messages {
            max-height: 400px; /* Maximum height for messages area */
            overflow-y: auto; /* Scrollable if overflow */
            margin-bottom: 20px; /* Margin below messages area */
            padding: 10px; /* Inner padding */
            border: 1px solid #0056b3; /* Border for messages area */
            border-radius: 5px; /* Rounded corners */
            background-color: #00395d; /* Background for messages */
        }
        .message {
            margin-bottom: 10px; /* Space between messages */
        }
        .message strong {
            color: #00ccff; /* Color for sender's name */
        }
        .message span {
            font-size: 0.9em; /* Smaller font for timestamp */
            color: #b0c4de; /* Light color for timestamp */
        }
        .input-area {
            display: flex; /* Flexbox layout for input */
            justify-content: space-between; /* Space between elements */
        }
        .input-area input[type="text"] {
            flex: 1; /* Take remaining space */
            padding: 10px; /* Inner padding */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
        }
        .input-area button {
            padding: 10px 15px; /* Inner padding */
            background-color: #0056b3; /* Button color */
            color: white; /* Button text color */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer on hover */
        }
        .input-area button:hover {
            background-color: #007bff; /* Lighter blue on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chat with <?php echo htmlspecialchars($chatUser); ?></h2>
    <div class="messages">
        <?php
        if (isset($result)) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="message">';
                echo '<strong>' . htmlspecialchars($row['sender']) . '</strong>: ' . htmlspecialchars($row['message']);
                echo '<span> (' . date('Y-m-d H:i', strtotime($row['sent_at'])) . ')</span>';
                echo '</div>';
            }
        }
        ?>
    </div>
    <div class="input-area">
        <input type="text" id="message" placeholder="Type your message...">
        <button id="sendBtn">Send</button>
    </div>
</div>

<script>
document.getElementById('sendBtn').addEventListener('click', function() {
    var messageInput = document.getElementById('message');
    var message = messageInput.value;

    if (message.trim() !== '') {
        // Send the message to the server
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_message.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                messageInput.value = ''; // Clear input field
                window.location.reload(); // Refresh chat to see new message
            }
        };
        xhr.send('receiver=' + encodeURIComponent('<?php echo $chatUser; ?>') + '&message=' + encodeURIComponent(message));
    }
});
</script>

</body>
</html>
