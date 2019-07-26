<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Picture;
use App\User;
use App\Album;
use App\Category;

class AdminTest extends TestCase
{
    private $admin;
    private $notAdmin;
    private $user;
    private $album;
    private $category;
    private $picture;

    /**
     * setup
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = factory(User::class)->create(['is_admin' => 1]);
        $this->notAdmin = factory(User::class)->create();
        $user = factory(User::class)->create();
        $this->user = $user;
        $this->album = factory(Album::class)->create(['user_id' => $user->id]);
        $this->category = factory(Category::class)->create(['user_id' => $user->id]);
        $this->picture = factory(Picture::class)->create(['user_id' => $user->id]);
    }

    /**
     * test admin can update.
     *
     * @return void
     */
    public function testAdminCanUpdateAlbum()
    {
        $data = [
            'title' => 'Päivitetty albumi'
        ];

        $response = $this->actingAs($this->admin, 'api')->json('PUT', 'api/albums/' . $this->album->id, $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Album updated successfully."]);
    }

    /**
     * test admin can delete.
     *
     * @return void
     */
    public function testAdminCanDeleteAlbum()
    {
        $response = $this->actingAs($this->admin, 'api')->json('DELETE', 'api/albums/' . $this->album->id);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Album deleted!"]);
    }

    /**
     * test only creator or admin can delete.
     *
     * @return void
     */
    public function testCanNotDeleteAlbum()
    {
        $response = $this->actingAs($this->notAdmin, 'api')->json('DELETE', 'api/albums/' . $this->album->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => "This action is unauthorized."]);
    }

    /**
     * test admin can update.
     *
     * @return void
     */
    public function testAdminCanUpdateCategory()
    {
        $data = [
            'title' => 'Päivitetty'
        ];

        $response = $this->actingAs($this->admin, 'api')->json('PUT', 'api/categories/' . $this->category->id, $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Category updated successfully."]);
    }

    /**
     * test admin can delete.
     *
     * @return void
     */
    public function testAdminCanDeleteCategory()
    {
        $response = $this->actingAs($this->admin, 'api')->json('DELETE', 'api/categories/' . $this->category->id);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Category deleted!"]);
    }

    /**
     * test only creator or admin can update.
     *
     * @return void
     */
    public function testCanNotUpdateCategory()
    {
        $data = [
            'title' => 'Päivitetty'
        ];

        $response = $this->actingAs($this->admin, 'api')->json('PUT', 'api/categories/' . $this->category->id, $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Category updated successfully."]);
    }

    /**
     * test admin can update.
     *
     * @return void
     */
    public function testAdminCanUpdatePicture()
    {
        $data = [
            'title' => 'Päivitetty'
        ];

        $response = $this->actingAs($this->admin, 'api')->json('PUT', 'api/pictures/' . $this->picture->id, $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Picture updated successfully."]);
    }

    /**
     * test admin can delete.
     *
     * @return void
     */
    public function testAdminCanDeletePicture()
    {
        $response = $this->actingAs($this->admin, 'api')->json('DELETE', 'api/pictures/' . $this->picture->id);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Picture deleted!"]);
    }

    /**
     * test only creator or admin can delete.
     *
     * @return void
     */
    public function testCanNotDeletePicture()
    {
        $response = $this->actingAs($this->notAdmin, 'api')->json('DELETE', 'api/pictures/' . $this->picture->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => "This action is unauthorized."]);
    }

    /**
     * test admin can update.
     *
     * @return void
     */
    public function testAdminCanUpdateUser()
    {
        $data = [
            'name' => 'Herra Huu'
        ];

        $response = $this->actingAs($this->admin, 'api')->json('PUT', 'api/users/' . $this->user->id, $data);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => "User updated!"]);
    }

     /**
     * test only creator or admin can update.
     *
     * @return void
     */
    public function testCanNotUpdateUser()
    {
        $data = [
            'name' => 'Rouva Huu'
        ];

        $response = $this->actingAs($this->notAdmin, 'api')->json('PUT', 'api/users/' . $this->user->id, $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => "This action is unauthorized."]);
    }

    /**
     * test admin can delete.
     *
     * @return void
     */
    public function testAdminCanDeleteUser()
    {
        $response = $this->actingAs($this->admin, 'api')->json('DELETE', 'api/users/' . $this->user->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => "User Deleted!"]);
    }
}
