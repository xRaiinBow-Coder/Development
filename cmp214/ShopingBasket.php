<?php 
    session_start();

    // Initialize totalPrice to 0
    $totalPrice = 0;

    // Handle quantity update or item removal
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle decrement
        if (isset($_POST['decrement']) && isset($_POST['id'])) {
            $id = $_POST['id'];
            // Decrement the quantity, but ensure it doesn't go below 1
            if ($_SESSION['basket'][$id]['quantity'] > 1) {
                $_SESSION['basket'][$id]['quantity']--;
            }
        }

        // Handle increment
        if (isset($_POST['increment']) && isset($_POST['id'])) {
            $id = $_POST['id'];
            // Increment the quantity
            $_SESSION['basket'][$id]['quantity']++;
        }

        // Handle remove item
        if (isset($_POST['remove']) && isset($_POST['id'])) {
            $id = $_POST['id'];
            // Remove the item from the basket
            unset($_SESSION['basket'][$id]);
            // Reindex the array to prevent gaps in the keys
            $_SESSION['basket'] = array_values($_SESSION['basket']);
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
        <table border="1"> <!-- Adding a border to the table for clarity -->
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
                        <!-- Decrement Quantity -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" value="<?= $i ?>" name="id">
                            <input type="submit" value="-" name="decrement">
                        </form>

                        <!-- Display Quantity -->
                        <?= $item['quantity'] ?>

                        <!-- Increment Quantity -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" value="<?= $i ?>" name="id">
                            <input type="submit" value="+" name="increment">
                        </form>
                    </td>
                    <td>
                        <!-- Remove Item -->
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
    <?php endif; ?>

</body>
</html>