<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Storage;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(RolesAndPermissionsSeeder::class);

        // Create user and assign appropriate role
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Sanctum::actingAs($user, ['*']);
    }
    public function test_getting_all_categories()
    {
        // Create some categories
        Category::factory()->count(3)->create();
        $response = $this->getJson(route('category.index'));
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'categories' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
    public function test_can_store_a_category()
    {
        Storage::fake('public');

        $data = [
            'name' => 'Test Category',
        ];

        $response = $this->postJson(route('category.store'), $data);
        // dd($response->json());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'category' => [
                    'id',
                    'name',
                    'slug',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Verify category is in the database
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        // Get the created category
        $category = Category::where('name', 'Test Category')->first();

        // Verify icon exists in storage
        Storage::disk('public')->assertExists($category->icon_path);
    }
    public function test_can_update_a_category()
    {
        Storage::fake('public');

        // Create a category
        $category = Category::create([
            'name' => 'Original Category',
            'slug' => 'original-category'
        ]);

        $data = [
            'name' => 'Updated Category',
            'slug' => 'updated-category'

        ];

        $response = $this->putJson(route('category.update', $category->id), $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'categories' => [
                    'id',
                    'name',
                    'slug',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'categories' => [
                    'name' => 'Updated Category',
                    'slug' => 'updated-category'
                ]
            ]);

        // Verify category is updated in the database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category'
        ]);

        // Refresh the model
        $category->refresh();

    }
    public function test_can_delete_a_category()
    {
        Storage::fake('public');


        $category = Category::create([
            'name' => 'Delete Test Category',
            'slug' => 'delete-test-category',
        ]);

        $response = $this->deleteJson(route('category.destroy', $category->id));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'category deleted successfully'
            ]);

        // Verify category is deleted from the database
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Verify icon is deleted from storage
    }
}
