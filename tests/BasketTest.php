<?php

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCanBeCreated()
    {
        $product = new Product(1, "Sample Product", "image.jpg", "A test product", 9.99);

        $this->assertEquals(1, $product->id());
        $this->assertEquals("Sample Product", $product->name());
        $this->assertEquals("image.jpg", $product->image());
        $this->assertEquals("A test product", $product->description());
        $this->assertEquals(9.99, $product->price());
    }
}