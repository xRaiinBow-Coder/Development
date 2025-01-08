<?php
session_start();  

require 'DB.php';
require 'product.php';

// Establish DB connection
$db = new DB();

if (isset($_POST['id'])) {
    // Retrieve the product ID from the form submission
    $productId = $_POST['id'];

    // Fetch only the description of the product based on the ID
    $query = $db->connect()->prepare("SELECT description FROM tbl_Productss WHERE id = :id");
    $query->bindParam(':id', $productId, PDO::PARAM_INT);
    $query->execute();

    // Fetch the product description
    $product = $query->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $description = $product['description']; // Now you have only the description
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
            <h2>Product Description</h2>
            <p><strong>Description:</strong> <?= $description ?></p>
        </div>
    </section>

</body>
</html>