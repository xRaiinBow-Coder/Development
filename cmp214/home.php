<?php

ini_set("display_errors", 1);
session_start();  

require 'DB.php';
require 'product.php';
require 'basketFunctions.php';

$db = new DB();

if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = array();
}

if (isset($_POST['add'])) {
    $id = $_POST['id'];  
    add($db, $id);  
    echo "Product with ID: " . $id . " added to basket.";
}

// Modify query to fetch only top 3 products
$query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY id DESC LIMIT 3"); // Corrected table name
$query->execute();

$products = array();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $id = $row["id"];
    $name = $row['name'];
    $image = $row['image'];
    $description = $row['description'];  // Fetch description
    $price = $row['price'];  // Fetch price

    // Ensure price is valid (default to 0.00 if NULL)
    $price = ($price === null) ? 0.00 : (float)$price;  // Handle null price by defaulting to 0.00

    // Pass the description and price to the Product constructor
    $products[] = new Product($id, $name, $image, $description, $price);
}

var_dump($_SESSION['basket']);  // Debugging the basket content

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <link rel="stylesheet" href="home9.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="upperBox">
        <h1 class="homeTitle">Tyne Brew</h1>
        <p class="HomeP">Welcome to our coffee shop! Where we make the best coffee on the planet.</p>
        <img src="img/cmug.png" alt="image" class="HomeImage">
    </div>

    <!-- Loop to display the top 3 products -->
    <?php foreach ($products as $p): ?>
        <div class="product-container">
            <img src="<?= $p->image() ?>" width="200px" height="200px" alt="Product Image" />
            <div class="product-info">
                <h3><?= $p->name() ?></h3>
                <p><?= $p->description() ?></p>
                <p>Price: $<?= number_format($p->price(), 2) ?></p>  <!-- Display price -->
            </div>
            <form method="post" action="">
                <input type="hidden" value="<?= $p->id() ?>" name="id">
                <input type="submit" class="btn" name="add" value="Add to Basket">
                <input type="submit" class="btn" name="add" value="View Item Info">
            </form>
        </div>
    <?php endforeach; ?>

</body>
</html>