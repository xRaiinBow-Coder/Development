<?php
 
    require 'DB.php';
    require 'product.php';
 
    ini_set(option: 'display_errors', value: 1);
 
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
 
        $db = new DB();
 
        $query = $db->connect()->prepare("SELECT * FROM tbl_Product WHERE id = '$id'");
        $query->execute();
 
        while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
        $product = new Product($row['id'], $row['name'], $row['image']);
    }
 
 
    }
?> 
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information</title>
</head>
<body>
    <h1>information for <?= $product->name() ?></h1>
    <img src="<?= $product->image() ?>" style="width: 400px; height: 400px;" />
    <p>Product Name: <?= $product->name() ?></p>
</body>
</html>