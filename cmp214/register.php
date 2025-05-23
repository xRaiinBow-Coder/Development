<?php 



include 'SessionHacking.php';
require 'DB.php';
$db = new DB;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="">
    <title>Registration</title>
    <style>

    body {
        background-color: #228B22;
    }

    .RegisterForm {
        position: absolute;  
        top: 30%;           
        left: 50%;           
        transform: translateX(-50%); 
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 4px 8px black;
        background-color: #6F4E37;
        padding: 20px;   
        width: 600px;    
        display: flex;   
        flex-direction: column;  
        gap: 10px;
        backdrop-filter: blur(10px);
    }

    input[type="submit"] {
        background-color:  #228B22;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        width: 50%;      
        display: block;  
        margin: 0 auto;  
    }

    input[type="submit"]:hover {
        background-color: white;
        color: black;
    }

    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <form method="post" class="RegisterForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
        <input type="text" placeholder="Username..." name="username" required>    
        <input type="email" placeholder="Email..." name="email" required>
        <input type="password" placeholder="Password" name="password" required>
        <input type="password" placeholder="Password Confirm" name="confpass" required>
        
        <!-- Optional role dropdown (for admin use only, can be hidden for regular users) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        <?php endif; ?>
        
        <input type="submit" value="Sign-Up" name="register">
    </form>

    <?php 

    if(isset($_POST['register'])){
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confpass = trim($_POST['confpass']);
        $role = isset($_POST['role']) && $_SESSION['role'] === 'admin' ? $_POST['role'] : 'user'; // Default to 'user'

        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed.");
        }

        // Validate input
        if(empty($email) || empty($username) || empty($password) || empty($confpass)){
            echo 'You must fill in all fields of the form!';
            die();
        }

        if($password != $confpass){
            echo 'Your passwords must match.';
            die();
        }

        if(strlen($email) < 5 || strlen($email) > 50){
            echo 'Your email does not meet the length requirements.';
            die();
        }

        if(strlen($username) < 3 || strlen($username) > 12){
            echo 'Your username does not meet the length requirements.';
            die();
        }

        if(strlen($password) < 3 || strlen($password) > 100){
            echo 'Your password does not meet the length requirements.';
            die();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo 'Invalid email address.';
            die();
        }

        if(!preg_match("/^[a-zA-Z0-9_]*$/", $username)){
            echo 'Invalid username. Only alphanumeric characters and underscores are allowed.';
            die();
        }

        $conn = $db->connect();
        if(!$conn){
            echo 'Database failed to connect.';
            die();
        }

        $emailQuery = $conn->prepare('SELECT * FROM tbl_users WHERE email = :email OR username = :username');
        $emailQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $emailQuery->bindParam(':username', $username, PDO::PARAM_STR);
        $emailQuery->execute();

        if($emailQuery->rowCount() > 0){
            echo 'Email or Username is already in use!';
            die();
        } else {
            
            $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
            $uuid = generateUUID();

            
            $insertQuery = $conn->prepare('INSERT INTO tbl_users (email, username, password, uuid, role) VALUES (:email, :username, :password, :uuid, :role)');
            $insertQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $insertQuery->bindParam(':username', $username, PDO::PARAM_STR);
            $insertQuery->bindParam(':password', $encryptedPassword, PDO::PARAM_STR);
            $insertQuery->bindParam(':uuid', $uuid, PDO::PARAM_STR);
            $insertQuery->bindParam(':role', $role, PDO::PARAM_STR);

            if($insertQuery->execute()){
                echo 'Account successfully created.';
                header("location: login.php");
                exit;
            } else {
                echo 'Failed to create account.';
                die();
            }
        }
    }

    function generateUUID(){
        return time() . bin2hex(random_bytes(8));
    }

    ?>

</body>
</html>