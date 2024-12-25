<?php 

ini_set("display_errors", 1);

require 'DB.php';
$db = new DB;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"href="style2.css">
    <title>Document</title>
</head>
<body>
    <?php include 'nav.php';?>
    <form method="post">
        <input type="email" placeholder="Email..." name="email">
        <input type="text" placeholder="Username..." name="username">
        <input type="password" placeholder="Password" name="password">
        <input type="password"placeholder="Password Confirm" name="confpass">
        <input type="submit" value="sign-up" name="register">

    </form>


    <?php 

    if(isset($_POST['register'])){
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confpass = $_POST['confpass'];

        if(empty($email) || empty($username) || empty($password)|| empty($confpass)){
            echo'you must fillin all fields of the form!';
            die();
        }

        if($password != $confpass){
            echo'your passwords must match';
            die();
        }

        if(strlen($email) < 5 || strlen($email) > 50){
            echo'your email does not meet the  length requirements';
            die();
        }

        if(strlen($username) < 3|| strlen($username) > 12){
            echo'your username does not meet the length requirements';
            die();
        }

        if(strlen($password) < 3|| strlen($password) > 100){
            echo'yourpassword does not meet the length requirements';
            die();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo 'invalid email address';
            die();
        }

        if(!preg_match("/^[a-zA-Z0-9_]*$/", $username)){
            echo 'invalid username';
            die();
        }

        $conn = $db->connect();
        if(!$conn){echo'database failed to connect';}

        $emailQuery = $conn->prepare('SELECT * FROM tbl_users WHERE email = :email OR username = :username');
        $emailQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $emailQuery->bindParam(':username', $username, PDO::PARAM_STR);
        $emailQuery->execute();

        if($emailQuery->rowCount() > 0){
            echo'Email is already in use!';
            die();
        }
        else{
            $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
            $uuid = generateUUID();
            $insertQuery = $conn->prepare('INSERT INTO tbl_users (email, username, password, uuid) VALUES (:email, :username, :password, :uuid)');
            $insertQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $insertQuery->bindParam(':username', $username, PDO::PARAM_STR);
            $insertQuery->bindParam(':password', $encryptedPassword, PDO::PARAM_STR);
            $insertQuery->bindParam(':uuid', $uuid, PDO::PARAM_STR);
            $insertQuery->execute();

            if($insertQuery){
                echo 'account successfully crafted';
                header("location: login.php");
            }
        }

    }

    function generateUUID(){
        return time() . bin2hex(random_bytes(8));
    }

    ?>

</body>




</html>