<?php
session_start();

include 'SessionHacking.php';  
require_once 'DB.php';

$purchaseId = $_GET['id'] ?? null;  

if (!$purchaseId) {
    die("<p style='color: red;'>Purchase ID is missing.</p>");
}

try {
    $db = new DB();
    $pdo = $db->connect();
    
    
    $query = "SELECT * FROM tbl_Reciepts WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $purchaseId]);
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$purchase) {
        die("<p style='color: red;'>Purchase not found.</p>");
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("<p style='color: red;'>CSRF token validation failed.</p>");
        }
        
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        
        if (empty($name) || empty($address)) {
            echo "<p style='color: red;'>All fields are required!</p>";
        } else {
    
            $updateQuery = "UPDATE tbl_Reciepts SET name = :name, address = :address WHERE id = :id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([
                ':name' => $name,
                ':address' => $address,
                ':id' => $purchaseId
            ]);

            //
            header('Location: Sales.php');
            exit;  
        }
    }
} catch (PDOException $e) {
    die("<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Purchase</title>
</head>
<body>
    <h1>Edit Purchase - Order ID: <?php echo htmlspecialchars($purchase['id']); ?></h1>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  

        <label for="name">Full Name:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($purchase['name']); ?>" required><br><br>

        <label for="address">Shipping Address:</label><br>
        <textarea name="address" id="address" rows="4" required><?php echo htmlspecialchars($purchase['address']); ?></textarea><br><br>

        <input type="submit" name="updatePurchase" value="Update Purchase">
    </form>

</body>
</html>