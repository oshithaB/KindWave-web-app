<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_title = $_POST['donation_title'] ?? '';
    $description = $_POST['description'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $category = $_POST['category'] ?? '';
    $image1 = $_FILES['image1'] ?? null;
    $image2 = $_FILES['image2'] ?? null;
    $image3 = $_FILES['image3'] ?? null;

    $target_dir = "uploads/";
    $target_files = [];
    $error = '';

    // Process each image
    foreach ([$image1, $image2, $image3] as $index => $image) {
        if (!empty($image['name'])) {
            $target_file = $target_dir . basename($image["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate image
            $check = getimagesize($image["tmp_name"]);
            if ($check === false) {
                $error = "File is not an image.";
                break;
            } elseif ($image["size"] > 2000000) {
                $error = "Sorry, your file is too large.";
                break;
            } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                $error = "Sorry, only JPG, JPEG, & PNG files are allowed.";
                break;
            }

            // Move the uploaded file
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                $target_files[] = $target_file; // Store the file path
            } else {
                $error = "Error uploading file.";
                break;
            }
        } else {
            $target_files[] = null; // No image uploaded for this index
        }
    }

    // Basic validation for other fields
    if (empty($donation_title) || empty($description) || empty($quantity) || empty($category)) {
        $error = "All fields are required.";
    }

    // If no errors, insert into the database
    if (empty($error)) {
        $donor_id = $_SESSION['user_id']; // Ensure user_id is stored in session

        // Prepare image columns
        $image1_path = $target_files[0] ?? null;
        $image2_path = $target_files[1] ?? null;
        $image3_path = $target_files[2] ?? null;

        // Insert donation into the database
        $query = "INSERT INTO donations (donor_id, category, item_name, quantity, description, image1, image2, image3) 
                  VALUES ('$donor_id', '$category', '$donation_title', '$quantity', '$description', '$image1_path', '$image2_path', '$image3_path')";
        
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
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
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

        <label for="image1">Upload Image 1:</label>
        <input type="file" name="image1" id="image1" accept="image/*" required>

        <label for="image2">Upload Image 2:</label>
        <input type="file" name="image2" id="image2" accept="image/*">

        <label for="image3">Upload Image 3:</label>
        <input type="file" name="image3" id="image3" accept="image/*">

        <input type="submit" value="Add Donation">
    </form>
</body>
</html>
