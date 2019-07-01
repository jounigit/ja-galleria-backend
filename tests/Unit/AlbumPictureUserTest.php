<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Picture;
use App\User;
use App\Album;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlbumPictureUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test relationships between user, albums and pictures.
     * User needs to be created because of the foreign key dependence.
     *
     * @return void
     */
    public function testAlbumPictureRelations()
    {
        $user = factory(User::class)->create();
        // Create album with 5 pictures in each.
        factory(Album::class)->create(['user_id' => $user->id])
            ->each(function ($album) use ($user) {
                $album->pictures()->attach(factory(Picture::class, 5)->create(['user_id' => $user->id]));
            });


        $albums = Album::all();
        $pictures = Picture::all();

        // Check there is 1 album..
        $this->assertEquals(1, $albums->count());
        // Check there is 5 pictures..
        $this->assertEquals(5, $pictures->count());
        // Check if user owns 5 pictures..
        $this->assertEquals($user->pictures->count(), 5);
        // Check if user owns 1 album..
        $this->assertEquals($user->albums->count(), 1);
        $this->assertEquals($user->id, $pictures->first()->user_id);
        //Check if relationship returns collection..
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Collection', $user->pictures);
    }
}
