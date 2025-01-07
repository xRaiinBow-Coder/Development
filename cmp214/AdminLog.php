<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

require 'DB.php';

function addAdminAccount() {
    $db = new DB();
    $conn = $db->connect();

    if (!$conn) {
        die("Failed to connect to the database.");
    }

    $adminEmail = "KJADMIN@egmail.com"; 
    $adminUsername = "KJADMIN"; 
    $adminPassword = "KJADMIN"; 
    $adminRole = "admin"; 
    $uuid = time() . bin2hex(random_bytes(8)); 

    
    $query = $conn->prepare('SELECT * FROM tbl_users WHERE email = :email OR username = :username');
    $query->bindParam(':email', $adminEmail, PDO::PARAM_STR);
    $query->bindParam(':username', $adminUsername, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo "Admin account already exists.";
    } else {
      
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

      
        $insertQuery = $conn->prepare('INSERT INTO tbl_users (email, username, password, uuid, role) VALUES (:email, :username, :password, :uuid, :role)');
        $insertQuery->bindParam(':email', $adminEmail, PDO::PARAM_STR);
        $insertQuery->bindParam(':username', $adminUsername, PDO::PARAM_STR);
        $insertQuery->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $insertQuery->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $insertQuery->bindParam(':role', $adminRole, PDO::PARAM_STR);

        if ($insertQuery->execute()) {
            echo "Admin account created successfully.";
        } else {
            echo "Failed to create admin account.";
        }
    }
}


?>