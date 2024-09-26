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

// Fetch the donation ID from the URL
if (!isset($_GET['id'])) {
    header("Location: live_donations.php"); // Redirect if ID is not provided
    exit();
}

$donation_id = $_GET['id'];

// Fetch the donation details
$sql = "SELECT * FROM donations WHERE donation_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: live_donations.php"); // Redirect if donation not found
    exit();
}

$donation = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donation</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link your CSS file -->
</head>
<body>

<h2>Edit Donation</h2>

<form action="update_donation.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>">
    
    <label for="item_name">Item Name:</label>
    <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($donation['item_name']); ?>" required>

    <label for="category">Category:</label>
    <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($donation['category']); ?>" required>

    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($donation['quantity']); ?>" required>

    <label for="image1">Donation Image:</label>
    <input type="file" id="image1" name="image1">
    <p>Current Image: <img src="uploads/<?php echo $donation['image1']; ?>" alt="Current Image" style="width:100px;height:auto;"></p>

    <button type="submit">Update Donation</button>
</form>

<a href="live_donations.php">Cancel</a>

</body>
</html>

<?php
// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
