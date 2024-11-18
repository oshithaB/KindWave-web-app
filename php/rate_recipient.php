<?php
session_start();
include 'db.php'; // Include the database connection

// Ensure the user is authenticated and authorized as a donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header('Location: index.html');
    exit;
}

// Get POST data
$request_id = intval($_POST['request_id']);
$rating = intval($_POST['rating']);

if ($rating < 1 || $rating > 5) {
    die('Invalid rating value.');
}

// Retrieve recipient details from the request
$sql = "SELECT recipient_id FROM requests WHERE request_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param('i', $request_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die('Request not found.');
}

$recipient_id = $row['recipient_id'];

// Update the recipient's rating and request count
$sql = "UPDATE users 
        SET rating = ((rating * num_ratings) + ?) / (num_ratings + 1), 
            num_ratings = num_ratings + 1 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param('di', $rating, $recipient_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unable to update recipient rating.']);
}

$stmt->close();
$conn->close();
?>
