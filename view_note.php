<?php
session_start();
include('db.php');

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $note_id = $_GET['id'];

    // Get the note details based on the ID
    $query = "SELECT * FROM notes WHERE id = '$note_id'";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Note</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 15px;
        }

        h1 {
            color: #333;
        }

        .note-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .note-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .note-description {
            font-size: 18px;
            margin-top: 20px;
        }

        .back-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="note-description">
    <?php
    // Split the description by periods, and remove any empty elements from the array
    $sentences = array_filter(explode('.', htmlspecialchars($note['description'])));

    // Render each sentence as a line with a dot symbol at the start
    foreach ($sentences as $sentence) {
        echo "<div class='note-line'>• " . trim($sentence) . ".</div>";
    }
    ?>
</div>
<div>
    <button><a href="home.php">BACK TO HOME</a></button>
</div>

</body>
</html>
