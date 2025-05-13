<?php
// Start session to match real behavior
session_start();

// Manually set a CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generate CSRF token

// Simulate the POST request with a product ID and CSRF token
$_POST['id'] = 1;  // Replace with actual product ID
$_POST['csrf_token'] = $_SESSION['csrf_token'];  // Send CSRF token

// Ensure that the file path is correct relative to where the script is being executed
$script_path = __DIR__ . '/ItemInfo.php';  // Absolute path to ItemInfo.php

// Include the ItemInfo.php script (use the absolute path)
if (file_exists($script_path)) {
    include $script_path;
} else {
    die("ItemInfo.php not found at path: $script_path");
}

// Capture the output and check for the presence of the product description
$output = ob_get_clean();

if (strpos($output, 'Product Description') !== false) {
    echo "Test passed: Product description found.\n";
} else {
    echo "Test failed: Product description not found.\n";
    exit(1);
}
