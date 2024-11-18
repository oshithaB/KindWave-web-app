<?php
include('db.php');
session_start();

// Update request status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Update the request status in the database
    $sql = "UPDATE requests SET status = ? WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $request_id);

    if ($stmt->execute()) {
        // If status updated successfully, return success
        echo 'success';
    } else {
        // If there's an error, return failure
        echo 'error';
    }
    exit;  // Stop further processing after updating status
}

// Fetch requests with 'in_review' status, prioritizing "food" items, and join with donor info
$sql = "
    SELECT 
        r.request_id,
        u.username AS recipient_name,
        d.item_name,
        d.category AS item_type,
        d.donation_id AS item_id,
        u.address AS recipient_address,
        r.quantity,
        r.status,
        d.image1 AS item_image,
        donor.username AS donor_name,
        donor.phone AS donor_phone
    FROM 
        requests r
    INNER JOIN 
        users u ON r.recipient_id = u.user_id
    INNER JOIN 
        donations d ON r.donation_id = d.donation_id
    INNER JOIN
        users donor ON d.donor_id = donor.user_id
    WHERE 
        r.status = 'in_review'  -- Only show requests with 'in_review' status
    ORDER BY 
        CASE d.category
            WHEN 'food' THEN 1
            ELSE 2
        END, 
        r.request_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Requests</title>
    <link rel="stylesheet" href="body.css">
    <?php include 'admin_navigation.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function changeStatus(requestId) {
            var newStatus = 'accepted';  // The status we want to set

            $.ajax({
                url: '',  // We are submitting to the same page
                method: 'POST',
                data: {
                    request_id: requestId,
                    status: newStatus
                },
                success: function(response) {
                    if (response == 'success') {
                        alert('Status updated to accepted successfully!');
                        location.reload();  // Reload to show updated status
                    } else {
                        alert('Error updating status.');
                    }
                }
            });
        }
    </script>
</head>
<body>

<h1>Claim Requests</h1>

<table border="1">
    <thead>
        <tr>
            <th>Item Image</th>
            <th>Recipient Name</th>
            <th>Item Name</th>
            <th>Item Type</th>
            <th>Quantity</th>
            <th>Recipient Address</th>
            <th>Donor Name</th>
            <th>Donor Contact</th>
            <th>Status</th>
            <th>Change Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="uploads/<?php echo $row['item_image']; ?>" alt="<?php echo $row['item_name']; ?>" width="100" height="100"></td>
                <td><?php echo $row['recipient_name']; ?></td>
                <td><?php echo $row['item_name']; ?></td>
                <td><?php echo $row['item_type']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['recipient_address']; ?></td>
                <td><?php echo $row['donor_name']; ?></td>
                <td><?php echo $row['donor_phone']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] != 'accepted'): ?>
                        <button onclick="changeStatus(<?php echo $row['request_id']; ?>)">Accept Request</button>
                    <?php else: ?>
                        <span>Accepted</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
