<?php
require 'db.php'; // Include database connection

// Handle Add User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $query = "INSERT INTO users (username, password, role, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssss', $username, $password, $role, $email, $phone, $address);
    $stmt->execute();
}

// Handle Delete User Request
if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    header('Location: manage_users.php');
    exit;
}

// Handle Edit User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $query = "UPDATE users SET username = ?, role = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssi', $username, $role, $email, $phone, $address, $user_id);
    $stmt->execute();
}

// Fetch Users for Display
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM users WHERE username LIKE ? OR user_id LIKE ? OR phone LIKE ?";
$search_term = "%$search%";
$stmt = $conn->prepare($query);
$stmt->bind_param('sss', $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>

    <!-- Add User Form -->
    <form method="POST">
        <h3>Add User</h3>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="delivery">Delivery Man</option>
        </select>
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="phone" placeholder="Phone">
        <input type="text" name="address" placeholder="Address">
        <button type="submit" name="add_user">Add User</button>
    </form>

    <!-- Search Bar -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search by username, ID, or phone" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- User Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                            <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
                            <select name="role">
                                <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="delivery" <?= $row['role'] === 'delivery' ? 'selected' : '' ?>>Delivery</option>
                            </select>
                            <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                            <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>">
                            <input type="text" name="address" value="<?= htmlspecialchars($row['address']) ?>">
                            <button type="submit" name="edit_user">Edit</button>
                        </form>
                        <a href="?delete_user_id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
