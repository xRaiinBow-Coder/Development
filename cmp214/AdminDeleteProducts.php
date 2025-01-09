<?php
session_start();
include 'SessionHacking.php';
require_once 'DB.php';

    $productId = $_POST['id'];

    try {
        $db = new DB();
        $pdo = $db->connect();

        $deleteQuery = "DELETE FROM tbl_Productss WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->execute([':id' => $productId]);

        header('location: DisplayProducts.php');

    } catch (PDOException $e) {
        die("<p style='color: red;'>Database error: " . $e->getMessage() . "</p>");
    }
?>