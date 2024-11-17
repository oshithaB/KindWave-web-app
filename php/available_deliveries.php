<?php
include('db.php');
session_start();

// Update request status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Prevent changing status after delivered
    $check_sql = "SELECT status FROM requests WHERE request_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $request_id);
    $check_stmt->execute();
    $current_status = $check_stmt->get_result()->fetch_assoc()['status'];

    if ($current_status === 'delivered') {
        echo 'delivered_error';
        exit;
    }

    // Update status
    $sql = "UPDATE requests SET status = ? WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $request_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit;
}

// Fetch requests with status 'accepted'
$sql = "
    SELECT 
        r.request_id,
        u.username AS recipient_name,
        u.phone AS recipient_phone,
        u.address AS recipient_address,
        r.quantity,
        r.status AS request_status,
        r.request_date,
        donations.item_name,
        donations.category AS item_type
    FROM 
        requests r
    INNER JOIN 
        users u ON r.recipient_id = u.user_id
    INNER JOIN 
        donations ON r.donation_id = donations.donation_id
    WHERE 
        r.status = 'accepted'
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Requests</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function changeStatus(requestId, newStatus) {
            $.ajax({
                url: '',  // Same page for processing
                method: 'POST',
                data: {
                    request_id: requestId,
                    status: newStatus
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Status updated successfully!');
                        location.reload();
                    } else if (response === 'delivered_error') {
                        alert('Status cannot be changed after delivered.');
                    } else {
                        alert('Error updating status.');
                    }
                }
            });
        }

        function openMap(address) {
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`, '_blank');
        }
    </script>
</head>
<body>

<h1>Available Requests</h1>

<table border="1">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Item Type</th>
            <th>Recipient Name</th>
            <th>Recipient Contact</th>
            <th>Recipient Address</th>
            <th>Quantity</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['item_name']; ?></td>
                <td><?php echo ucfirst($row['item_type']); ?></td>
                <td><?php echo $row['recipient_name']; ?></td>
                <td><?php echo $row['recipient_phone']; ?></td>
                <td>
                    <?php echo $row['recipient_address']; ?>
                    <button onclick="openMap('<?php echo $row['recipient_address']; ?>')">View Location</button>
                </td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['request_date']; ?></td>
                <td><?php echo ucfirst($row['request_status']); ?></td>
                <td>
                    <?php if ($row['request_status'] === 'delivered'): ?>
                        <span>Delivered</span>
                    <?php else: ?>
                        <button onclick="changeStatus(<?php echo $row['request_id']; ?>, 'up_for_delivery')">Up for Delivery</button>
                        <button onclick="changeStatus(<?php echo $row['request_id']; ?>, 'delivered')">Delivered</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
