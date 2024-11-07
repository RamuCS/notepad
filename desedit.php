<?php
session_start();
include('db.php');

// Check if id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $desc_id = mysqli_real_escape_string($mysqli, $_GET['id']);

    // Fetch description details
    $desc_query = "SELECT * FROM description WHERE id = '$desc_id'";
    $desc_result = mysqli_query($mysqli, $desc_query);

    if ($desc_result && mysqli_num_rows($desc_result) > 0) {
        $desc_data = mysqli_fetch_assoc($desc_result);
        $description_text = $desc_data['description'];
        $current_file = $desc_data['file'];
    } else {
        echo "Description not found.";
        exit();
    }

    // Handle form submission to update description with file upload
    if (isset($_POST['update_description'])) {
        $description_text = mysqli_real_escape_string($mysqli, $_POST['description']);
        $file_name = $current_file; // Retain the current file unless a new file is uploaded

        // Check if a new file was uploaded
        if (!empty($_FILES['file_upload']['name'])) {
            $file_name = basename($_FILES["file_upload"]["name"]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $file_name;

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
                $file_name = mysqli_real_escape_string($mysqli, $file_name);
            } else {
                echo "Error uploading file.";
                exit();
            }
        }

        // Update description and file path in the database
        $update_query = "UPDATE description SET description = '$description_text', file = '$file_name' WHERE id = '$desc_id'";

        if (mysqli_query($mysqli, $update_query)) {
            $_SESSION['message'] = "Description updated successfully!";
            header("Location: description.php?subtitle_id=" . $desc_data['subtitle_id']);
            exit();
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
} else {
    echo "No description selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Description</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #343a40;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 800px;
        }

        textarea {
            width: 800px;
            height: 400px;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            resize: vertical;
            margin-bottom: 15px;
            outline: none;
        }

        textarea:focus {
            border-color: #4CAF50;
        }

        input[type="file"] {
            display: block;
            margin: 0 auto 15px;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
        }

        button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        a.back-button {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }

        a.back-button:hover {
            background-color: #0056b3;
        }

        /* For mobile responsiveness */
        @media screen and (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Description</h1>

    <!-- Form for editing the description -->
    <form method="POST" action="" enctype="multipart/form-data">
        <textarea name="description" placeholder="Enter description" required><?php echo htmlspecialchars($description_text); ?></textarea>
        <input type="file" name="file_upload" accept="image/*, application/pdf">
        <button type="submit" name="update_description">Update Description</button>
    </form>

    <a href="description.php?subtitle_id=<?php echo $desc_data['subtitle_id']; ?>" class="back-button">Back to Descriptions</a>
</div>

</body>
</html>
