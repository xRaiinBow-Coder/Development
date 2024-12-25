<!DOCTYPE html>
<html lang="en">
<head>
    <link href="event21.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View events</title>
</head>
<body>
<div class="displayMain">
<?php 

require "connect.php";
$sql = "SELECT * FROM MyEvents ORDER BY Date DESC, time DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='event-container'>";
    echo "<h2 class='event-title'>Most Recent Event</h2>";
    $row = mysqli_fetch_assoc($result);
    echo "<div class='event-details'>";
    echo "<p class='event-property'>Event Name: <span class='event-value'>" . $row["Name"] . "</span></p>";
    echo "<p class='event-property'>Description: <span class='event-value'>" . $row["description"] . "</span></p>";
    echo "<p class='event-property'>Date: <span class='event-value'>" . $row["Date"] . "</span></p>";
    echo "<p class='event-property'>Time: <span class='event-value'>" . $row["time"] . "</span></p>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>No events found</p>";
}

?>
</div>
</body>
</html>