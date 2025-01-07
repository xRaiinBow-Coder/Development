<?php
session_start();
require_once 'DB.php'; 

$orderComplete = false;

function saveReceipt(PDO $pdo, $name, $address, $cardNumber, $totalAmount, $purchaser) {
    try {
        $query = "INSERT INTO tbl_Reciepts (name, address, card, amount, purchaser) VALUES (:name, :address, :card, :amount, :purchaser)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':address' => $address,
            ':card' => $cardNumber,
            ':amount' => $totalAmount,
            ':purchaser' => $purchaser
        ]);
        echo "<p style='color: green;'>Receipt saved successfully for $purchaser.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error saving receipt: " . $e->getMessage() . "</p>";
    }
}

function calculateTotal($basket) {
    $total = 0;
    foreach ($basket as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

try {
    $db = new DB();
    $pdo = $db->connect(); 
} catch (PDOException $e) {
    die("<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>");
}

if (isset($_POST['completePurchase'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $cardNumber = trim($_POST['card_number']);  
    $purchaser = $_SESSION['username'] ?? 'Unknown'; 

    if (empty($name) || empty($address) || empty($cardNumber)) {
        echo "<p style='color: red;'>All fields are required!</p>";
    } else {
        $totalAmount = calculateTotal($_SESSION['basket'] ?? []);
        
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

        echo "<h3>Total Amount: £" . number_format($totalAmount, 2) . "</h3>";
        unset($_SESSION['basket']); 
        echo "<h3>Thank you for your purchase, $purchaser!</h3>";

        saveReceipt($pdo, $name, $address, substr($cardNumber, -4), $totalAmount, $purchaser);
        $orderComplete = true;
    }
}

if (isset($_POST['guestCheckoutSubmit'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $cardNumber = trim($_POST['card_number']); 
    $purchaser = 'Guest'; 

    if (empty($name) || empty($address) || empty($cardNumber)) {
        echo "<p style='color: red;'>All fields are required!</p>";
    } else {
        $totalAmount = calculateTotal($_SESSION['basket'] ?? []);
        
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

        echo "<h3>Total Amount: £" . number_format($totalAmount, 2) . "</h3>";
        unset($_SESSION['basket']); 
        echo "<h3>Thank you for your purchase, Guest!</h3>";

        saveReceipt($pdo, $name, $address, substr($cardNumber, -4), $totalAmount, $purchaser);
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
<?php include 'nav.php'; ?>

    <?php if ($orderComplete): ?>
        <h1>Thank you for your purchase!</h1>
        <p>Your order has been successfully placed. You will receive a confirmation email shortly.</p>
        <p><a href="ShopingBasket.php">Return to the shop</a></p>
    <?php else: ?>
        <h2>Checkout Form</h2>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <form method="post" action="">
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" required><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" required></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" required><br><br>

                <input type="submit" name="completePurchase" value="Complete Purchase">

            </form>
        <?php else: ?>
            <form method="post" action="">
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" required><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" required></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" required><br><br>

                <input type="submit" name="guestCheckoutSubmit" value="Complete Purchase">
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>