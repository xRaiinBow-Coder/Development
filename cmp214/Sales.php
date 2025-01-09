<?php
session_start();

include 'SessionHacking.php';
require_once 'DB.php';


if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    die("You must be logged in to view the purchase history.");
}

try {
    $db = new DB();
    $pdo = $db->connect(); 
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
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
        echo "<Error fetching purchase history: " . $e->getMessage();
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
    <style>
       body {
        background-color: #228B22;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        height: 100vh; 
    }

    .container {
        width: 80%; 
        max-width: 1000px;
        text-align: center;
        padding: 20px;
        background-color:white;
        border-radius: 10px;
        box-shadow: 0px 0px 15px black;
        margin-top: 20px; 
        overflow: auto; 
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        overflow-x: auto;
    }

    table, th, td {
        border: 1px solid black;
    }

    th, td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #6F4E37;
    }

    a {
        text-decoration: underline;
        color:red;

    }

    a:hover {
        color: green;
    }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

        <?php if ($isAdmin): ?>
            <h2>Company Transaction History</h2>
        <?php else: ?>
            <h2>Your Purchase History</h2>
        <?php endif; ?>

        <table>
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
                                <a href="Adminedit.php?id=<?php echo $purchase['id']; ?>">Edit</a> | 
                                <a href="Admindelete.php?id=<?php echo $purchase['id']; ?>">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>