<?php
session_start();
require 'db.php'; // Include your database connection file

// Check if user is logged in and is a delivery person
if (!isset($_SESSION['user_id'])) {
    die("You need to log in first.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handle the 'Delivered' button action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_delivered'])) {
    $delivery_id = intval($_POST['delivery_id']);

    // Start a transaction to ensure both updates succeed or none at all
    $conn->begin_transaction();

    try {
        // Update status in deliveries table
        $stmt = $conn->prepare("UPDATE deliveries SET status = 'delivered' WHERE delivery_id = ? AND delivery_man_id = ?");
        $stmt->bind_param("ii", $delivery_id, $user_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update deliveries table.");
        }

        // Fetch the corresponding request ID
        $stmt = $conn->prepare("SELECT request_id FROM deliveries WHERE delivery_id = ?");
        $stmt->bind_param("i", $delivery_id);
        $stmt->execute();
        $stmt->bind_result($request_id);
        $stmt->fetch();
        $stmt->close();

        // Update status in requests table
        $stmt = $conn->prepare("UPDATE requests SET status = 'delivered' WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update requests table.");
        }

        // Commit the transaction
        $conn->commit();

        echo "<p>Delivery #{$delivery_id} and request #{$request_id} marked as delivered.</p>";
    } catch (Exception $e) {
        // Roll back the transaction in case of any error
        $conn->rollback();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Fetch delivery requests for the logged-in delivery person
$sql = "
    SELECT 
        d.delivery_id, r.request_id, d.status, r.quantity,
        u.username AS recipient_username, u.phone AS recipient_phone, u.address AS recipient_address,
        do.item_name
    FROM deliveries d
    JOIN requests r ON d.request_id = r.request_id
    JOIN users u ON r.recipient_id = u.user_id
    JOIN donations do ON r.donation_id = do.donation_id
    WHERE d.delivery_man_id = ? AND d.status = 'up_for_delivery'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display delivery requests
if ($result->num_rows > 0) {
    echo "<h1>Accepted Deliveries</h1>";
    echo "<table border='1'>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Recipient Name</th>
            <th>Recipient Contact</th>
            <th>Recipient Address</th>
            <th>Action</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        $google_maps_link = "https://www.google.com/maps/search/?api=1&query=" . urlencode($row['recipient_address']);
        echo "<tr>
            <td>{$row['request_id']}</td>
            <td>{$row['item_name']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['recipient_username']}</td>
            <td>{$row['recipient_phone']}</td>
            <td><a href='{$google_maps_link}' target='_blank'>View on Map</a></td>
            <td>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='delivery_id' value='{$row['delivery_id']}'>
                    <button type='submit' name='mark_delivered'>Delivered</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No deliveries available for you.</p>";
}

$stmt->close();
$conn->close();
?>
