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


$query = $db->connect()->prepare("SELECT * FROM tbl_Product");
$query->execute();

$products = array();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $id = $row["id"];
    $name = $row['name'];
    $image = $row['image'];

    
    $products[] = new Product($id, $name, $image, $description, $price);
}


var_dump($_SESSION['basket']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
</head>
<body>
    <h1>Product List</h1>

    <?php foreach ($products as $p): ?>
        <div>
            <img src="<?= $p->image() ?>" width="200px" height="200px" alt="Product Image" />
            <form method="post" action="">  <!-- Form submits to the same page -->
                <input type="hidden" value="<?= $p->id() ?>" name="id">
                <input type="submit" class="btn" name="add" value="Add to Basket">
            </form>
        </div>
    <?php endforeach; ?>

</body>
</html>