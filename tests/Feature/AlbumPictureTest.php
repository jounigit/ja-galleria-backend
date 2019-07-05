<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Picture;
use App\User;
use App\Album;

class AlbumPictureTest extends TestCase
{
    /**
     * setup for test 1 album with 5 pictures.
     * User needs to be created because of the foreign key dependence.
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        // Create album with 5 pictures in each.
        factory(Album::class)->create(['user_id' => $user->id])
            ->each(function ($album) use ($user) {
                $album->pictures()->attach(factory(Picture::class, 5)->create(['user_id' => $user->id]));
            });
    }

    /**
     * Test albums, pictures.
     * @return void
     */
    public function testAlbumsAndPicturesExists()
    {
        // Check there is 1 album..
        $this->assertEquals(1, Album::all()->count());
        // Check there is 5 pictures..
        $this->assertEquals(5, Picture::all()->count());
    }

    /**
     * Test relationships betweenalbums and pictures.
     * @return void
     */
    public function testAlbumPictureRelations()
    {
        $album = Album::all()->first();
        // Check if album owns 5 pictures..
        $this->assertEquals($album->pictures->count(), Picture::all()->count());
    }

    /**
     * Test album deletes.
     *
     * @return void
     */
    public function testAlbumDeletes()
    {
        $album = Album::all()->first();

        // Softdelete album.
        $album->delete();
        // Get Softdeleted album.
        $albumTrashed = Album::withTrashed()->first();
        $this->assertDatabaseHas('albums', $albumTrashed->toArray());
        $this->assertSoftDeleted('albums', $albumTrashed->toArray());
        // Check there is still 5 pictures in pivot table..
        $this->assertEquals(5, $albumTrashed->pictures->count());
        // Delete album permanently.
        $albumTrashed->forceDelete();
        $albumTrashed = Album::withTrashed()->first();
        $this->assertEquals(null, $albumTrashed);

        // Check there is still 5 pictures..
        $pictures = Picture::all();
        $this->assertEquals(5, $pictures->count());
    }

}
