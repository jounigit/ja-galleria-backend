<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Category;
use App\User;

class CategoryControllerTest extends TestCase
{
    /**
     * setup categorytest with 3 categories.
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        factory(Category::class, 3)->create([
            'user_id' => $user->id
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
        $this->assertEquals(3, count($response->getData()->data));
        $response->assertJsonStructure(
            [
                'data' => [
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
        $category = $response->getData()->data[0];

        $showcategories = $this->json('GET', 'api/categories/' . $category->id);

        $showcategories->assertStatus(200);
        $showcategories->assertJson([
            'data' => [
                'id' => $category->id,
                'title' => $category->title,
                'content' => $category->content
            ]
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
        $user = factory(User::class)->create();

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
        $category = $response->getData()->data[0];

        $data = [
            'title' => 'Päivitetty category',
            'content' => 'Hieno päivitys'
        ];

        $user = factory(User::class)->create();
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

        $category = $response->getData()->data[0];

        $user = factory(User::class)->create();
        $delete = $this->actingAs($user, 'api')->json('DELETE', '/api/categories/' . $category->id);
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Category deleted!"]);
    }

}
