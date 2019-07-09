<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Album;
use App\User;
use App\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    /**
     * Test Category belongs to a User.
     *
     * @return void
     */
    public function testCategoryBelongtoUser()
    {
        $user = factory(User::class)->create();
        $category = factory(Category::class)->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $category->user);
    }

    public function testCategoryHasManyAlbums()
    {
        factory(Category::class)->create();

        $category = Category::all()->last();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->albums);
    }
}
