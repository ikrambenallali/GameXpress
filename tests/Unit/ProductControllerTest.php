<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Storage;


class ProductControllerTest extends TestCase
{


    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $user->assignRole('product_manager');

        Sanctum::actingAs($user, ['*']);
    }

    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_getting_all_products()
    {
        $response = $this->getJson(route('product.index'));
        // dd($response->json());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'produits' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'stock',
                        'category_id',
                        'status',
                        'images' => [
                            '*' => [
                                'id',
                                'image_url',
                                'is_primary',
                                'product_id',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
    public function test_can_store_a_product()
    {
        Storage::fake('public');
        // $category = Category::factory()->create();

        $data = [
            'name' => 'product test016',
            'price' => 299.99,
            'stock' => 20,
            'category_id' => 1,
            'primary_image' => \Illuminate\Http\UploadedFile::fake()->image('primary.jpg'),
            'images' => [
                \Illuminate\Http\UploadedFile::fake()->image('image1.jpg'),
                \Illuminate\Http\UploadedFile::fake()->image('image2.jpg'),
            ],
        ];

        $response = $this->postJson(route('products.store'), $data);
        // dd($response->json()); 

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'product' => [
                    'id',
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'category_id',
                    'status',
                    'images' => [
                        '*' => [
                            'id',
                            'product_id',
                            'image_url',
                            'is_primary',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('produits', [
            'name' => 'product test016',
            'slug' => 'product-test016',
            'price' => 299.99,
            'stock' => 20,
            'category_id' => 1,
        ]);

        $product = Product::where('name', 'product test016')->first();

        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage, 'Primary image not found');
        Storage::disk('public')->assertExists($primaryImage->image_url);

        $otherImages = $product->images()->where('is_primary', false)->get();
        $this->assertCount(2, $otherImages, 'Expected 2 non-primary images');

        foreach ($otherImages as $image) {
            Storage::disk('public')->assertExists($image->image_url);
        }
    }
    public function test_updating_a_product()
    {
        Storage::fake('public');
        // $category = Category::factory()->create();
        $newCategory = Category::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Original Product',
            'category_id' => 1,
            'stock' => 5
        ]);

        // Create a primary image
        $product->images()->create([
            'image_url' => 'test/primary.jpg',
            'is_primary' => true
        ]);

        // Create additional images
        $product->images()->create([
            'image_url' => 'test/image1.jpg',
            'is_primary' => false
        ]);

        $updateData = [
            'name' => 'Updated Product011',
            'price' => 199.99,
            'stock' => 20,
            'category_id' => $newCategory->id,
            'primary_image' => \Illuminate\Http\UploadedFile::fake()->image('new_primary.jpg'),
            'images' => [
                \Illuminate\Http\UploadedFile::fake()->image('new_image1.jpg'),
                \Illuminate\Http\UploadedFile::fake()->image('new_image2.jpg'),
                \Illuminate\Http\UploadedFile::fake()->image('new_image3.jpg'),
            ],
        ];

        $response = $this->putJson(route('products.update', $product->id), $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'produits' => [
                    'id',
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'category_id',
                    'status',
                    'images' => [
                        '*' => [
                            'id',
                            'image_url',
                            'is_primary',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Check product was updated in database
        $this->assertDatabaseHas('produits', [
            'id' => $product->id,
            'name' => 'Updated Product011',
            'slug' => 'updated-product011',
            'price' => 199.99,
            'stock' => 20,
            'category_id' => $newCategory->id,
            'status' => 'disponible'
        ]);

        // Refresh product from database
        $product->refresh();

        // Verify we have one primary image
        $primaryImage = $product->images()->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
        Storage::disk('public')->assertExists($primaryImage->image_url);

        // Verify we have 3 non-primary images
        $otherImages = $product->images()->where('is_primary', false)->get();
        $this->assertCount(3, $otherImages);

        foreach ($otherImages as $image) {
            Storage::disk('public')->assertExists($image->image_url);
        }
    }
    public function test_deleting_a_product()
    {
        Storage::fake('public');
        // $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => 1
        ]);

        // Create a primary image with real path in fake storage
        $primaryImagePath = 'products/primary-test.jpg';
        Storage::disk('public')->put($primaryImagePath, 'test contents');

        $product->images()->create([
            'image_url' => $primaryImagePath,
            'is_primary' => true
        ]);

        // Create additional image with real path in fake storage
        $imagePath = 'products/test-image.jpg';
        Storage::disk('public')->put($imagePath, 'test contents');

        $product->images()->create([
            'image_url' => $imagePath,
            'is_primary' => false
        ]);

        $response = $this->deleteJson(route('products.destroy', $product->id));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'product supprimer avec succès'
            ]);

        // Check product was deleted from database
        $this->assertSoftDeleted('produits', [
            'id' => $product->id
        ]);
        // Check associated images were deleted
        // $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);

        // // Check files were deleted from storage
        // Storage::disk('public')->assertMissing($primaryImagePath);
        // Storage::disk('public')->assertMissing($imagePath);
    }
}
