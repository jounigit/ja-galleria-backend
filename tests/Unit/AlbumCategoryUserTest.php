<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Category;
use App\User;
use App\Album;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlbumCategoryUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test relationships between users, albums and categories.
     * User needs to be created because of the foreign key dependence.
     *
     * @return void
     */
    public function testAlbumCategoryUserRelations()
    {
        $user = factory(User::class)->create();
        // Create 2 categories with 3 albums in each.
        factory(Category::class, 2)->create(['user_id' => $user->id])
            ->each(function ($category) use ($user) {
                $category->albums()->saveMany(factory(Album::class, 3)->make(['user_id' => $user->id]));
            });

        $categories = Category::all();
        $albums = Album::all();

        // Check there is 2 categories..
        $this->assertEquals(2, $categories->count());
        // Check there is 6 albums..
        $this->assertEquals(6, $albums->count());
        // Check if user owns 2 categories..
        $this->assertEquals($user->categories->count(), 2);
        // Check if user owns 6 albums..
        $this->assertEquals($user->albums->count(), 6);
        $this->assertEquals($user->id, $categories->first()->user_id);
        //Check if relationship returns collection.
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $user->categories);
    }
}
