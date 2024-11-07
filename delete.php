<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($mysqli, $_GET['id']);

    // Delete query
    $query = "DELETE FROM notes WHERE id = '$id'";
    if (mysqli_query($mysqli, $query)) {
        // Store a success message in the session
        $_SESSION['message'] = "Note successfully deleted!";
    } else {
        $_SESSION['message'] = "Error deleting note: " . mysqli_error($mysqli);
    }

    // Redirect back to the main page
    header("Location: home.php");
    exit();
} else {
    $_SESSION['message'] = "No note selected for deletion.";
    header("Location: home.php");
    exit();
}
?>
