<?php
session_start();
include 'db.php'; // Database connection

$currentUserId = $_SESSION['user_id']; // Ensure user ID is stored in session

// Fetch users you've chatted with, ensuring they have exchanged messages
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, 
           (SELECT message FROM chats WHERE (sender_id = u.user_id AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = u.user_id) 
            ORDER BY sent_at DESC LIMIT 1) AS last_message,
           (SELECT sent_at FROM chats WHERE (sender_id = u.user_id AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = u.user_id) 
            ORDER BY sent_at DESC LIMIT 1) AS last_sent_at,
           (SELECT sender_id FROM chats WHERE (sender_id = u.user_id AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = u.user_id) 
            ORDER BY sent_at DESC LIMIT 1) AS last_sender
    FROM users u
    WHERE u.user_id IN (
        SELECT DISTINCT CASE 
            WHEN sender_id = ? THEN receiver_id 
            ELSE sender_id 
        END AS other_user 
        FROM chats 
        WHERE sender_id = ? OR receiver_id = ?
    ) AND u.user_id != ?
");

$stmt->bind_param("iiiiiiiiii", $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$chats = [];
while ($row = $result->fetch_assoc()) {
    // Check if the last message is from the current user
    $unread = ($row['last_sender'] != $currentUserId); // Unread if last sender is not the current user
    $chats[] = [
        'username' => $row['username'],
        'last_message' => $row['last_message'],
        'unread' => $unread,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <style>
        body {
            display: flex;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
        }
        .sidebar {
            width: 300px;
            background-color: #007bff;
            color: white;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            margin: 0 0 20px;
        }
        .user {
            margin: 10px 0;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .user:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .unread {
            color: red;
            font-weight: bold;
        }
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            background-color: white;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .input-area {
            display: flex;
            margin-bottom: 15px;
        }
        .input-area input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        .input-area button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .input-area button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Chat List</h2>
    <?php if (empty($chats)): ?>
        <p>No chats available.</p>
    <?php else: ?>
        <?php foreach ($chats as $chat): ?>
            <div class="user <?= $chat['unread'] ? 'unread' : '' ?>" data-username="<?= htmlspecialchars($chat['username']); ?>">
                <?= htmlspecialchars($chat['username']) . ($chat['unread'] ? ' (unread)' : ''); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3>Start New Chat</h3>
    <div class="input-area">
        <input type="text" id="newChatUsername" placeholder="Type username...">
        <button id="startChat">Start Chat</button>
    </div>
</div>

<div class="chat-area">
    <h2>Chat with <span id="chatWith">Select a user</span></h2>
    <div id="messages" class="messages"></div>
    <div class="input-area">
        <input type="text" id="messageInput" placeholder="Type your message...">
        <button id="sendMessage">Send</button>
    </div>
</div>

<script>
    let currentChatUser = '';

    document.querySelectorAll('.user').forEach(function(user) {
        user.addEventListener('click', function() {
            currentChatUser = this.getAttribute('data-username');
            document.getElementById('chatWith').textContent = currentChatUser;
            loadMessages(currentChatUser);
        });
    });

    document.getElementById('sendMessage').addEventListener('click', function() {
        const message = document.getElementById('messageInput').value.trim();
        if (message && currentChatUser) {
            sendMessage(currentChatUser, message);
            document.getElementById('messageInput').value = ''; // Clear input
        }
    });

    document.getElementById('startChat').addEventListener('click', function() {
        const username = document.getElementById('newChatUsername').value.trim();
        if (username) {
            currentChatUser = username;
            document.getElementById('chatWith').textContent = currentChatUser;
            loadMessages(currentChatUser);
            document.getElementById('newChatUsername').value = ''; // Clear input
        }
    });

    function loadMessages(username) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'load_messages.php?username=' + encodeURIComponent(username), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('messages').innerHTML = xhr.responseText;
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight; // Scroll to bottom
            }
        };
        xhr.send();
    }

    function sendMessage(username, message) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_message.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                loadMessages(username); // Reload messages to show the new one
            }
        };
        xhr.send('receiver=' + encodeURIComponent(username) + '&message=' + encodeURIComponent(message));
    }

    // Refresh the whole page every 20 seconds
    setInterval(() => {
        window.location.reload();
    }, 200000);
</script>

</body>
</html>
