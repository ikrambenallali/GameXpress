<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
    public function test_product_has_name()
    {
        $product = new Product(['name' => 'wita']);
        $this->assertEquals('wita', $product->name);
    }
    public function test_product_has_slug()
    {
        $product = new Product(['slug' => 'wita']);
        $this->assertEquals('wita', $product->slug);
    }
    public function test_product_has_price()
    {
        $product = new Product(['price' => 100]);
        $this->assertEquals(100, $product->price);
    }
    public function test_product_has_stock()
    {
        $product = new Product(['stock' => 20]);
        $this->assertEquals(20, $product->stock);
    }
    public function test_product_has_status()
    {
        $product = new Product(['status' => 'disponible']);
        $this->assertEquals('disponible', $product->status);
    }
    public function test_product_has_category_id()
    {
        $product = new Product(['category_id' => 1]);
        $this->assertEquals(1, $product->category_id);
    }
}
