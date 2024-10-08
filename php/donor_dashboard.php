<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <span id="userName"><?php echo htmlspecialchars($_SESSION["username"]); ?></span></h1>
        <nav>
            <ul>
                <li><a href="donor_dashboard.php">Home</a></li>
                <li><a href="add_donation.php">Add Donations</a></li>
                <li><a href="live_donations.php">Live Donations</a></li>
                <li><a href="requests.php">Requests</a></li>
                <li><a href="donation_history.php">Donation History</a></li>
                <li><a href="chat.php">Chat</a></li>
                <li><a href="donor_account.php">My Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Home</h2>
        <p>This is the Dashboard.</p>
    </main>
</body>
</html>
