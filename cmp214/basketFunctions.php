<?php
session_start();


function add($db, $id)
{
    $query = $db->connect()->prepare("SELECT * FROM tbl_Productss WHERE id = :id");
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        
        if (isset($_SESSION['basket']) && array_search($id, array_column($_SESSION['basket'], 'id')) !== false) {
           
            $key = array_search($id, array_column($_SESSION['basket'], 'id'));
            $_SESSION['basket'][$key]['quantity']++;
        } else {
          
            $toAdd = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'image' => $row['image'],
                'price' => $row['price'],  
                'quantity' => 1  
            );

            $_SESSION['basket'][] = $toAdd;
        }
    } else {
        echo "Product not found!";
    }
}
?>