<?php
session_start();

include 'SessionHacking.php';  
require_once 'DB.php';

$purchaseId = $_GET['id'] ?? null;  

if (!$purchaseId) {
    die("Purchase ID is missing.");
}

try {
    $db = new DB();
    $pdo = $db->connect();
    
    
    $query = "SELECT * FROM tbl_Reciepts WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $purchaseId]);
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$purchase) {
        die("Purchase not found");
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed");
        }
        
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        
        if (empty($name) || empty($address)) {
            echo "All fields are required!";
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
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Purchase</title>
    <style>

        body {
            background-color: #228B22;
        }

        h1 {
            position: absolute;  
            top: 20%;           
            left: 50%;           
            transform: translateX(-50%); 
        }

        .EditForm {
            position: absolute;  
            top: 30%;           
            left: 50%;           
            transform: translateX(-50%); 
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px black;
            background-color: #6F4E37;
            padding: 20px;   
            width: 600px;    
            display: flex;   
            flex-direction: column;  
            gap: 10px;
    
        }

        .EditForm label {
            text-align: center;
            text-transform: uppercase;
        }

        input[type="submit"] {
            background-color:  #228B22;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 50%;      
            display: block;  
            margin: 0 auto;  
        }

        input[type="submit"]:hover {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <h1>Purchase - ID: <?php echo htmlspecialchars($purchase['id']); ?></h1>

    <form method="post" class="EditForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  

        <label for="name">Full Name:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($purchase['name']); ?>" required><br><br>

        <label for="address">Shipping Address:</label><br>
        <textarea name="address" id="address" rows="4" required><?php echo htmlspecialchars($purchase['address']); ?></textarea><br><br>

        <input type="submit" name="updatePurchase" value="Update Purchase">
    </form>

</body>
</html>