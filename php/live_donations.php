<?php
// Start the session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.html"); // Redirect to login if not logged in
    exit();
}

// Fetch the logged-in user's ID
$donor_id = $_SESSION['user_id'];

// Fetch donations added by the logged-in donor
$sql = "SELECT * FROM donations WHERE donor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Donations</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link your CSS file -->
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .donation-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .donation-card {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 300px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .donation-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .donation-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .donation-info {
            padding: 15px;
        }

        .donation-info h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .donation-info p {
            margin: 5px 0;
            color: #666;
        }

        .donation-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        .edit-btn, .delete-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Your Live Donations</h2>

    <div class="donation-container">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="donation-card">
                <img src="uploads/<?php echo $row['image1']; ?>" alt="Donation Image">
                <div class="donation-info">
                    <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($row['quantity']); ?></p>
                </div>
                <div class="donation-actions">
                    <button class="edit-btn" onclick="editDonation(<?php echo $row['donation_id']; ?>)">Edit</button>
                    <button class="delete-btn" onclick="deleteDonation(<?php echo $row['donation_id']; ?>)">Delete</button>
                </div>
            </div>
        <?php } ?>
    </div>

    <script>
        // Function to handle editing donation
        function editDonation(donationId) {
            // Redirect to edit donation page with donation ID
            window.location.href = 'edit_donation.php?id=' + donationId;
        }

        // Function to handle deleting donation
        function deleteDonation(donationId) {
            if (confirm("Are you sure you want to delete this donation?")) {
                window.location.href = 'delete_donation.php?id=' + donationId;
            }
        }
    </script>

</body>
</html>

<?php
// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
