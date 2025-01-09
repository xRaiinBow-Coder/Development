<?php

error_reporting(E_ALL);


session_start();

include 'SessionHacking.php';
require 'DB.php';



if (isset($_POST['login'])) {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $loginIdentifier = trim($_POST['loginIdentifier']);
    $loginPassword = trim($_POST['loginPassword']);

    if (empty($loginIdentifier) || empty($loginPassword)) {
        echo "Both fields are required.";
        exit;
    }

    $db = new DB;
    $conn = $db->connect();

    if (!$conn) {
        echo "Failed to connect to the database.";
        exit;
    }

   
    $query = $conn->prepare('SELECT * FROM tbl_users WHERE email = :loginIdentifier OR username = :loginIdentifier');
    $query->bindParam(':loginIdentifier', $loginIdentifier, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $user = $query->fetch(PDO::FETCH_ASSOC);

       
        if (password_verify($loginPassword, $user['password'])) {
            session_regenerate_id(true); 

            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['uuid'] = $user['uuid'];
            $_SESSION['role'] = $user['Role']; 

           
            if ($user['role'] === 'admin') {
                header("Location: DisplayProducts.php");
            } else {
                header("Location: home.php");
            }
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No account found with that email or username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" >
    <title>Login Form</title>
    <style>

        body {
            background-color: #228B22
        }

        .LogInForm {
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

        h1 {
            margin-top: 75px;
            font-weight: bold;
            color: #fff;
            position: absolute;  
            top: 15%;           
            left: 45%;           
        }



    </style>
</head>
<body>
<?php include 'nav.php'; ?>
    
    <h1>Login Form</h1>

    
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <p>Welcome, <?= $_SESSION['username'] ?>!</p>
        <p><a href="chekout.php">Proceed to Checkout</a></p>
    <?php else: ?>
        <form method="post" class="LogInForm">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="loginIdentifier">Email or Username</label><br>
            <input type="text" placeholder="Enter Email or Username..." name="loginIdentifier" id="loginIdentifier" required><br>
            
            <label for="loginPassword">Password</label><br>
            <input type="password" placeholder="Enter Password..." name="loginPassword" id="loginPassword" required><br>

            <input type="submit" name="login" value="Login">
        </form>
    <?php endif; ?>

</body>
</html> 
