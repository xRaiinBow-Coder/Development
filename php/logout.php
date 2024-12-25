<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo $_SESSION['username']; ?>
</head>
<body>
<nav class="navbar">
        <div class="nav">
            <button><a href="logout.php">log out</a></button>
        </div>
    
</body>
</html>
<?php
session_start();
session_destroy();
header("Location: main.php");
?>