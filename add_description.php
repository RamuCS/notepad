<!DOCTYPE html>
<html>
<head>
    <title>Add Description</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }

        .description-container {
            width: 90%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        textarea {
            width: 100%;
            height: 400px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<?php
include("db.php");

// Handle form submission
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $des = $_POST['des'];
    $sname = $_POST['sname'];

    // Clean data to prevent SQL injection
    $name = mysqli_real_escape_string($mysqli, $name);
    $des = mysqli_real_escape_string($mysqli, $des);
    $sname = mysqli_real_escape_string($mysqli, $sname);

    // Validate form inputs
    if ($name == "" || $des == "" || $sname == "") {
        echo "<div class='error'>All fields should be filled. Either one or many fields are empty.</div>";
    } else {
        // Check if title already exists in the notes table
        $check_query = "SELECT * FROM notes WHERE title = '$name'";
        $check_result = mysqli_query($mysqli, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<div class='error'>This title already exists in your notes.</div>";
        } else {
            // Insert the note into the notes table
            $query = "INSERT INTO notes (title, description) VALUES ('$name', '$des')";
            if (mysqli_query($mysqli, $query)) {
                // Get the last inserted note's ID
                $note_id = mysqli_insert_id($mysqli);

                // Now insert the subtitle into the subtitle table
                $subtitle_query = "INSERT INTO subtitle (id, subtitle) VALUES ('$id', '$sname')";
                if (mysqli_query($mysqli, $subtitle_query)) {
                    echo "<script>alert('Note and Subtitle added successfully!'); window.location.href='home.php';</script>";
                } else {
                    echo "Error adding subtitle: " . mysqli_error($mysqli);
                }
            } else {
                echo "Error: " . mysqli_error($mysqli);
            }
        }
    }
}
?>

<div class="description-container">
    <h1>Enter Description</h1>
    <form name="descriptionForm" method="post" action="">
        <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>" />
        <input type="hidden" name="sname" value="<?php echo $_POST['sname']?>">
        <textarea name="des" placeholder="Enter your description here..."></textarea>
        <input type="submit" name="submit" value="Save Note">
    </form>
</div>

</body>
</html>
