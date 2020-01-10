<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Category;
use App\User;

class CategoryControllerTest extends TestCase
{
    private $userCreator;
    private $userNotCreator;

    /**
     * setup categorytest with 3 categories.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userNotCreator = factory(User::class)->create();
        $user = factory(User::class)->create();
        $this->userCreator = $user;
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
        $this->assertEquals(3, count($response->getData()));
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
        $category = $response->getData()[0];

        $data = [
            'title' => 'Päivitetty category',
            'content' => 'Hieno päivitys'
        ];

        $updated = $this->actingAs($this->userCreator, 'api')->json('PUT', 'api/categories/' . $category->id, $data);
        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Category updated successfully."]);
    } /**/


    /**
     * Test updating without permission.
     *
     * @return void
     */
    public function testUpdateWithOutPermission()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);
        $category = $response->getData()[0];

        $data = [
            'title' => 'Päivitetty category',
            'content' => 'Hieno päivitys'
        ];

        $updated = $this->actingAs($this->userNotCreator, 'api')->json('PUT', 'api/categories/' . $category->id, $data);
        $updated->assertStatus(403);
        $updated->assertJson(['message' =>  "This action is unauthorized."]);
    }

        /**
     * Test deleting the user is unauthorized.
     *
     * @return void
     */
    public function testDeleteUnauthorized()
    {
        $response = $this->json('GET', '/api/categories');
        $response->assertStatus(200);

        $category = $response->getData()[0];

        $delete = $this->actingAs($this->userNotCreator, 'api')->json('DELETE', '/api/categories/' . $category->id);
        $delete->assertStatus(403);
        $delete->assertJson(['message' =>  "This action is unauthorized."]);
    }

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

        $delete = $this->actingAs($this->userCreator, 'api')->json('DELETE', '/api/categories/' . $category->id);
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Category deleted!"]);
    }

}
