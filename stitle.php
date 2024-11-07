<?php
session_start();
include('db.php');

// Check if an ID is passed via the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $title_id = mysqli_real_escape_string($mysqli, $_GET['id']);

    // Query to get the main title information
    $title_query = "SELECT title FROM notes WHERE id = '$title_id'";
    $title_result = mysqli_query($mysqli, $title_query);

    if ($title_result && mysqli_num_rows($title_result) > 0) {
        $title_data = mysqli_fetch_assoc($title_result);
        $title_name = $title_data['title'];
    } else {
        echo "Title not found.";
        exit();
    }

    // Search functionality for subtitles (if a search keyword is passed)
    $search_query = "";
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search_keyword = mysqli_real_escape_string($mysqli, $_POST['search']);
        $search_query = "AND stitle LIKE '%$search_keyword%'";
    }

    // Query to get subtitles based on search and title ID
    $sname_query = "SELECT * FROM subtitle WHERE note_id = '$title_id' $search_query";
    $sname_result = mysqli_query($mysqli, $sname_query);
} else {
    echo "No title selected.";
    exit();
}

// Handling subtitle addition via POST
if (isset($_POST['add_subtitle'])) {
    // Get the subtitle text entered by the user
    $new_subtitle = mysqli_real_escape_string($mysqli, $_POST['stitle']);
    $note_id = $title_id;  // Use the $title_id from the GET parameter, which represents the note's ID

    // Insert the new subtitle into the subtitle table, linking it to the note using the note_id
    $insert_query = "INSERT INTO subtitle (note_id, subtitle) VALUES ('$note_id', '$new_subtitle')";
    
    if (mysqli_query($mysqli, $insert_query)) {
        $_SESSION['message'] = "Subtitle added successfully!";
        // Redirect back to the page to refresh content
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $note_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($mysqli);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subtitles for <?php echo htmlspecialchars($title_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h1 {
            color: #333;
            text-align: center;
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

        .back-button {
            display: inline-block;
            margin: 20px 0;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        .search-bar {
            width: 100%;
            max-width: 400px;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar input {
            width: 85%;
            padding: 8px;
            font-size: 16px;
        }

        .add-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .add-button:hover {
            background-color: #45a049;
        }

        .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
}

/* Resizing input field */
.modal-content input[type="text"], .modal-content input[type="email"], .modal-content input[type="password"], .modal-content textarea {
    width: 100%;  /* Ensures input takes up full width inside modal */
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 10px;
    box-sizing: border-box; /* Includes padding and border in the width */
    min-height: 40px; /* Minimum height for input field */
}

.modal-content input[type="text"]:focus, .modal-content input[type="email"]:focus, .modal-content input[type="password"]:focus, .modal-content textarea:focus {
    border-color: #4CAF50; /* Border color when focused */
    outline: none;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

    </style>
</head>
<body>

<h1>Subtitles for "<?php echo htmlspecialchars($title_name); ?>"</h1>

<!-- Search Bar -->
<div class="search-bar">
    <form method="POST" action="">
        <input type="text" name="search" placeholder="Search subtitles" value="<?php echo isset($search_keyword) ? htmlspecialchars($search_keyword) : ''; ?>">
    </form>
</div>

<!-- Add Subtitle Button -->
<button class="add-button" id="addSubtitleBtn">Add Subtitle</button>

<!-- Modal for Adding Subtitle -->
<div id="addSubtitleModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add Subtitle</h2>
        <form method="POST" action="">
            <input type="text" name="stitle" placeholder="Enter subtitle" required>
            <button type="submit" name="add_subtitle">Add Subtitle</button>
        </form>
    </div>
</div>

<!-- Table to display subtitles -->
<table>
    <tr>
        <th>ID</th>
        <th>Subtitle</th>
        <th>Created By</th>
        <th>Update</th>
    </tr>

    <?php
    // Check if there are subtitles to display
    if ($sname_result && mysqli_num_rows($sname_result) > 0) {
        while ($row = mysqli_fetch_assoc($sname_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td><a href=\"description.php?subtitle_id=" . $row['id'] . "\">" . htmlspecialchars($row['subtitle']) . "</a></td>";
            echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
            echo "<td><a href=\"subedit.php?id=" . $row['id'] . "\">Edit</a> | <a href=\"subdelete.php?id=" . $row['id'] . "\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No subtitles found for this title.</td></tr>";
    }
    ?>
</table>
<a href="home.php" class="back-button">Back to Notes</a>

<script>
    // Modal Script
    var modal = document.getElementById("addSubtitleModal");
    var btn = document.getElementById("addSubtitleBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
