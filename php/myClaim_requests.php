<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the 'recipient' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recipient') {
    header('Location: index.html');
    exit;
}

// Fetch the logged-in recipient's ID
$recipient_id = $_SESSION['user_id'];

// Fetch claims related to the logged-in recipient, including donor information and ratings
$sql = "SELECT r.request_id, r.donation_id, r.quantity, 
               u.address AS donor_address, u.username AS donor_username, 
               r.status, d.item_name, ROUND(COALESCE(u.rating, 0), 2) AS donor_rating
        FROM requests r
        JOIN donations d ON r.donation_id = d.donation_id
        JOIN users u ON d.donor_id = u.user_id
        WHERE r.recipient_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $recipient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false || $result->num_rows == 0) {
    echo "No claims found or there was an error with the query.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Claims - Recipient Dashboard</title>
    <link rel="stylesheet" href="body.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rating-container {
            display: inline-block;
        }
        .rating {
            font-size: 20px;
            cursor: pointer;
        }
        .rating.active {
            color: gold;
        }
    </style>
</head>
<body>

    <?php include 'recipient_navigation.php'; ?>
    <div class="container">
        <h1>Your Claims</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Donation ID</th>
                    <th>Item Name</th>
                    <th>Quantity Claimed</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['donor_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['donation_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['donor_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <div class="rating-container">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="rating <?php echo $i <= round($row['donor_rating']) ? 'active' : ''; ?>">
                                        &#9733;
                                    </span>
                                <?php endfor; ?>
                                <p><?php echo round($row['donor_rating'], 2); ?> / 5</p>
                            </div>
                            <?php if ($row['status'] == 'delivered'): ?>
                                <div class="rating-container">
                                    <p>Rate:</p>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span 
                                            class="rating" 
                                            onclick="rate(<?php echo $i; ?>, <?php echo $row['request_id']; ?>, '<?php echo addslashes($row['donor_username']); ?>')">
                                            &#9733;
                                        </span>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>No Actions</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function rate(stars, requestId, donorUsername) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'rate_donor.php';

            const requestInput = document.createElement('input');
            requestInput.type = 'hidden';
            requestInput.name = 'request_id';
            requestInput.value = requestId;

            const ratingInput = document.createElement('input');
            ratingInput.type = 'hidden';
            ratingInput.name = 'rating';
            ratingInput.value = stars;

            const donorInput = document.createElement('input');
            donorInput.type = 'hidden';
            donorInput.name = 'donor_username';
            donorInput.value = donorUsername;

            form.appendChild(requestInput);
            form.appendChild(ratingInput);
            form.appendChild(donorInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
