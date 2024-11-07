<?php
include("db.php");
?>
<?php
function addtitle($title, $des) {
    global $mysqli;
    $stmt = $mysqli->prepare('INSERT INTO notepad(title,description)values(?,?) ');
    $stmt->bind_param('ss', $title, $des);
    $stmt->execute();
    $stmt->close();
}
?>