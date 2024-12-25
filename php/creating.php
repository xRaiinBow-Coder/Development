<!DOCTYPE html>
<html lang="en">
<head>
    <link href="Create4.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo $_SESSION['username']; ?>
</head>
<body>
<?php include 'nav.php'; ?>
<h1>Create an Account</h1>
<div class="create">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="username">Create a Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="lastname">Creat a password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        
        <input type="submit" value="Submit">
        </div>
    
</body>
</html>
<?php

require 'connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
  $username = $conn->real_escape_string($_POST['username']);
  $password = $conn->real_escape_string($_POST['password']);

  $sql = "INSERT INTO MyAccounts (username, password) VALUES ('$username', '$password')";

  if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
      header("location: login.php");
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
}


$conn->close();
?>