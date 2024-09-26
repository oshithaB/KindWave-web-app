<?php
session_start();
include 'db.php';

// Check if the user is logged in and has a role of donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: index.html"); // Redirect to login if not logged in
    exit();
}

// Fetch donation history for the logged-in donor
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM donation_history WHERE donor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link your CSS file -->
    <style>
        body {
            background-color: #001f3f; /* Dark blue background */
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #003366; /* Darker blue for the table */
        }
        th, td {
            border: 1px solid #fff;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #00509e; /* Lighter blue for header */
        }
        tr:nth-child(even) {
            background-color: #004080; /* Alternating row colors */
        }
        a {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <h1>Donation History</h1>
    <table>
        <thead>
            <tr>
                <th>Donation ID</th>
                <th>Category</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Images</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['donation_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image1']); ?>" alt="Image 1" width="50">
                            <img src="<?php echo htmlspecialchars($row['image2']); ?>" alt="Image 2" width="50">
                            <img src="<?php echo htmlspecialchars($row['image3']); ?>" alt="Image 3" width="50">
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No donation history found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="donor_dashboard.html">Back to Dashboard</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
