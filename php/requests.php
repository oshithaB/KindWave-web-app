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
               r.status, d.item_name, COALESCE((SELECT AVG(rating) FROM ratings WHERE recipient_id = u.user_id), 0) AS recipient_rating
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
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .rating {
            cursor: pointer;
            color: #ddd;
        }
        .rating.active {
            color: gold;
        }
        .no-action {
            color: gray;
            cursor: not-allowed;
        }
    </style>
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
                                <div class="rating-container">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span 
                                            class="rating <?php echo $i <= round($row['recipient_rating']) ? 'active' : ''; ?>" 
                                            onclick="rate(<?php echo $i; ?>, <?php echo $row['request_id']; ?>)">
                                            &#9733;
                                        </span>
                                    <?php endfor; ?>
                                </div>
                            <?php else: ?>
                                <?php echo str_repeat('⭐', round($row['recipient_rating'])); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] != 'delivered'): ?>
                                <form method="POST" action="update_status.php">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <button type="submit" name="status" value="in_review">In Review</button>
                                </form>
                            <?php else: ?>
                                <span class="no-action">No Actions</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function rate(stars, requestId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'rate_recipient.php';

            const requestInput = document.createElement('input');
            requestInput.type = 'hidden';
            requestInput.name = 'request_id';
            requestInput.value = requestId;

            const ratingInput = document.createElement('input');
            ratingInput.type = 'hidden';
            ratingInput.name = 'rating';
            ratingInput.value = stars;

            form.appendChild(requestInput);
            form.appendChild(ratingInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>

<?php
$stmt->close(); // Close the statement
$conn->close(); // Close the database connection
?>
