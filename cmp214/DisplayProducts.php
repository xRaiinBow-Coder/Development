<?php

ini_set("display_errors", 1);
session_start();

include 'SessionHacking.php';
require 'DB.php';
require 'product.php';
require 'basketFunctions.php';

$db = new DB();


if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = array();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

   
    if (isset($_POST['add'])) {
        $productId = $_POST['id']; 
        add($db, $productId); 
        echo "Product with ID: " . htmlspecialchars($productId) . " added to basket.";
    }
}

$filterColumn = isset($_GET['filter']) && in_array($_GET['filter'], ['id', 'name', 'price']) ? $_GET['filter'] : 'id';

$query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY $filterColumn");
$query->execute();

$products = array();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $productId = $row["id"];
    $productName = $row['name'];
    $productImage = $row['image'];
    $productDescription = $row['description'];
    $productPrice = $row['price'];

    
    $productPrice = ($productPrice === null) ? 0.00 : (float)$productPrice;

    $products[] = new Product($productId, $productName, $productImage, $productDescription, $productPrice);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link rel="stylesheet" href="home2.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>All Products</h1>
    <div class="filter-container">
        <form method="get" action="">
            <label for="filter">Sort by:</label>
            <select name="filter" id="filter">
                <option value="id" <?= $filterColumn === 'id' ? 'selected' : '' ?>>Default</option>
                <option value="name" <?= $filterColumn === 'name' ? 'selected' : '' ?>>Name</option>
                <option value="price" <?= $filterColumn === 'price' ? 'selected' : '' ?>>Price</option>
            </select>
            <button type="submit" class="btn">Apply</button>
        </form>
    </div>

    <section class="products">
        <?php if (empty($products)): ?>
            <p>No products available at the moment.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-container">
                    <img src="<?= htmlspecialchars($product->image(), ENT_QUOTES, 'UTF-8') ?>" width="200px" height="200px" alt="<?= htmlspecialchars($product->name(), ENT_QUOTES, 'UTF-8') ?>" />
                    <div class="product-info">
                        <h3><?= htmlspecialchars($product->name(), ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($product->description(), ENT_QUOTES, 'UTF-8') ?></p>
                        <p>Price: $<?= number_format($product->price(), 2) ?></p>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product->id(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="submit" class="btn" name="add" value="Add to Basket">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</body>
</html>