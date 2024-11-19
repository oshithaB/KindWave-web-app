<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to log in first.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch done deliveries for the logged-in user
$sql = "
    SELECT 
        d.delivery_id, r.request_id, r.quantity, d.status, 
        u.username AS recipient_username, u.phone AS recipient_phone, u.address AS recipient_address,
        do.item_name
    FROM deliveries d
    JOIN requests r ON d.request_id = r.request_id
    JOIN users u ON r.recipient_id = u.user_id
    JOIN donations do ON r.donation_id = do.donation_id
    WHERE d.delivery_man_id = ? AND d.status = 'delivered'
    ORDER BY d.delivery_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display done deliveries
echo "<h1>Done Deliveries</h1>";

if ($result->num_rows > 0) {
    echo "<table border='1'>
        <tr>
            <th>Delivery ID</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Recipient Name</th>
            <th>Recipient Contact</th>
            <th>Recipient Address</th>
            <th>Status</th>
        </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $google_maps_link = "https://www.google.com/maps/search/?api=1&query=" . urlencode($row['recipient_address']);
        echo "<tr>
            <td>{$row['delivery_id']}</td>
            <td>{$row['item_name']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['recipient_username']}</td>
            <td>{$row['recipient_phone']}</td>
            <td><a href='{$google_maps_link}' target='_blank'>View on Map</a></td>
            <td>{$row['status']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No done deliveries found.</p>";
}

$stmt->close();
$conn->close();
?>
