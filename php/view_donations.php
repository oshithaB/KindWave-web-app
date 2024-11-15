<?php
include('db.php');
session_start();

// Fetch search and category filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch donations based on search and category
$sql = "SELECT d.*, u.username, u.rating FROM donations d 
        JOIN users u ON d.donor_id = u.user_id 
        WHERE d.item_name LIKE '%$search%'";
if ($category) {
    $sql .= " AND d.category = '$category'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Donations</title>
    <style>
        /* Add your CSS styling here for cards, search bar, and dropdown */
    </style>
</head>
<body>
    <h1>View Donations</h1>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search donations..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="category">
            <option value="">All Categories</option>
            <option value="clothes" <?php echo $category == 'clothes' ? 'selected' : ''; ?>>Clothes</option>
            <option value="food" <?php echo $category == 'food' ? 'selected' : ''; ?>>Food</option>
            <option value="supplies" <?php echo $category == 'supplies' ? 'selected' : ''; ?>>Supplies</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="donations">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h2><?php echo $row['item_name']; ?></h2>
                <p>Donor: <?php echo $row['username']; ?> (Rating: <?php echo $row['rating']; ?>)</p>
                <p>Category: <?php echo $row['category']; ?></p>
                <p>Description: <?php echo $row['description']; ?></p>
                <p>Quantity Available: <?php echo $row['quantity']; ?></p>
                <img src="<?php echo $row['image1']; ?>" alt="Donation Image" style="max-width:100%;">
                <a href="donation_details.php?id=<?php echo $row['donation_id']; ?>">View</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
