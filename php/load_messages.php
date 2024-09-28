<?php
session_start();
include 'db.php';

$currentUser = $_SESSION['username'];
$chatUser = $_GET['username'] ?? '';

if ($chatUser) {
    $stmt = $conn->prepare("
        SELECT c.message, c.sent_at, u.username AS sender
        FROM chats c
        JOIN users u ON u.user_id = c.sender_id
        WHERE (c.sender_id = (SELECT user_id FROM users WHERE username = ?) AND c.receiver_id = (SELECT user_id FROM users WHERE username = ?))
        OR (c.sender_id = (SELECT user_id FROM users WHERE username = ?) AND c.receiver_id = (SELECT user_id FROM users WHERE username = ?))
        ORDER BY c.sent_at ASC
    ");
    $stmt->bind_param('ssss', $currentUser, $chatUser, $chatUser, $currentUser);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<div><strong>' . htmlspecialchars($row['sender']) . ':</strong> ' . htmlspecialchars($row['message']) . ' <span>(' . date('Y-m-d H:i', strtotime($row['sent_at'])) . ')</span></div>';
    }
}
?>
