<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../cmp214/Product.php';

class ProductTest extends TestCase
{
    public function testProductProperties()
    {
        $product = new Product(1, 'Apple', 'apple.jpg', 'Fresh apple', 1.99);
        $this->assertEquals(1, $product->id());
        $this->assertEquals('Apple', $product->name());
        $this->assertEquals('apple.jpg', $product->image());
        $this->assertEquals('Fresh apple', $product->description());
        $this->assertEquals(1.99, $product->price());
    }
}
