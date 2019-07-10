<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Album;
use App\User;
use App\Category;

class AlbumTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->album = factory(Album::class)->create([
            'user_id' => factory(User::class)->create()->id,
            'category_id' => factory(Category::class)->create()->id
        ]);
    }

    /**
     * Test Album belongs to a user.
     *
     * @return void
     */
    public function testAlbumBelongtoUser()
    {
        $this->assertInstanceOf(User::class, $this->album->user);
    }

    /**
     * Test Album belongs to a category.
     *
     * @return void
     */
    public function testAlbumBelongtoCategory()
    {
        $this->assertInstanceOf(Category::class, $this->album->category);
    }

     /**
     * Test Album belongs to many pictures.
     */
    public function testAlbumBelongsToManyPicture()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->album->pictures);
    }
}
