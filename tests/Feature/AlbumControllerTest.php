<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Album;
use App\User;

class AlbumControllerTest extends TestCase
{
    private $userCreator;
    private $userNotCreator;

    /**
     * setup albumtest with 3 albums.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userNotCreator = factory(User::class)->create();
        $user = factory(User::class)->create();
        $this->userCreator = $user;
        factory(Album::class, 3)->create([
            'user_id' => $user->id
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

        $this->assertEquals(3, count($response->getData()->data));
        $response->assertJsonStructure(
            [
                'data' => [
                    [
                        'id',
                        'user_id',
                        'category_id',
                        'title',
                        'slug',
                        'content',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ]
                ],
                'meta' => [
                    'album_count'
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
        $album = $response->getData()->data[0];

        $showAlbum = $this->json('GET', 'api/albums/' . $album->id);

        $showAlbum->assertStatus(200);
        $showAlbum->assertJson([
            'data' => [
                'id' => $album->id,
                'title' => $album->title,
                'content' => $album->content
            ]
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
        $user = factory(User::class)->create();

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
        $album = $response->getData()->data[0];

        $data = [
            'title' => 'Päivitetty albumi',
            'content' => 'Hieno päivitys'
        ];

        $updated = $this->actingAs($this->userCreator, 'api')->json('PUT', 'api/albums/' . $album->id, $data);
        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Album updated successfully."]);
    } /**/

    /**
     * Test updating album.
     *
     * @return void
     */
    public function testUpdateWithOutPermission()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);
        $album = $response->getData()->data[0];

        $data = [
            'title' => 'Päivitetty albumi',
            'content' => 'Hieno päivitys'
        ];

        $updated = $this->actingAs($this->userNotCreator, 'api')->json('PUT', 'api/albums/' . $album->id, $data);
        $updated->assertStatus(403);
        $updated->assertJson(['message' => "This action is unauthorized."]);
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

        $album = $response->getData()->data[0];

        $delete = $this->actingAs($this->userCreator, 'api')->json('DELETE', '/api/albums/' . $album->id);
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Album deleted!"]);
    }

        /**
     * Test deleting the Album.
     *
     * @return void
     */
    public function testDeleteWithOutPermission()
    {
        $response = $this->json('GET', '/api/albums');
        $response->assertStatus(200);

        $album = $response->getData()->data[0];

        $userNotCreater = factory(User::class)->create();
        $delete = $this->actingAs($userNotCreater, 'api')->json('DELETE', '/api/albums/' . $album->id);
        $delete->assertStatus(403);
        $delete->assertJson(['message' => "This action is unauthorized."]);
    }
}
