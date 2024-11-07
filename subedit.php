<?php
session_start();
include('db.php');

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $note_id = $_GET['id'];

    // Fetch the current note data from the database
    $query = "SELECT * FROM subtitle WHERE id = '$note_id'";
    $result = mysqli_query($mysqli, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($mysqli));
    }

    $note = mysqli_fetch_array($result);

    // Check if the note exists
    if (!$note) {
        echo "Note not found.";
        exit();
    }
} else {
    echo "No note selected.";
    exit();
}

// Update the note if the form is submitted
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($mysqli, $_POST['subtitle']);
    // Update the note in the database
    $update_query = "UPDATE subtitle SET subtitle = '$title' WHERE id = '$note_id'";
    if (mysqli_query($mysqli, $update_query)) {
        // Redirect back to the home page after updating
        header("Location: stitle.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($mysqli);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        .edit-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        textarea {
            resize: vertical;
        }

        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            align-self: center;
        }

        .submit-button:hover {
            background-color: #45a049;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Note</h2>
        <form method="post" action="">
            <label for="title">SubTitle</label>
            <input type="text" id="title" name="subtitle" value="<?php echo htmlspecialchars($note['subtitle']); ?>" required>

            <input type="submit" name="update" value="Save Changes" class="submit-button">
        </form>

        <div class="back-link">
            <a href="home.php">Back to Notes</a>
        </div>
    </div>
</body>
</html>
