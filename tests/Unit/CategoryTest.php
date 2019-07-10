<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Category;

class CategoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create([
            'user_id' => factory(User::class)->create()->id,
        ]);
    }

    /**
     * Test Category belongs to a User.
     *
     * @return void
     */
    public function testCategoryBelongtoUser()
    {
        $this->assertInstanceOf(User::class, $this->category->user);
    }

    /**
     * Test Category has many albums.
     */
    public function testCategoryHasManyAlbums()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->category->albums);
    }
}
