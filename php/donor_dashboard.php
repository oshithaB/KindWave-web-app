<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
<?php include 'donor_navigation.php'; ?>
    <header>
        <h1>Welcome, <span id="userName"><?php echo htmlspecialchars($_SESSION["username"]); ?></span></h1>
      
    </header>
    <main>
        <h2>Home</h2>
        <p>This is the Dashboard.</p>
    </main>
</body>
</html>
