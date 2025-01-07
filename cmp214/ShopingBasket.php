<?php 
session_start();

$totalPrice = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <?php if (!isset($_SESSION['basket']) || empty($_SESSION['basket'])): ?>
        <h2>Your basket is empty</h2>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Item</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($_SESSION['basket'] as $i => $item): ?>
                <tr>
                    <td><img src="<?= $item['image'] ?>" width="50px" height="50px" alt="Product Image"></td>
                    <td><?= $item['name'] ?></td>
                    <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" value="<?= $i ?>" name="id">
                            <input type="submit" value="-" name="decrement">
                        </form>

                        <?= $item['quantity'] ?>

                        <form method="post" style="display:inline;">
                            <input type="hidden" value="<?= $i ?>" name="id">
                            <input type="submit" value="+" name="increment">
                        </form>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" value="<?= $i ?>" name="id">
                            <input type="submit" value="Remove" name="remove">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="3" style="text-align:right;">Grand total</td>
                <td>£<?= number_format($totalPrice, 2) ?></td>
            </tr>
        </table>

        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <h2>Checkout</h2>
            <form method="post" action="chekout.php">
                <input type="submit" value="Proceed to Checkout">
            </form>

            <form method="post">
                <input type="submit" name="cancelCheckout" value="Cancel Checkout">
            </form>
        <?php elseif (isset($_SESSION['continueAsGuest'])): ?>
            <h2>Guest Checkout</h2>
            <form method="post" action="chekout.php">
            </form>
            <form method="post">
                <input type="submit" name="cancelCheckout" value="Cancel Checkout">
            </form>
        <?php else: ?>
            <form method="post">
                <input type="submit" name="purchase" value="Proceed to Purchase">
            </form>

            <?php if (isset($_SESSION['purchaseOption']) && $_SESSION['purchaseOption']): ?>
                <h3>Choose an option:</h3>
                <form method="post">
                    <input type="radio" name="action" value="login" id="login" required>
                    <label for="login">Log In</label><br>
                    <input type="radio" name="action" value="guest" id="guest">
                    <label for="guest">Continue as Guest</label><br>
                    <input type="radio" name="action" value="register" id="register">
                    <label for="register">Register</label><br>
                    <input type="submit" value="Submit">
                </form>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>