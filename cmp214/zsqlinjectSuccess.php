<?php 

    session_start();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged in</title>
</head>
<body>
    <h1> you have logged into your account! <?= $_SESSION['username']?> </h1>
</body>
</html>