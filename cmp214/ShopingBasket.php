<?php 
session_start();

// Initialize totalPrice to 0
$totalPrice = 0;

// Handle quantity update or item removal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle decrement
    if (isset($_POST['decrement']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        if ($_SESSION['basket'][$id]['quantity'] > 1) {
            $_SESSION['basket'][$id]['quantity']--;
        }
    }

    // Handle increment
    if (isset($_POST['increment']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $_SESSION['basket'][$id]['quantity']++;
    }

    // Handle remove item
    if (isset($_POST['remove']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        unset($_SESSION['basket'][$id]);
        $_SESSION['basket'] = array_values($_SESSION['basket']);
    }

    // Handle purchase decision (login, guest, or register)
    if (isset($_POST['purchase'])) {
        $_SESSION['purchase_option'] = true;
    }

    // If 'continue as guest' is selected, display the checkout form
    if (isset($_POST['action']) && $_POST['action'] == 'guest') {
        $_SESSION['continue_as_guest'] = true;
        unset($_SESSION['purchase_option']);
    }

    // If 'log in' is selected, redirect to login page with redirect to checkout
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        header("Location: login.php?redirect=checkout");
        exit();
    }

    // If 'register' is selected, redirect to registration page
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        header("Location: register.php");
        exit();
    }

    // Cancel checkout action
    if (isset($_POST['cancel_checkout'])) {
        unset($_SESSION['continue_as_guest']);
        header("Location: ShopingBasket.php");
        exit();
    }

    // Handle guest checkout form submission
    if (isset($_POST['guest_checkout_submit'])) {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $card_number = trim($_POST['card_number']);

        // Validate the input fields
        if (empty($name) || empty($address) || empty($card_number)) {
            echo "<p style='color: red;'>All fields are required!</p>";
        } else {
            // Display order summary
            echo "<h2>Order Summary</h2>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Shipping Address:</strong> $address</p>";
            echo "<p><strong>Card Number:</strong> ************" . substr($card_number, -4) . "</p>";

            // Display basket items
            if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
                echo "<h3>Items in your basket:</h3>";
                foreach ($_SESSION['basket'] as $item) {
                    echo "<p>{$item['name']} - £" . number_format($item['price'] * $item['quantity'], 2) . " x {$item['quantity']}</p>";
                }
            }

            // Clear the basket after checkout
            unset($_SESSION['basket']);
            echo "<h3>Thank you for your purchase!</h3>";
            exit();
        }
    }
}

// Calculate the total price from the basket
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
                <input type="submit" name="cancel_checkout" value="Cancel Checkout">
            </form>
        <?php elseif (isset($_SESSION['continue_as_guest'])): ?>
            <h2>Guest Checkout</h2>
            <form method="post" action="chekout.php">
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" required><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" required></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" required><br><br>

                <input type="submit" name="guest_checkout_submit" value="Complete Purchase">
            </form>

            <form method="post">
                <input type="submit" name="cancel_checkout" value="Cancel Checkout">
            </form>
        <?php else: ?>
            <form method="post">
                <input type="submit" name="purchase" value="Proceed to Purchase">
            </form>

            <?php if (isset($_SESSION['purchase_option']) && $_SESSION['purchase_option']): ?>
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