<?php
session_start();
include('db.php');

// Check if subtitle_id is provided in the URL
if (isset($_GET['subtitle_id']) && !empty($_GET['subtitle_id'])) {
    $subtitle_id = mysqli_real_escape_string($mysqli, $_GET['subtitle_id']);

    // Fetch subtitle details
    $subtitle_query = "SELECT subtitle FROM subtitle WHERE id = '$subtitle_id'";
    $subtitle_result = mysqli_query($mysqli, $subtitle_query);

    if ($subtitle_result && mysqli_num_rows($subtitle_result) > 0) {
        $subtitle_data = mysqli_fetch_assoc($subtitle_result);
        $subtitle_text = $subtitle_data['subtitle'];
    } else {
        echo "Subtitle not found.";
        exit();
    }

    // Handle form submission to add description with file upload
    if (isset($_POST['add_description'])) {
        $description_text = mysqli_real_escape_string($mysqli, $_POST['description']);
        $file_name = "";

        // Check if file was uploaded
        // Define the uploads directory
$target_dir = "uploads/";

// Check if the directory exists, if not create it
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Creates directory with write permissions
}

// Check if file was uploaded
if (!empty($_FILES['file_upload']['name'])) {
    $file_name = basename($_FILES["file_upload"]["name"]);
    $target_file = $target_dir . $file_name;

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
        $file_name = mysqli_real_escape_string($mysqli, $file_name);
    } else {
        echo "Error uploading file.";
        exit();
    }
}

        // Insert description and file path into the database
        $insert_query = "INSERT INTO description (subtitle_id, description, file) VALUES ('$subtitle_id', '$description_text', '$file_name')";

        if (mysqli_query($mysqli, $insert_query)) {
            $_SESSION['message'] = "Description and file added successfully!";
            header("Location: description.php?subtitle_id=" . $subtitle_id);
            exit();
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }

    // Fetch descriptions related to the subtitle
    $desc_query = "SELECT * FROM description WHERE subtitle_id = '$subtitle_id'";
    $desc_result = mysqli_query($mysqli, $desc_query);
} else {
    echo "No subtitle selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descriptions for "<?php echo htmlspecialchars($subtitle_text); ?>"</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1 {
            color: #495057;
            text-align: center;
        }

        .container {
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            position: relative;
        }

        /* Top-right Add Description button */
        .open-modal-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .open-modal-btn:hover {
            background-color: #45a049;
        }

        /* Full-screen modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            width: 80vw;
            max-width: 80vw;
            height: 80vh;
            padding: 30px;
            border-radius: 0;
            text-align: center;
            overflow-y: auto;
            position: relative;
        }

        .modal-content textarea {
            width: 100%;
            height: 100%;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            resize: vertical;
            min-height: 200px;
            margin-top: 10px;
        }

        .modal-content button {
            margin-top: 10px;
            width: 20%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .modal-content button:hover {
            background-color: #45a049;
        }

        .close-modal-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 50%;
        }

        .close-modal-btn:hover {
            background-color: #d32f2f;
        }

        /* Descriptions table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        th:nth-child(2) {
            width: 60%; /* Adjusted width of description column */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #45a049;
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        #imageModal img {
            max-width: 80%;
            max-height: 80%;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .close-image-modal-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 50%;
        }

        .close-image-modal-btn:hover {
            background-color: #d32f2f;
        }
    </style>

    <script>
        // Function to open the image modal and display the clicked image
        function openImageModal(imageSrc) {
            document.getElementById('imageModal').style.display = 'flex';
            document.getElementById('modalImage').src = imageSrc;
        }

        // Function to close the image modal
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        function openModal() {
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</head>
<body>

<div class="container">
    <button class="open-modal-btn" onclick="openModal()">Add Description</button>

    <h1>Descriptions for "<?php echo htmlspecialchars($subtitle_text); ?>"</h1>

    <!-- Display existing descriptions -->
    <table>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Updates</th>
        </tr>

        <?php
        if ($desc_result && mysqli_num_rows($desc_result) > 0) {
            while ($desc_row = mysqli_fetch_assoc($desc_result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($desc_row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($desc_row['description']) . "</td>";
                
                // Only show image link if a file exists
                if (!empty($desc_row['file'])) {
                    $image_path = 'uploads/' . $desc_row['file'];
                    echo "<td><a href='#' onclick=\"openImageModal('$image_path')\">" . htmlspecialchars($desc_row['file']) . "</a></td>";
                } else {
                    echo "<td>No image</td>";
                }
                
                
                echo "<td>" . htmlspecialchars($desc_row['created_at']) . "</td>";
                echo "<td><a href=\"desedit.php?id=" . $desc_row['id'] . "\">Edit</a> | <a href=\"desdelete.php?id=" . $desc_row['id'] . "\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                              echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No descriptions found for this subtitle.</td></tr>";
        }
        ?>
    </table>

    <a href="home.php" class="back-button">Back to Notes</a>
</div>

<!-- Image modal for displaying the clicked image -->
<div id="imageModal" class="modal" onclick="closeImageModal()">
    <button class="close-image-modal-btn" onclick="closeImageModal()">×</button>
    <img id="modalImage" src="" alt="Selected Image">
</div>

<!-- Full-screen modal for adding a description with file upload -->
<div id="modal" class="modal">
    <div class="modal-content">
        <button class="close-modal-btn" onclick="closeModal()">×</button>
        <h2>Add Description</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <textarea name="description" placeholder="Enter description" required></textarea>
            <input type="file" name="file_upload" accept="image/*, application/pdf" style="margin-top: 15px;">
            <button type="submit" name="add_description">Add Description</button>
        </form>
    </div>
</div>


</body>
</html>