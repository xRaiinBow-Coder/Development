<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['role'] === 'admin') {
        $navigation = '<ul class="NAV">
            <li><a href="home.php">Home</a></li>
            <li><a href="Sales.php">Previous Sales</a></li>
            <li><a href="logout.php">Log out</a></li>
            <li><a href="DisplayProducts.php">Coffee ☕️</a></li>
        </ul>';
    } else {
        $navigation = '<ul class="NAV">
            <li><a href="home.php">Home</a></li>
            <li><a href="logout.php">Log out</a></li>
            <li><a href="DisplayProducts.php">Coffee ☕️</a></li>
            <li><a href="ShopingBasket.php">Shopping Cart 🛒</a></li>
            <li><a href="Sales.php">Previous Sales</a></li>
        </ul>';
    }
} else {

    $navigation = '<ul class="NAV">
        <li><a href="home.php">Home</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php">Log in</a></li>
        <li><a href="DisplayProducts.php">Coffee ☕️</a></li>
        <li><a href="ShopingBasket.php">Cart 🛒</a></li>
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