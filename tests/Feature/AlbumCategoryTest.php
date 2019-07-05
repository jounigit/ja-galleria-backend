<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Category;
use App\User;
use App\Album;

class AlbumCategoryTest extends TestCase
{
    /**
     * setup for test 2 categories with 3 albums.
     * User needs to be created because of the foreign key dependence.
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        // Create 2 categories with 3 albums in each.
        factory(Category::class, 2)->create(['user_id' => $user->id])
            ->each(function ($category) use ($user) {
                $category->albums()->saveMany(factory(Album::class, 3)->make(['user_id' => $user->id]));
            });
    }

    /**
     * Test albums, categories.
     * @return void
     */
    public function testAlbumsAndCategoriesExists()
    {
        // Check there is 2 categories..
        $this->assertEquals(2, Category::all()->count());
        // Check there is 6 albums..
        $this->assertEquals(6, Album::all()->count());
    }

    /**
     * Test relationships between albums and categories.
     * @return void
     */
    public function testAlbumCategoryRelations()
    {
        $category = Category::all()->first();
        $albums = Album::all();

        $this->assertEquals($albums->first()->category_id, $category->id);
        $this->assertNotEquals($albums->first()->category_id, 2);
    }

    /**
     *
     * @return void
     */
    public function testCategoryDeletes()
    {

        $categories = Category::all();
        $category = Category::all()->first();

        // Softdelete category.
        $category->delete();
        // Get Softdeleted album.
        $categoryTrashed = Category::withTrashed()->first();
        $this->assertDatabaseHas('categories', $categoryTrashed->toArray());
        $this->assertSoftDeleted('categories', $categoryTrashed->toArray());

    }
}
