<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * setup categorytest with 3 categoriess.
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(Category::class, 3)->create([
            'user_id' => 1
        ]);
    }

    /**
     * Test getting all categoriess.
     *
     * @return void
     */
    public function testGettingAllCategories()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure(
            [
                [
                    'id',
                    'user_id',
                    'title',
                    'slug',
                    'content',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ]
            ]
        );
    }

    /**
     * Test getting one categories.
     *
     * @return void
     */
    public function testGettingCategory()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);
        $category = $response->getData()[0];

        $showcategories = $this->json('GET', 'api/categories/' . $category->id);

        $showcategories->assertStatus(200);
        $showcategories->assertJson([
            'id' => $category->id
        ]);
    }

        /**
     * Test creating category.
     *
     * @return void
     */
    public function testCreateCategory()
    {
        $data = [
            'title' => 'Uusi categoriesi',
            'content' => 'Hieno sisältö'
        ];
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/categories', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Category stored successfully."]);
        $response->assertJson(['data' => $data]);
    }

    /**
     * Test updating category.
     *
     * @return void
     */
    public function testUpdateCategory()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);
        $category = $response->getData()[0];

        $data = [
            'user_id' => 1,
            'title' => 'Päivitetty category',
            'content' => 'Hieno päivitys'
        ];

        $user = factory(\App\User::class)->create();
        $updated = $this->actingAs($user, 'api')->json('PUT', 'api/categories/' . $category->id, $data);
        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Category updated successfully."]);
    } /**/

       /**
     * Test deleting the category.
     *
     * @return void
     */
    public function testDeleteCategory()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);

        $category = $response->getData()[0];

        $user = factory(\App\User::class)->create();
        $delete = $this->actingAs($user, 'api')->json('DELETE', '/api/categories/' . $category->id);
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Category deleted!"]);
    }

}
