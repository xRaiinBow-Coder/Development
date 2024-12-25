<!DOCTYPE html>
<html lang="en">
<head>
    <link href="event21.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Only</title>
</head>
<body>
<h1>Post an event!</h1>
    <?php include 'searchDate.php'; ?>
    <?php include 'search.php'; ?>
    <div class="event">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="Name">Event Name:</label><br>
        <input type="text" id="Name" name="Name" required><br>
        <label for="description">Event Description:</label><br>
        <textarea id="description" name="description"></textarea><br>
        <label for="Date">Date:</label><br>
        <input type="Date" id="Date" name="Date" required><br>
        <label for="time">Time:</label><br>
        <input type="time" id="time" name="time"><br>
        <input type="submit" value="submit">
    </div>
    </form> 
<div class="display">
<?php

session_start();
require "connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $Date = mysqli_real_escape_string($conn, $_POST['Date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $sql = "INSERT INTO MyEvents (Name, description, Date, time) VALUES ('$Name', '$description', '$Date', '$time')";
    if (mysqli_query($conn, $sql)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM MyEvents";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>Existing Events</h2>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p>Event Name: " . htmlspecialchars($row["Name"]) . "</p>";
        echo "<p>Description: " . htmlspecialchars($row["description"]) . "</p>";
        echo "<p>Date: " . htmlspecialchars($row["Date"]) . "</p>";
        echo "<p>Time: " . htmlspecialchars($row["time"]) . "</p>";

        echo '<form method="post" action="delete.php">';
        echo '<input type="hidden" name="event_id" value="' . htmlspecialchars($row["id"]) . '">';
        echo '<input type="submit" name="delete_event" value="Delete">';
        echo '</form><br>';

        echo '<form method="post" action="edit.php">';
        echo '<input type="hidden" name="event_id" value="' . htmlspecialchars($row["id"]) . '">';
        echo '<input type="submit" name="edit_event" value="edit">';
        echo '</form><br>';

        echo '<hr>';
    }
} else {
    echo "<p>No events found</p>";
}

?>
</div>
</body>
</html>