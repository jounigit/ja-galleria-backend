<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Album;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlbumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * setup albumtest with 3 albums.
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(Album::class, 3)->create([
            'user_id' => 1,
            'category_id' => 2
        ]);
    }

    /**
     * Test getting all albums.
     *
     * @return void
     */
    public function testGettingAllAlbums()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure(
            [
                [
                    'id',
                    'user_id',
                    'category_id',
                    'title',
                    'slug',
                    'content',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ]
            ]
        );
    }

    /**
     * Test getting one Album.
     *
     * @return void
     */
    public function testGettingAlbum()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);
        $album = $response->getData()[0];

        $showAlbum = $this->json('GET', 'api/albums/' . $album->id);

        $showAlbum->assertStatus(200);
        $showAlbum->assertJson([
            'id' => $album->id
        ]);
    }

    /**
     * Test creating Album.
     *
     * @return void
     */
    public function testCreateAlbum()
    {
        $data = [
            'title' => 'Uusi albumi',
            'content' => 'Hieno sisältö'
        ];
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/albums', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Album stored successfully."]);
        $response->assertJson(['data' => $data]);
    }

    /**
     * Test updating album.
     *
     * @return void
     */
    public function testUpdateAlbum()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);
        $album = $response->getData()[0];

        $data = [
            'user_id' => 1,
            'category_id' => 1,
            'title' => 'Päivitetty albumi',
            'content' => 'Hieno päivitys'
        ];

        $user = factory(\App\User::class)->create();
        $updated = $this->actingAs($user, 'api')->json('PUT', 'api/albums/' . $album->id, $data);
        // dd($updated);
        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Album updated successfully."]);
    } /**/

    /**
     * Test deleting the Album.
     *
     * @return void
     */
    public function testDeleteAlbum()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);

        $album = $response->getData()[0];

        $user = factory(\App\User::class)->create();
        $delete = $this->actingAs($user, 'api')->json('DELETE', '/api/albums/' . $album->id);
        // $delete->dump();
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Album deleted!"]);
    }

    /** album has relations */
    public function albumHasAllRelations(){
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);

        $album = $response->getData()[0];

        $this->assertInstanceOf('App\User', $album->user);
        $this->assertInstanceOf('App\Category', $album->category);
        $this->assertInstanceOf('App\Picture', $album->pictures);
    }



}
