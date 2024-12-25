
<?php
session_start(); 
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
   $navigation = '<ul>
<li><a href="main.php">Home</a></li>
<li><a href="members.php">Events</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>';
} else {
   $navigation = '<ul>
<li><a href="main.php">Home</a></li>
<li><a href="login.php">Log In</a></li>
<li><a href="creating.php">Create Account</a></li>
</ul>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link href="navbar1.css" rel="stylesheet">
<title>Nav</title>
</head>
<body>
<?php echo $navigation; ?>

</body>
</html>