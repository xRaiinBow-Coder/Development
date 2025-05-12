<?php

require 'DB.php';

function adminLogin($emailOrUsername, $password) {
    $db = new DB();
    $conn = $db->connect();

    if (!$conn) {
        die("Failed to connect to the database.");
    }

    // Query to fetch user by email or username
    $query = $conn->prepare('SELECT * FROM tbl_users WHERE email = :emailOrUsername OR username = :emailOrUsername');
    $query->bindParam(':emailOrUsername', $emailOrUsername, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() === 1) {
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Check if the role is admin
            if ($user['role'] === 'admin') {
                return 'Admin login successful.';
            } else {
                return 'User is not an admin.';
            }
        } else {
            return 'Invalid password.';
        }
    } else {
        return 'User not found.';
    }
}