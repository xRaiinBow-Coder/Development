<!DOCTYPE html>
<html lang="en">
<head>
    <link href="event21.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web</title>
</head>
<body>
<div class="searchDate">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="startDate"> Enter: Start Date:</label><br>
        <input type="date" id="startDate" name="startDate">
        <label for="endDate">Enter: End Date:</label><br>
        <input type="date" id="end_date" name="endDate">
        <input type="submit" value="Search">
        <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Delete Search</button>
    </form>
</div>
<div class="displayDate">
    <?php
    session_start();
    require "connect.php";

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['startDate']) && isset($_GET['endDate'])) {
        $startDate = mysqli_real_escape_string($conn, $_GET['startDate']);
        $endDate = mysqli_real_escape_string($conn, $_GET['endDate']);
        $sql = "SELECT * FROM MyEvents WHERE Date BETWEEN '$startDate' AND '$endDate'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Events</h2>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p>Event Name: " . htmlspecialchars($row["Name"]) . "</p>";
            echo "<p>Description: " . htmlspecialchars($row["description"]) . "</p>";
            echo "<p>Date: " . htmlspecialchars($row["Date"]) . "</p>";
            echo "<p>Time: " . htmlspecialchars($row["time"]) . "</p>";
            
            echo '<hr>';
        }
    } else {
        echo "<p>No events found</p>";
    }
}
    ?>
</div>
</body>
</html>