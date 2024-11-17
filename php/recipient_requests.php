<?php
// Include database connection
require_once 'db.php';

// Update status to 'accepted' and move data to users table
if (isset($_POST['accept'])) {
    $tempUserId = $_POST['temp_user_id'];
    
    // Retrieve the temp user data
    $query = "SELECT * FROM temp_user WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tempUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tempUserData = $result->fetch_assoc();

    if ($tempUserData) {
        // Insert data into the users table
        $insertQuery = "INSERT INTO users (username, password, role, email, phone, address, profile_picture, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param(
            "ssssssss",
            $tempUserData['username'],
            $tempUserData['password'],
            $tempUserData['role'],
            $tempUserData['email'],
            $tempUserData['phone'],
            $tempUserData['address'],
            $tempUserData['profile_picture'],
            $tempUserData['created_at']
        );
        $stmtInsert->execute();

        // Remove user from temp_user table
        $deleteQuery = "DELETE FROM temp_user WHERE id = ?";
        $stmtDelete = $conn->prepare($deleteQuery);
        $stmtDelete->bind_param("i", $tempUserId);
        $stmtDelete->execute();

        echo "<p>User has been accepted and moved to the users table successfully.</p>";
    } else {
        echo "<p>User not found.</p>";
    }
}

// Fetch all temp users
$query = "SELECT * FROM temp_user";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Requests</title>
</head>
<body>
    <h1>Recipient Requests</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Profile Picture</th>
                <th>Proof</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['address'] ?></td>
                        <td>
                            <?php if (!empty($row['profile_picture'])): ?>
                                <img src="<?= $row['profile_picture'] ?>" alt="Profile Picture" width="100">
                            <?php else: ?>
                                No Picture
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['proof_document'])): ?>
                                <a href="<?= $row['proof_document'] ?>" download>Download Proof</a>
                            <?php else: ?>
                                No Proof
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="temp_user_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="accept">Accept</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No recipient requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
