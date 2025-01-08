<?php 
session_start();

include 'SessionHacking.php';

$totalPrice = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    
    if (isset($_POST['decrement']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        if ($_SESSION['basket'][$id]['quantity'] > 1) {
            $_SESSION['basket'][$id]['quantity']--;
        }
    }

    
    if (isset($_POST['increment']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $_SESSION['basket'][$id]['quantity']++;
    }

    
    if (isset($_POST['remove']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        unset($_SESSION['basket'][$id]);
        $_SESSION['basket'] = array_values($_SESSION['basket']);
    }

    
    if (isset($_POST['purchase'])) {
        $_SESSION['purchaseOption'] = true;
    }

   
    if (isset($_POST['action']) && $_POST['action'] == 'guest') {
        $_SESSION['continueAsGuest'] = true;
        unset($_SESSION['purchaseOption']);
        
        header("Location: chekout.php");
        exit();
    }

    
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        header("Location: login.php?redirect=checkout");
        exit();
    }

    
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        header("Location: register.php");
        exit();
    }

    
    if (isset($_POST['cancelCheckout'])) {
        unset($_SESSION['continueAsGuest']);
        header("Location: ShopingBasket.php");
        exit();
    }

    
    if (isset($_POST['guestCheckoutSubmit'])) {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $cardNumber = trim($_POST['cardNumber']);

        
        if (empty($name) || empty($address) || empty($cardNumber)) {
            echo "<p style='color: red;'>All fields are required!</p>";
        } else {
            
            echo "<h2>Order Summary</h2>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Shipping Address:</strong> $address</p>";
            echo "<p><strong>Card Number:</strong> ************" . substr($cardNumber, -4) . "</p>";

            
            if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
                echo "<h3>Items in your basket:</h3>";
                foreach ($_SESSION['basket'] as $item) {
                    echo "<p>{$item['name']} - £" . number_format($item['price'] * $item['quantity'], 2) . " x {$item['quantity']}</p>";
                }
            }

            
            unset($_SESSION['basket']);
            echo "<h3>Thank you for your purchase!</h3>";
            exit();
        }
    }
}


if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
    foreach ($_SESSION['basket'] as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Basket</title>
    <style >
    body {
    height: 100vh; 
    margin: 0;
    padding-top: 100px; 
    background-color: #228B22;
    border-radius: 25px;
}

.Title1 {
    position: absolute;
    top: 10%; 
    left: 50%;
    transform: translateX(-50%); 
}

.basket {
    width: 80%; 
    padding: 20px;
    background-color: #6F4E37; 
    border-radius: 25px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    margin-left: auto;
    margin-right: auto; 
    margin-top: 10%; 
}

.items {
    margin-bottom: 20px; 
}

.items img {
    width: 200px;
    height: 200px;
    margin-right: 15px;
    border-radius: 5px;
    border: 2px solid #000; 
}

.items form {
    display: inline-block;
}

.actions {
    display: flex;  
    flex-direction: column; 
    align-items: flex-end;  
    margin-right: 100%;
}


.Choice form {
    width: 20%; 
    margin: 0 auto; 
    padding: 20px;
    background-color: grey;
    text-align: center;
    z-index: 100;
    margin-top: 20px; 
    border-radius: 15px;
}

.OptionForm {
    text-align: center; 
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px; 
    color: #000; 
}

.Proceed1 {
    text-align: center; 
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px; 
    color: #000; 
}

.Proceed1 input[type="submit"] {
    background-color:  black;
    font-size: 16px;
    color:  white;;
    cursor: pointer;
    border-radius: 5px;
    border-radius: solid black;
}

.Proceed1 input[type="submit"]:hover {
    background-color: #4E3629; 
    color: #228B22; 
}

.Choice form .radio {
    margin-bottom: 15px; 
    display: flex;
    justify-content: left; 
    align-items: center; 
}

.Choice input[type="radio"] {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    cursor: pointer;
    accent-color: #6F4E37; 
}

.Choice input[type="submit"] {
    background-color:  black;
    font-size: 16px;
    color:  white;;
    cursor: pointer;
    border-radius: 5px;
    border-radius: solid black;
}

.Choice input[type="submit"]:hover {
    background-color: #4E3629; 
    color: #228B22; 
}





    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <?php if (!isset($_SESSION['basket']) || empty($_SESSION['basket'])): ?>
        <h2 class="Title1">Your basket is empty</h2>
    <?php else: ?>
        <div class="basket">
            <?php foreach ($_SESSION['basket'] as $i => $item): ?>
                <div class="items">
                    <img src="<?= $item['image'] ?>" alt="Product Image">
                    <span><?= $item['name'] ?> - £<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    <span> x <?= $item['quantity'] ?></span>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                        <input type="hidden" value="<?= $i ?>" name="id">
                        <input type="submit" value="-" name="decrement">
                    </form>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                        <input type="hidden" value="<?= $i ?>" name="id">
                        <input type="submit" value="+" name="increment">
                    </form>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                        <input type="hidden" value="<?= $i ?>" name="id">
                        <input type="submit" value="Remove" name="remove">
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="total-price">
                <h3>Grand Total: £<?= number_format($totalPrice, 2) ?></h3>
            </div>
        </div>

        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <h2>Checkout</h2>
            <form method="post" action="chekout.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                <input type="submit" value="Proceed to Checkout">
            </form>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                <input type="submit" name="cancelCheckout" value="Cancel Checkout">
            </form>
        <?php elseif (isset($_SESSION['continueAsGuest'])): ?>
            <h2>Guest Checkout</h2>
            <form method="post" action="chekout.php">
                <!-- Guest checkout form content -->
            </form>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                <input type="submit" name="cancelCheckout" value="Cancel Checkout">
            </form>
        <?php else: ?>
            <form method="post" class="Proceed1">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                <input type="submit" name="purchase" value="Proceed to Purchase">
            </form>

            <?php if (isset($_SESSION['purchaseOption']) && $_SESSION['purchaseOption']): ?>
                <div class="Choice">
                    <h3 class="OptionForm">Choose an option:</h3>
                    <form method="post">
                        <div class="radio">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                            <input type="radio" name="action" value="login" id="login" required>
                            <label for="login">Log In</label>
                        </div>

                        <div class="radio">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                            <input type="radio" name="action" value="guest" id="guest">
                            <label for="guest">Continue as Guest</label>
                        </div>

                        <div class="radio">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                            <input type="radio" name="action" value="register" id="register">
                            <label for="register">Register</label>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"> <!-- CSRF Token -->
                        <input type="submit" value="Submit">
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>