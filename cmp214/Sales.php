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

    <?php if (!empty($purchaseHistory)): ?>
        <?php foreach ($purchaseHistory as $purchase): ?>
            <div style="border: 1px solid #ddd; margin: 10px; padding: 10px;">
                <h3>Order ID: <?php echo htmlspecialchars($purchase['id']); ?></h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($purchase['name']); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($purchase['address']); ?></p>
                <p><strong>Amount:</strong> £<?php echo number_format($purchase['amount'], 2); ?></p>
                <p><strong>Card (Last 4 Digits):</strong> ************<?php echo substr($purchase['card'], -4); ?></p>

                <?php if ($isAdmin): ?>
                    <p>
                        <a href="editPurchase.php?id=<?php echo $purchase['id']; ?>">Edit</a> | 
                        <a href="deletePurchase.php?id=<?php echo $purchase['id']; ?>">Delete</a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No purchase history found.</p>
    <?php endif; ?>

    <?php if (!$isAdmin): ?>
        <p><a href="ShopingBasket.php">Return to the shop</a></p>
    <?php endif; ?>
</body>
</html>