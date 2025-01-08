<?php
session_start();

include 'SessionHacking.php';
require_once 'DB.php'; 


$orderComplete = false;

if (isset($_POST['cancelCheckout'])) {
    header("Location: ShopingBasket.php"); 
    exit();
}

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
        echo "<p class='message';'>Reciept saved!, Thank you $purchaser.</p>";
    } catch (PDOException $e) {
        echo "<p>Error saving receipt: " . $e->getMessage() . "</p>";
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
    die("<p >Database connection failed: " . $e->getMessage() . "</p>");
}

if (isset($_POST['completePurchase'])) {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
    $cardNumber = trim($_POST['card_number']);  
    $purchaser = $_SESSION['username'] ?? 'Unknown'; 

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    if (empty($name) || empty($address) || empty($cardNumber)) {
        echo "<p>Please fill in all fields</p>";
    } elseif (!preg_match("/^\d{16}$/", $cardNumber)) { 
        echo "<p class='error'>Invalid card number. Please enter a valid 16-digit card number.</p>";
    } else {
        $totalAmount = calculateTotal($_SESSION['basket'] ?? []);
        
        echo "<div class='RecieptSummary'>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Shipping Address:</strong> $address</p>";
        echo "<p><strong>Card Number:</strong> ************" . substr($cardNumber, -4) . "</p>";

        if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
            echo "<h3>Items in your basket:</h3>";
            foreach ($_SESSION['basket'] as $item) {
                echo "<p class='item'>{$item['name']} - £" . number_format($item['price'] * $item['quantity'], 2) . " x {$item['quantity']}</p>";
            }
        }

        echo "<h3>Total Amount: £" . number_format($totalAmount, 2) . "</h3>";
        unset($_SESSION['basket']); 
        echo "<h3>Thank you for your purchase, $purchaser!</h3>";
        echo "</div>";

        saveReceipt($pdo, $name, $address, substr($cardNumber, -4), $totalAmount, $purchaser);
        $orderComplete = true;
    }
}

if (isset($_POST['guestCheckoutSubmit'])) {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
    $cardNumber = trim($_POST['card_number']); 
    $purchaser = 'Guest'; 

    // Input validation
    if (empty($name) || empty($address) || empty($cardNumber)) {
        echo "<p>Please fill in all fields</p>";
    } elseif (!preg_match("/^\d{16}$/", $cardNumber)) { 
        echo "<p class='error';'>Invalid card number. Please enter a valid 16-digit card number.</p>";
    } else {
        $totalAmount = calculateTotal($_SESSION['basket'] ?? []);

        echo "<div class='RecieptSummary'>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Shipping Address:</strong> $address</p>";
        echo "<p><strong>Card Number:</strong> ************" . substr($cardNumber, -4) . "</p>";

        if (isset($_SESSION['basket']) && !empty($_SESSION['basket'])) {
            echo "<h3>Items in your basket:</h3>";
            foreach ($_SESSION['basket'] as $item) {
                echo "<p class='item'>{$item['name']} - £" . number_format($item['price'] * $item['quantity'], 2) . " x {$item['quantity']}</p>";
            }
        }

        echo "<h3>Total Amount: £" . number_format($totalAmount, 2) . "</h3>";
        unset($_SESSION['basket']); 
        echo "<h3>Thank you for your purchase!!, Guest!</h3>";
        echo "</div>";

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
<style >
      /* General body styling */
      body {
            font-family: Arial, sans-serif;
            background-color: black;
            padding-top: 20px;
            display: flex;
            justify-content: center;  
            align-items: center;     
            height: 100vh;
            position: relative; 
        }

        h2 {
            color: white;
            text-align: center;
            position: absolute;  
            top: 200px;           
            left: 50%;           
            transform: translateX(-50%); 
        }

        .container {
            max-width: 1000px;
            width: 100%;
            padding: 20px;
            padding-left: 100px;
            box-sizing: border-box;
            z-index: 1; 
        }
        
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px #228B22;
            width: 75%;
            margin: 0 auto;          
        }   

        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: inline-block;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #6F4E37;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #228B22;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #6F4E37;
        }

        .order-summary {
            background-color:  #228B22;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .reciept {
            background-color: #228B22; 
            color: white;             
            padding: 30px;             
            border-radius: 8px;        
            text-align: center;        
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            max-width: 600px;          
            margin: 0 auto;            
            position: absolute;        
            top: 40%;                  
            left: 50%;
            transform: translate(-50%, -50%);                 
            z-index: 10;              
        }

        .RecieptSummary {
            background-color: #228B22; 
            color: white;              
            padding: 20px;             
            border-radius: 8px;        
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);     
            margin: 20px auto;         
            text-align: left;  
            max-width: 600px;
            margin-top: 250px;
            position: absolute;        
            top: 45%;                  
            left: 50%;
            transform: translate(-50%, -50%);   
        }

        .item {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        
        h3 {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }

        .reciept h1 {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        
        .reciept p {
            font-size: 18px;
            margin-bottom: 15px;
        }

        
        .reciept a {
            font-size: 18px;
            color: #fff;
            background-color: #6F4E37; 
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            border: 2px solid #fff;
        }

        .reciept a:hover {
            background-color: #fff;
            color: #228B22; 
            border-color: #228B22;
        }

        .message {
            color: pink;               
            padding: 15px;                 
            margin: 10px 0;           
            position: fixed;           
            bottom: 20px;             
            right: 20px;               
            z-index: 1000;            
        }

        .cancelButton {
            background-color: #ff4747;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }

        .ButtonsCheckout {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .error{
            color: red;               
            padding: 15px;                 
            margin: 10px 0;           
            position: fixed;           
            bottom: 20px;             
            right: 20px;               
            z-index: 1000;  
        }
</style>

    <?php if ($orderComplete): ?>
        <div class="reciept">
        <h1>Thank you for your purchase!</h1>
        <p>Your order has been successfully placed.</p>
        <p><a href="ShopingBasket.php">Return to the shop</a></p>
    </div>
    <?php else: ?>
        <h2>Checkout Form</h2>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" ><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" ></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" ><br><br>

                <input type="submit" name="completePurchase" value="Complete Purchase">
                <input type="submit" name="cancelCheckout" value="Cancel Checkout" class="cancelButton">

            </form>
        <?php else: ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
                <label for="name">Full Name:</label><br>
                <input type="text" name="name" id="name" ><br><br>

                <label for="address">Shipping Address:</label><br>
                <textarea name="address" id="address" rows="4" ></textarea><br><br>

                <label for="card_number">Card Number:</label><br>
                <input type="text" name="card_number" id="card_number" ><br><br>

                <div class="ButtonsCheckout">
                    <input type="submit" name="completePurchase" value="Complete Purchase">
                    <input type="submit" name="cancelCheckout" value="Cancel Checkout" class="cancelButton">
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>