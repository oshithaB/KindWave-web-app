<?php
session_start();
include 'db.php';

// Ensure the user is authenticated and authorized as a recipient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recipient') {
    header('Location: index.html');
    exit;
}

// Get POST data
$request_id = intval($_POST['request_id']);
$rating = intval($_POST['rating']);

if ($rating < 1 || $rating > 5) {
    die('Invalid rating value.');
}

// Retrieve donor details from the request
$sql = "SELECT d.donor_id FROM requests r
        JOIN donations d ON r.donation_id = d.donation_id
        WHERE r.request_id = ?";
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

$donor_id = $row['donor_id'];

// Update the donor's rating and request count
$sql = "UPDATE users 
        SET rating = ((rating * num_ratings) + ?) / (num_ratings + 1), 
            num_ratings = num_ratings + 1 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Error preparing query: ' . $conn->error);
}

$stmt->bind_param('di', $rating, $donor_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unable to update donor rating.']);
}

$stmt->close();
$conn->close();
?>
