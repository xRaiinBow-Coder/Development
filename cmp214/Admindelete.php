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

    echo "<p>Deleted successfully.</p>";
    echo "<p><a href='Sales.php'>Back to the purchase history!</a></p>";

} catch (PDOException $e) {
    die("<p style='color: red;'>Database error: " . $e->getMessage() . "</p>");
}
?>