<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);

    session_set_cookie_params([
        'lifetime' => 1800, 
        'domain' => 'sencldigitech.co.uk/kljefferson',  
        'path' => '/',
        'secure' => true,  
        'samesite' => 'Strict',  
        'httponly' => true,  
    ]);
    session_start();
}


$lifetime = 1800; 
if (isset($_SESSION['createdAt'])) {
    if (time() - $_SESSION['createdAt'] > $lifetime) {
        session_regenerate_id(true);  
        $_SESSION['createdAt'] = time();  
    }
} else {
    $_SESSION['createdAt'] = time();  
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generate a new CSRF token
}

?>
