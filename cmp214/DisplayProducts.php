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

$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'id-asc';


list($filterColumn, $sortOrder) = explode('-', $sortOption);
if ($filterColumn === 'price') {
    $query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY CAST(price AS DECIMAL(10, 2)) $sortOrder");
} elseif ($filterColumn === 'name') {
    $query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY name $sortOrder");
} else {
    $query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY $filterColumn");
}

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
    <style>
        body {
            background-color: #228B22;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 2.5rem;
            color: black;
        }

        .Filter {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .products1 {
            display: flex;                      
            flex-wrap: wrap;                    
            justify-content: center;            
            gap: 20px;                         
            margin: 20px;                       
        }

        .productsBox h3 {
            color: white;
        }

        .ProductsBox {
            background-color: #6F4E37;        
            border: none;          
            border-radius: 8px;                 
            box-shadow: 0 4px 8px black;  
            padding: 20px;                      
            text-align: center;                 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            width: 300px;                       
            height: 500px;                      
        }

        .ProductsBox img {
            width: 100%;      
            height: 200px;      
            object-fit: cover;  
            border-radius: 8px; 
        }

        .productInfo p {
            color: #228B22;
            font-size: 100%;
        }

        .ProductsBox .btn {
            background-color:  #228B22;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 50%;      
            display: block;  
            margin: 0 auto; 
        }

        .ProductsBox .btn:hover {
            background-color: white;
            color: black;
        }

    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>All Products</h1>
    <div class="Filter">
        <form method="get" action="">
            <label for="sort">Filter:</label>
            <select name="sort" id="sort">
                <option value="id-asc" <?= $sortOption === 'id-asc' ? 'selected' : '' ?>>Default</option>
                <option value="name-asc" <?= $sortOption === 'name-asc' ? 'selected' : '' ?>>A to Z</option>
                <option value="name-desc" <?= $sortOption === 'name-desc' ? 'selected' : '' ?>>Z to A</option>
                <option value="price-asc" <?= $sortOption === 'price-asc' ? 'selected' : '' ?>>Price Low to High</option>
                <option value="price-desc" <?= $sortOption === 'price-desc' ? 'selected' : '' ?>>Price High to Low</option>
            </select>
            
            <button type="submit" class="btn">Apply</button>
        </form>
    </div>

    <section class="products1">
    <?php if (empty($products)): ?>
        <p>No products available at the moment.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="ProductsBox">
                <img src="<?= htmlspecialchars($product->image(), ENT_QUOTES, 'UTF-8') ?>" width="200px" height="200px" alt="<?= htmlspecialchars($product->name(), ENT_QUOTES, 'UTF-8') ?>" />
                <div class="productInfo">
                    <h3><?= htmlspecialchars($product->name(), ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($product->description(), ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Price: £<?= number_format($product->price(), 2) ?></p>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
                    <input type="hidden" name="id" value="<?= htmlspecialchars($product->id(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="submit" class="btn" name="add" value="Add to Basket">
                </form>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <form method="post" action="AdminDeleteProducts.php">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product->id(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="submit" class="btn" name="delete" value="Delete Product">
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
</body>
</html>