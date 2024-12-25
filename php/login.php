<!DOCTYPE html>
<html lang="en">
<head>
    <link href="log8.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo $_SESSION['username']; ?>
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Log In</h1>
<div class="login">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        
        <input type="submit" value="Submit">
        </div>
    
    
    
</body>
</html>
<?php

require 'connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $enteredusername = $_POST['username'];
  $enteredpassword = $_POST['password'];

  $enteredusername = $conn->real_escape_string($enteredusername);
  $enteredpassword = $conn->real_escape_string($enteredpassword);

  
  $sql = "SELECT id, username, Role FROM MyAccounts WHERE username = '$enteredusername' AND Password = '$enteredpassword'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['Role'] = $row['Role'];

        echo "Role: ". $row['Role']; 

        if ($row['Role'] == 'admin') {
            header("Location: main.php");
            exit;
        } else {
            header("Location: members.php");
            exit;
        }
    } else {
        echo "Invalid username or password.";
    }
}

$conn->close();
?>

