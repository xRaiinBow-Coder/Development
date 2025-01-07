<?php
session_start();
require_once 'DB.php';


if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    die("<p style='color: red;'>You must be logged in to view the purchase history.</p>");
}

try {
    $db = new DB();
    $pdo = $db->connect(); 
} catch (PDOException $e) {
    die("<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>");
}


function getPurchaseHistory(PDO $pdo, $username = null) {
    try {
        if ($username) {
           
            $query = "SELECT * FROM tbl_Reciepts WHERE purchaser = :username ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':username' => $username]);
        } else {
            
            $query = "SELECT * FROM tbl_Reciepts ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error fetching purchase history: " . $e->getMessage() . "</p>";
        return [];
    }
}

$username = $_SESSION['username']; 
$isAdmin = $_SESSION['role'] === 'admin'; 


$purchaseHistory = getPurchaseHistory($pdo, $isAdmin ? null : $username);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Purchase History</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

    <?php if ($isAdmin): ?>
        <h2>Admin - View All Purchase History</h2>
    <?php else: ?>
        <h2>Your Purchase History</h2>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Amount</th>
                <th>Card (Last 4 Digits)</th>
                <?php if ($isAdmin): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchaseHistory as $purchase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($purchase['id']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['name']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['address']); ?></td>
                    <td>£<?php echo number_format($purchase['amount'], 2); ?></td>
                    <td>************<?php echo substr($purchase['card'], -4); ?></td>
                    <?php if ($isAdmin): ?>
                        <td>
                            <a href="editPurchase.php?id=<?php echo $purchase['id']; ?>">Edit</a> | 
                            <a href="deletePurchase.php?id=<?php echo $purchase['id']; ?>">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!$isAdmin): ?>
        <p><a href="ShopingBasket.php">Return to the shop</a></p>
    <?php endif; ?>
</body>
</html>