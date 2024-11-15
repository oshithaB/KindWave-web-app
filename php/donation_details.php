<?php
include('db.php');
session_start();

if (!isset($_GET['id'])) {
    die("Invalid Donation ID");
}

$donation_id = intval($_GET['id']);
$sql = "SELECT d.*, u.username, u.rating FROM donations d 
        JOIN users u ON d.donor_id = u.user_id 
        WHERE d.donation_id = $donation_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Donation not found");
}
$donation = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Details</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>
    <h1><?php echo $donation['item_name']; ?></h1>
    <p>Donor: <?php echo $donation['username']; ?> (Rating: <?php echo $donation['rating']; ?>)</p>
    <p>Category: <?php echo $donation['category']; ?></p>
    <p>Description: <?php echo $donation['description']; ?></p>
    <p>Quantity Available: <?php echo $donation['quantity']; ?></p>
    <img src="<?php echo $donation['image1']; ?>" alt="Image 1">
    <img src="<?php echo $donation['image2']; ?>" alt="Image 2">
    <img src="<?php echo $donation['image3']; ?>" alt="Image 3">

    <form method="POST" action="claim_donation.php">
        <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" min="1" max="<?php echo $donation['quantity']; ?>" required>
        <button type="submit">Claim</button>
    </form>
</body>
</html>
