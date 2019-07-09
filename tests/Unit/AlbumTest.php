<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Album;
use App\User;
use App\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlbumTest extends TestCase
{
    /**
     * Test Album belongs to a user.
     *
     * @return void
     */
    public function testAlbumBelongtoUser()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $album->user);
    }

     /**
     * Test Album belongs to a category.
     *
     * @return void
     */
    public function testAlbumBelongtoCategory()
    {
        $category = factory(Category::class)->create();
        $album = factory(Album::class)->create(['category_id' => $category->id]);
        $this->assertInstanceOf(Category::class, $album->category);
    }
}
