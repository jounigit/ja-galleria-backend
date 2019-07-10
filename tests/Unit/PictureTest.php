<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Picture;

class PictureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->picture = factory(Picture::class)->create(['user_id' => $this->user->id]);
    }

    /**
     * Test Picture belongs to a user.
     *
     * @return void
     */
    public function testPictureBelongtoUser()
    {
        $this->assertInstanceOf(User::class, $this->picture->user);
    }

     /**
     * Test Picture belongs to many albums.
     */
    public function testPictureBelongsToManyAlbums()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->picture->albums);
    }
}
