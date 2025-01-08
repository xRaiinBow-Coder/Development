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

        echo "<p>Deleted successfully.</p>";
        echo "<p><a href='DisplayProducts.php'>This way back to products</a></p>";

    } catch (PDOException $e) {
        die("<p style='color: red;'>Database error: " . $e->getMessage() . "</p>");
    }
?>