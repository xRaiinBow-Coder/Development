<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_event'])) {
        if (isset($_POST['event_id'])) {
            $eventId = $conn->real_escape_string($_POST['event_id']);
            $sql = "SELECT Name, description, Date, time FROM MyEvents WHERE id = '$eventId'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <link href="event21.css" rel="stylesheet">
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body>
                <?php include "nav.php"; ?>
                    <h1>Edit Event</h1>
                    <div class="edit">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                        <label for="Name">Event Name:</label><br>
                        <input type="text" id="Name" name="Name" value="<?php echo $row['Name']; ?>" required><br>
                        <label for="description">Event Description:</label><br>
                        <textarea id="description" name="description"><?php echo $row['description']; ?></textarea><br>
                        <label for="Date">Date:</label><br>
                        <input type="date" id="Date" name="Date" value="<?php echo $row['Date']; ?>" required><br>
                        <label for="time">Time:</label><br>
                        <input type="time" id="time" name="time" value="<?php echo $row['time']; ?>"><br>
                        <input type="submit" name="update_event" value="Update">
                    </form>
                    </div>
                </body>
                </html>
                <?php
                exit;
            } else {
                echo "<p>Event not found.</p>";
            }
        } else {
            echo "<p>Invalid request.</p>";
        }
    } elseif (isset($_POST['update_event']) && isset($_POST['event_id'])) {
        $eventId = $conn->real_escape_string($_POST['event_id']);
        $name = $conn->real_escape_string($_POST['Name']);
        $description = $conn->real_escape_string($_POST['description']);
        $date = $conn->real_escape_string($_POST['Date']);
        $time = $conn->real_escape_string($_POST['time']);

        $update_sql = "UPDATE MyEvents SET Name = '$name', description = '$description', Date = '$date', time = '$time' WHERE id = '$eventId'";
        if ($conn->query($update_sql) === TRUE) {
            header("location: members.php");
            exit;
        } else {
            echo "<p>Error updating event: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Invalid request.</p>";
    }
}

mysqli_close($conn);
?>