<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $navigation = '<ul class="NAV">
 <li><a href="home.php">Home</a></li>
 <li><a href="logout.php">Log out</a></li>
<li><a href="">Shopping cart 🛒</a></li>
 </ul>';
 } else {
    // Navigation for guests
    $navigation = '<ul class="NAV">
 <li><a href="home.php">Home</a></li>
 <li><a href="register.php">Register</a></li>
 <li><a href="login.php">Log in</a></li>
 <li><a href="">Coffee ☕️</a></li>
 <li><a href="">Cart 🛒</a></li>
 </ul>';
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet"href="style2.css">
</head>
<body>
    <?php echo $navigation; ?>
    
</body>
</html>