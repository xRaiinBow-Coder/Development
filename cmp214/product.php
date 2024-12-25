<?php 
class Product {
    private $id;
    private $name;
    private $image;
    private $description;
    private $price;
    private $quantity;

    // Updated constructor to handle price and description
    public function __construct($id, $name, $image, $description, $price, $quantity = 1) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->description = $description;
        $this->price = $price;  // Store price
        $this->quantity = $quantity;  // Default to 1 if not specified
    }

    public function id() {
        return $this->id;
    }

    public function name() {
        return $this->name;
    }

    public function image() {
        return $this->image;
    }

    public function description() {
        return $this->description;
    }

    public function price() {
        return $this->price;
    }

    public function quantity() {
        return $this->quantity;
    }
}