<?php


include 'SessionHacking.php';
require 'DB.php';

if(isset($_POST['upload'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];  
    $price = $_POST['price']; 

    $targetDir = "img/";
    $targetFile = $targetDir . basename($_FILES['image']['name']);
    $type = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $supported = ['image/jpg', 'image/jpeg', 'image/png'];
    $canUpload = false;

    if(file_exists($targetFile)) {
        $canUpload = false;
        die("Image file has already been uploaded");
    }

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    if($_FILES['image']['size'] > 3000000 || $_FILES['image']['size'] === 0) {
        $canUpload = false;
        die("Incorrect file size. Must be less than 3MB");
        
    }

    if(!in_array(mime_content_type($_FILES['image']['tmp_name']), $supported)) {
        $canUpload = false;
        die("Incorrect MIME type. Image file must be JPEG, JPG, PNG");
    }

    if(!getimagesize($_FILES['image']['tmp_name'])) {
        $canUpload = false;
        die("File is not an image.");
    } else {
        $canUpload = true;
    }

    if($canUpload) {
        if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $db = new DB;

            $query = $db->connect()->prepare("INSERT INTO tbl_Productss (name, description, price, image) VALUES (:name, :description, :price, :image)");
            $query->bindParam(':name', $name);
            $query->bindParam(':description', $description);
            $query->bindParam(':price', $price);
            $query->bindParam(':image', $targetFile);

            $query->execute();

            header('Location: create.php');
            echo "Succes";
            exit();  
        } else {
            die("Error uploading file.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <style>

        body {
            background-color: #228B22
        }

        h1 {
            position: absolute;  
            top: 40%;           
            left: 50%;           
            transform: translateX(-50%); 
        }

        .CreatePost {
            position: absolute;  
            top: 30%;           
            left: 50%;           
            transform: translateX(-50%); 
            border: 1px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 4px 8px black;
            background-color: #6F4E37;
            padding: 20px;   
            width: 600px;    
            display: flex;   
            flex-direction: column;  
            gap: 5px;
        }

        .CreatePost label {
            text-align: center;
        }

        input[type="submit"] {
            background-color:  #228B22;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 50%;      
            display: block;  
            margin: 0 auto;  
        }

        input[type="file"] {
            background-color:  #228B22;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 40%;      
            display: block;  
            margin: 0 auto;
            text-transform: uppercase;
        }

        input[type="file"]:hover {
            background-color: white;
            color: black;
        }

        input[type="submit"]:hover {
            background-color: white;
            color: black;
        }
        
    </style>
</head>
<body>


<?php include 'nav.php'; ?>
    <h1>Create A New Product</h1>

    <form method="post" action="create.php" enctype="multipart/form-data" class="CreatePost">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">  <!-- CSRF Token -->
        
        <label for="name">New Product Name:</label><br>
        <input type="text" placeholder="Enter a product name" id="name" name="name" required><br><br>


        <label for="description">Product Description:</label><br>
        <textarea id="description" name="description" placeholder="Enter a product description" required></textarea><br><br>
        <label for="price">Product Price:</label><br>
        <input type="number" step="0.01" placeholder="Enter product price" id="price" name="price" required><br><br>

        <label for="image">Select An Image File:</label><br>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <input type="submit" name="upload" value="Upload">
    </form>
</body>
</html>