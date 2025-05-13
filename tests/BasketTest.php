<?php

// Autoload or include the necessary files
require 'add.php';  // Include the add function definition
require 'DB.php';    // Include DB class definition

// Mock the database connection (or use an actual database in real tests)
class MockDB {
    public function connect() {
        return new PDO('sqlite::memory:');  // In-memory SQLite database
    }
}

session_start();

// Initialize session variable
$_SESSION['basket'] = [];

// Mock the product data
$mockDb = new MockDB();
$db = $mockDb->connect();

// Insert a sample product for the test (you should insert products before running add())
$db->exec("CREATE TABLE tbl_Productss (id INTEGER PRIMARY KEY, name TEXT, image TEXT, price DECIMAL(10, 2));");
$db->exec("INSERT INTO tbl_Productss (id, name, image, price) VALUES (1, 'Sample Product', 'sample.jpg', 9.99);");

// Test 1: Adding a new product to the basket
add($db, 1);  // Add product with id = 1

// Assert that the basket contains the added product
if (count($_SESSION['basket']) == 1 && $_SESSION['basket'][0]['id'] == 1) {
    echo "Test 1 passed: Product was added to the basket.\n";
} else {
    echo "Test 1 failed: Product was not added to the basket.\n";
}

// Test 2: Adding the same product again (should increase quantity)
add($db, 1);  // Add product with id = 1 again

// Assert that the basket contains the product and the quantity is 2
if (count($_SESSION['basket']) == 1 && $_SESSION['basket'][0]['quantity'] == 2) {
    echo "Test 2 passed: Product quantity was updated to 2.\n";
} else {
    echo "Test 2 failed: Product quantity was not updated correctly.\n";
}

// Test 3: Try adding a non-existent product (should fail)
add($db, 999);  // Non-existent product

// Assert that no product was added
if (count($_SESSION['basket']) == 1) {
    echo "Test 3 passed: No product was added for non-existent product.\n";
} else {
    echo "Test 3 failed: A non-existent product should not be added.\n";
}

?>
