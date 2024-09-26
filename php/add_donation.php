<?php
session_start();
include 'db.php';
require_once 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_title = $_POST['donation_title'] ?? '';
    $description = $_POST['description'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $category = $_POST['category'] ?? '';
    $image = $_FILES['image'] ?? null;

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Basic validation
    if (empty($donation_title) || empty($description) || empty($quantity) || empty($category) || empty($image['name'])) {
        $error = "All fields are required.";
    }

    // Check if image file is valid
    if (empty($error)) {
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
        } elseif ($image["size"] > 2000000) { // Limit file size to 2MB
            $error = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png"])) { // Only allow specific formats
            $error = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        }
    }

    // If no errors, try to upload the file and insert into the database
    if (empty($error) && move_uploaded_file($image["tmp_name"], $target_file)) {
        // Insert donation into the database
        $query = "INSERT INTO donations (donation_title, description, quantity, category, image) 
                  VALUES ('$donation_title', '$description', '$quantity', '$category', '$target_file')";
        if (mysqli_query($conn, $query)) {
            $success = "Donation added successfully!";
        } else {
            $error = "Error adding donation: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; background-color: #f4f4f4; }
        h2 { color: #333; }
        form { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input[type="text"], input[type="number"], input[type="file"], select {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #5cb85c; color: white; border: none; padding: 10px; cursor: pointer;
        }
        input[type="submit"]:hover { background-color: #4cae4c; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Add Donation</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="donation_title">Donation Title:</label>
        <input type="text" name="donation_title" id="donation_title" required>

        <label for="description">Description:</label>
        <input type="text" name="description" id="description" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>

        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="food">Food</option>
            <option value="clothes">Clothes</option>
            <option value="supplies">Supplies</option>
        </select>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <input type="submit" value="Add Donation">
    </form>
</body>
</html>
