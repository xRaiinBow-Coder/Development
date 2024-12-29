<?php
session_start();

// Initialize order completion flag
$orderComplete = false;

// Handle logged-in user checkout form submission
if (isset($_POST['complete_purchase'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $card_number = trim($_POST['card_number']);

    // Validate the input fields
    if (empty($name) || empty($address) || empty($card_number)) {
        echo "<p style='color: red;'>All fields are required!</p>";
    } else {
        // Display order summary for logged-in users
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
        echo "<h3>Thank you for your purchase, " . $_SESSION['username'] . "!</h3>";
        $orderComplete = true;
    }
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
        // Display order summary for guest users
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
        echo "<h3>Thank you for your purchase, Guest!</h3>";
        $orderComplete = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <?php if ($orderComplete): ?>
        <h1>Thank you for your purchase!</h1>
        <p>Your order has been successfully placed. You will receive a confirmation email shortly.</p>
        <p><a href="ShopingBasket.php">Return to the shop</a></p>
    <?php else: ?>
        <h2>Checkout</h2>

        <!-- For logged-in users -->
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <form method="post" action="">
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" required><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" required></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" required><br><br>

                <input type="submit" name="complete_purchase" value="Complete Purchase">
            </form>
        <!-- For guest users -->
        <?php else: ?>
            <form method="post" action="">
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" required><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" required></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" required><br><br>

                <input type="submit" name="guest_checkout_submit" value="Complete Purchase">
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>