

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo $_SESSION['username']; ?>
</head>
<body>
    <?php include 'nav.php'; ?>

    <?php include 'events.php' ?>
</body>
</html>
<?php
require "connect.php";

$sql = "CREATE TABLE MyEvents (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(255) NOT NULL,
  description TEXT,
  Date DATE NOT NULL,
  time TIME
)";

if ($conn->query($sql) === TRUE) {
  echo "events table created";
} else {
  echo "Error creating table: " . $conn->error;
}
?>
