<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }
    /**
     * Test User has many albums.
     *
     * @return void
     */
    public function testUserHasManyAlbums()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->albums);
    }

    /**
     * Test User has many categories.
     *
     * @return void
     */
    public function testUserHasManyCategories()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->categories);
    }

    /**
     * Test User has many pictures.
     *
     * @return void
     */
    public function testUserHasManyPictures()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->pictures);
    }
}
