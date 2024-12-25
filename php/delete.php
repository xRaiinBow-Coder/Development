<!DOCTYPE html>
<html lang="en">
<head>
    <link href="event21.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Page</title>
</head>
<body>
    <div class="delete">
<form method="post" action="delete.php">
    <input type="hidden" name="event_id" value="<?php echo $row["id"]; ?>">
    <input type="submit" name="delete_event" value="Delete">

</form>
</div>
</body>
</html>
<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("location: login.php");
   exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
   $eventId = $conn->real_escape_string($_POST['event_id']);

   $sql = "SELECT Name, description, Date, time FROM MyEvents WHERE id = '$eventId'";
   $result = $conn->query($sql);
   if ($result->num_rows > 0) {
       $row = $result->fetch_assoc();

       $deleteEventSql = "DELETE FROM MyEvents WHERE id = '$eventId'";
       if ($conn->query($deleteEventSql) === TRUE) {
           echo "<p>Event deleted successfully.</p>";
           header("location: members.php");
           exit;
       } else {
           echo "<p>Error deleting event: " . $conn->error . "</p>";
       }
   } else {
       echo "<p>Event not found.</p>";
   }
} else {
   echo "<p>Invalid request.</p>";
}
?>