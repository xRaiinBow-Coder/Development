<?php
session_start();  

include 'SessionHacking.php';
require 'DB.php';
require 'product.php';

// Establish DB connection
$db = new DB();

if (isset($_POST['id'])) {

    $productId = $_POST['id'];

    
    $query = $db->connect()->prepare("SELECT description FROM tbl_Productss WHERE id = :id");
    $query->bindParam(':id', $productId, PDO::PARAM_INT);
    $query->execute();

    
    $product = $query->fetch(PDO::FETCH_ASSOC);

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    if ($product) {
        $description = $product['description']; 
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "No product selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Info</title>
    <style>
      .item {
        margin-top: 150px; 
        padding: 20px;     
        border: 2px solid #6F4E37; 
        background-color: #f9f9f9; 
        border-radius: 10px; 
        max-width: 600px;  
        margin-left: auto;   
        margin-right: auto;  
}   


    </style>
   
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="item">
        <div class="product-container">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
            <h2>Product Description</h2>
            <p><strong>Description:</strong> <?= $description ?></p>
        </div>
    </section>

</body>
</html>