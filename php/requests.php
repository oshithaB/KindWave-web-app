<?php
session_start(); // Start the session to access user information
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the 'donor' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header('Location: index.html'); // Redirect to login if not authenticated
    exit;
}

// Fetch the logged-in donor's ID
$donor_id = $_SESSION['user_id'];

// Fetch requests related to the logged-in donor
$sql = "SELECT r.request_id, r.donation_id, r.quantity, 
               u.address AS recipient_address, u.username AS recipient_username, 
               r.status, d.item_name, u.rating AS recipient_rating 
        FROM requests r 
        JOIN donations d ON r.donation_id = d.donation_id 
        JOIN users u ON r.recipient_id = u.user_id 
        WHERE d.donor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests - Donor Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Your Donation Requests</h1>
        <table>
            <thead>
                <tr>
                    <th>Recipient</th>
                    <th>Donation ID</th>
                    <th>Item Name</th>
                    <th>Quantity Requested</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['recipient_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['donation_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['recipient_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'delivered'): ?>
                                <form method="POST" action="rate_recipient.php">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <input type="number" name="rating" min="1" max="5" required>
                                    <button type="submit">Rate</button>
                                </form>
                            <?php else: ?>
                                <?php echo str_repeat('⭐', (int)$row['recipient_rating']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="update_status.php">
                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                <button type="submit" name="status" value="in_review">In Review</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close(); // Close the statement
$conn->close(); // Close the database connection
?>
