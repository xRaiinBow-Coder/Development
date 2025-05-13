<?php
// Simulate a session start to set the CSRF token (the same way it would happen in the real script).
session_start();

// Manually set a CSRF token, as the real script would generate it.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // This will generate a new CSRF token.

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

// Simulate the product ID and POST request data.
$_POST['id'] = 1;  // Replace this with an existing product ID from your database for a real test.
$_POST['csrf_token'] = $_SESSION['csrf_token'];  // Send the correct CSRF token.

// Include the script to be tested.
include 'ItemInfo.php';

// Capture the output and check for product description (can adjust based on actual HTML).
$output = ob_get_clean();
if (strpos($output, 'Product Description') !== false) {
    echo "Test passed: Product description found.\n";
} else {
    echo "Test failed: Product description not found.\n";
    exit(1);  // Fail the test if the description isn't found.
}
