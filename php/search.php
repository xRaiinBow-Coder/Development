<!DOCTYPE html>
<html lang="en">
<head>
    <link href="event21.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="search1">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="search">Event Name search:</label><br>
        <input type="text" id="search" name="search">
        <input type="submit" value="Search" name="search_button">
        <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">Delete Search</button>
    </form>
</div>
<div class="displaySearch">
    <?php
    session_start();
    require "connect.php";

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search_button']) && isset($_GET['search'])) {
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $sql = "SELECT * FROM MyEvents WHERE Name LIKE '%$search%'";
        
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