
<?php
$servername = "213.171.200.36";
$username = "kljefferson";
$password = "Password20*";
$dbname = "kljefferson";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

?>