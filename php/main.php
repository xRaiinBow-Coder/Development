<!DOCTYPE html>
<html lang="en">
<head>
  <link href="FrontPage15.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo $_SESSION['username']; ?>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="Details">
    <h2>Who are we?</h2>
    <p class="text">This website is designed for the local community and those who are struggling with the cost of living crisis.
      we aim to bring the community closer, because we dont want anyone to be suffering alone. So please come along and take part in this amazing community.
      We hope you sign up for an account and participate in the local events which get hosted by the community, such as yourself.

      No one should be suffering alone!

      I hope this webpage serves you well, finding those who are in a similar position is always better than going through it alone.
    </p>
    
  </div>
  <?php include 'ViewEvents.php'; ?>
  <div class="footer">
    <ul>
      <li class="Name">Kieran Lee Jefferson</li>
      <li class="Student">1285670</li>
      <img src="img.png" alt="image" height="20px" width="40px">
    </ul>
  </div>
</body>
</html>
<?php
require 'connect.php';

$sql = "CREATE TABLE MyAccounts (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(30) NOT NULL,
Password VARCHAR(30) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}


$conn->close();
?>