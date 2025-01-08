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

if (isset($_POST['add'])) {
    // CSRF token validation
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $id = $_POST['id'];  
    add($db, $id);  
    echo "Product with ID: " . $id . " added to basket.";
}

$query = $db->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY id DESC LIMIT 3"); // Corrected table name
$query->execute();

$products = array();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $id = $row["id"];
    $name = $row['name'];
    $image = $row['image'];
    $description = $row['description'];  
    $price = $row['price'];  

    $price = ($price === null) ? 0.00 : (float)$price;

    $products[] = new Product($id, $name, $image, $description, $price);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <link rel="stylesheet" href="home2.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="upperBox">
        <div class="left-side">
            <h1 class="homeTitle" id="Title1" style="color: black;">Tyne Brew</h1>
            <p class="HomeP">Welcome to our coffee shop! Where we make the best coffee on the planet.</p>
        </div>
        <div class="right-side">
            <img src="img/cmug.png" alt="image" class="HomeImage">
        </div>
    </div>

    <section class="products">
        <?php foreach ($products as $p): ?>
            <div class="product-container">
                <img src="<?= $p->image() ?>" width="200px" height="200px" alt="Product Image" />
                <div class="product-info">
                    <h3><?= $p->name() ?></h3>
                    <p><?= $p->description() ?></p>
                    <p>Price: $<?= number_format($p->price(), 2) ?></p>  
                </div>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
                    <input type="hidden" value="<?= $p->id() ?>" name="id">
                    <input type="submit" class="btn" name="add" value="Add to Basket">
                    
                    <input type="submit" class="btn" name="view" value="Info" formaction="ViewInfo.php?product_id=<?= $p->id() ?>">
                </form>
            </div>
        <?php endforeach; ?>
    </section>

</body>
</html>