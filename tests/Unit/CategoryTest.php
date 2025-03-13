<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
    public function test_category_has_name()
    {
        $category = new Category(['name' => 'wita']);
        $this->assertEquals('wita', $category->name);
    }
    public function test_category_has_slug()
    {
        $category = new Category(['slug' => 'wita']);
        $this->assertEquals('wita', $category->slug);
    }
}
