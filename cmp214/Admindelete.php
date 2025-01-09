<?php
session_start();

include 'SessionHacking.php';
require_once 'DB.php';

$purchaseId = $_GET['id'];

try {
    $db = new DB();
    $pdo = $db->connect();

    $deleteQuery = "DELETE FROM tbl_Reciepts WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([':id' => $purchaseId]);

    header('location: Sales.php');

} catch (PDOException $e) {
    die("<p style='color: red;'>Database error: " . $e->getMessage() . "</p>");
}
?>