<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);


session_start();

require 'DB.php';
include 'SessionHacking.php';


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
    <link rel="stylesheet" href="style2.css">
    <title>Login Form</title>
</head>
<body>
<?php include 'nav.php'; ?>
    
    <h1>Login - Secured</h1>

    
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <p>Welcome, <?= $_SESSION['username'] ?>!</p>
        <p><a href="chekout.php">Proceed to Checkout</a></p>
    <?php else: ?>
        <form method="post">

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
