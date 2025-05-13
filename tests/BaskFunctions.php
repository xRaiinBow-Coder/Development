<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../cmp214/basketFunctions.php';

class BasketFunctionsTest extends TestCase {
    private $dbMock;
    private $productId = 1;
    private $product = [
        'id' => 1,
        'name' => 'Test Product',
        'image' => 'test.jpg',
        'price' => 10.99,
    ];

    protected function setUp(): void {
        // Mock database connection
        $this->dbMock = $this->createMock(PDO::class);
        $this->dbMock->method('connect')->willReturn($this->dbMock);

        // Simulate an empty session
        $_SESSION['basket'] = [];
    }

    public function testAddProductToBasket() {
        // Mock the database query result
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('fetch')->willReturn($this->product);

        $this->dbMock->method('prepare')->willReturn($statementMock);
        $this->dbMock->method('execute')->willReturn(true);

        // Add product
        add($this->dbMock, $this->productId);

        // Assert that the product was added to the basket
        $this->assertCount(1, $_SESSION['basket']);
        $this->assertEquals($this->product['id'], $_SESSION['basket'][0]['id']);
    }

    public function testIncreaseProductQuantityInBasket() {
        // Add the product initially
        add($this->dbMock, $this->productId);
        
        // Add the same product again
        add($this->dbMock, $this->productId);

        // Assert that the quantity is increased
        $this->assertCount(1, $_SESSION['basket']);
        $this->assertEquals(2, $_SESSION['basket'][0]['quantity']);
    }

    public function testProductNotFound() {
        // Mock the database query to return false (product not found)
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('fetch')->willReturn(false);
        
        $this->dbMock->method('prepare')->willReturn($statementMock);

        // Capture the output
        ob_start();
        add($this->dbMock, $this->productId);
        $output = ob_get_clean();

        // Assert that the output is "Product not found!"
        $this->assertEquals("Product not found!", $output);
    }
}
