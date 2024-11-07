<?php
session_start();
include('db.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];

    // Clean data to prevent SQL injection
    $name = mysqli_real_escape_string($mysqli, $name);

    // Validate form inputs
    if ($name == "" ) {
        echo "<div class='error'>All fields should be filled. Either one or many fields are empty.</div>";
    } else {
        // Check if title already exists in the notes table
        $check_query = "SELECT * FROM notes WHERE title = '$name'";
        $check_result = mysqli_query($mysqli, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('This title already exists in your notes.')</script>";
        } else {
            // Insert the note into the notes table
            $query = "INSERT INTO notes (title) VALUES ('$name')";
            if (mysqli_query($mysqli, $query)) {
                // Get the last inserted note's ID
                $note_id = mysqli_insert_id($mysqli);
            } else {
                echo "Error: " . mysqli_error($mysqli);
            }
        }
    }
}
?>


<?php $searchTerm = "";

// Set default query to retrieve all notes, ordered alphabetically by title
$query = "SELECT * FROM notes ORDER BY title ASC";

// Check if a search term is submitted
if (isset($_POST['search']) && !empty($_POST['searchTerm'])) {
    $searchTerm = mysqli_real_escape_string($mysqli, $_POST['searchTerm']);
    $query = "SELECT * FROM notes WHERE title LIKE '%$searchTerm%' ORDER BY title ASC";
}

// Check if the clear button is pressed, reset the query to show all notes
if (isset($_POST['clear'])) {
    $searchTerm = "";
}

// Execute the query
$result = mysqli_query($mysqli, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($mysqli));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-button:hover {
            background-color: #45a049;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }

        .search-bar input[type="submit"] {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-bar input[type="submit"]:hover {
            background-color: #45a049;
        }

        .search-bar input[type="submit"].clear {
            background-color: #ff4b4b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e0e0e0;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin: 10px auto;
            width: 100%;
            max-width: 600px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>
        Welcome Dhevanadhan!!!!
        <a href="index.php" class="add-button">Add Title</a>
    </h1>

    <!-- Display success message if available -->
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='message'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']); // Clear message after displaying
    }
    ?>

    <!-- Search bar -->
    <div class="search-bar">
        <form method="post" action="">
            <input type="text" name="searchTerm" placeholder="Search by title" value="<?php echo isset($searchTerm) ? htmlspecialchars($searchTerm) : ''; ?>">
            <input type="submit" name="search" value="Search">
            <input type="submit" name="clear" value="Clear" class="clear">
        </form>
    </div>

    <!-- Table to display notes -->
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Created</th>
            <th>Edit</th>
        </tr>

        <?php
        // Check if there are results
        if (mysqli_num_rows($result) > 0) {
            // Loop through the result set using a while loop
            while ($res = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $res['id'] . "</td>";
                echo "<td><a href='stitle.php?id=" . $res['id'] . "'>" . $res['title'] . "</a></td>";  // Make title clickable 
                echo "<td>" . $res['created_by'] . "</td>";  

                // Edit and Delete links
                echo "<td><a href=\"edit.php?id=" . $res['id'] . "\">Edit</a> | <a href=\"delete.php?id=" . $res['id'] . "\" onClick=\"return confirm('Are you sure you want to delete? id = " . $res['id'] . ", title = " . $res['title'] . "')\">Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No notes found.</td></tr>";
        }
    
        ?>
    </table>
</body>
</html>
