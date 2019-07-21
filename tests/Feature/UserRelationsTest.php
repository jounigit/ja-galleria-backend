<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Picture;
use App\User;
use App\Album;
use App\Category;

class UserRelationsTest extends TestCase
{
    private $user;

    /**
     * setup for users relations test.
     * User owns 2 categories, 3 albums, 15 pictures.
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->user = $user;
        factory(Category::class, 2)->create(['user_id' => $user->id]);
        // Create album with 5 pictures in each.
        factory(Album::class, 3)->create(['user_id' => $user->id])
            ->each(function ($album) use ($user) {
                $album->pictures()->attach(factory(Picture::class, 5)->create(['user_id' => $user->id]));
            });
    }

    /**
     * Test relationships between user and categories.
     * @return void
     */
    public function testUserOwnsCategories()
    {
        // Check if user owns categories..
        $this->assertEquals($this->user->categories->count(), Category::all()->count());
        $this->assertNotEquals($this->user->categories->count(), 3);
    }

     /**
     * Test relationships between user and categories.
     * @return void
     */
    public function testUserOwnsAlbums()
    {
        // Check if user owns albums..
        $this->assertEquals($this->user->albums->count(), Album::all()->count());
        $this->assertNotEquals($this->user->albums->count(), 2);
    }

    /**
     * Test relationships between user and pictures.
     * @return void
     */
    public function testUserOwnsPictures()
    {
        // Check if user owns pictures..
        $this->assertEquals($this->user->pictures->count(), Picture::all()->count());
        $this->assertEquals($this->user->pictures->count(), 15);
    }

    /**
     * Test soft deleting the user and all related instances.
     *
     * @return void
     */
    public function testSoftDeletingUserAndRelations()
    {
        // Softdelete user.
        $this->user->delete();
        // Get Softdeleted album.
        $userTrashed = User::withTrashed()->first();
        $this->assertDatabaseHas('users', $userTrashed->toArray());
        $this->assertSoftDeleted('users', $userTrashed->toArray());
        // Check there is still 2 categories, 3 albums, 15 pictures..
        $this->assertEquals(2, $userTrashed->categories->count());
        $this->assertEquals(3, $userTrashed->albums->count());
        $this->assertEquals(15, $userTrashed->pictures->count());
    }

    /**
     * Test permanently deleting the user and all related instances.
     *
     * @return void
     */
    public function testPermanentlyDeletingUserAndRelations()
    {
        // Forcedelete user.
        $this->user->forceDelete();
        // Get Softdeleted album.
        $user = User::withTrashed()->first();
        $this->assertDatabaseMissing('users', $this->user->toArray());
        $this->assertNotEquals(2, Category::all()->count());
        $this->assertNotEquals(3, Album::all()->count());
        $this->assertEquals(0, Picture::all()->count());
    }

}
