<?php 
    ini_set('display_errors', 1);

    require 'DB.php';

    $db = new DB;

    session_start();

    if(isset($_POST['submit']))
    {
        if(isset($_POST['username']) && isset($_POST['password']))
        {
            $username = $_POST['username'];
            $password = $_POST['password'];


            $sql = "SELECT username FROM 214_users WHERE username = :username AND password = :password";
            echo $sql;
            $query = $db->connect()->prepare($sql);
            $query->bindParam(':username', $username, PDO::PARAM_STR );
            $query->bindParam(':password', $password, PDO::PARAM_STR );
            $query->execute();

           if($query->rowCount() > 0)
           {
            $_SESSION['username']  = $username;
            header('location: sqlinjectSuccess.php');
           }
           else
           {
            echo "incorrect credentials";
           }
        }
    }
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login form</title>
</head>
<body>
    <h1>Login - secured</h1>
    
    <form method="post">
        <label for="username">username</label><br>
        <input type="text" placeholder="Enter Username.." name="username" id="Username">
        <br>
        <label for="password">password</label><br>
        <input type="text" placeholder="Enter Password.." name="password" id="password">
        <br>
        <input type="submit" name="submit" value="login">
    </form>
</body>
</html>