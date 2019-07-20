<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Picture;
use App\User;
use File;

class PictureControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * setup picturetest with 5 pictures.
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        factory(Picture::class, 5)->create([
            'user_id' => $user->id
        ]);
    }

    /**
     * Test getting all pictures.
     *
     * @return void
     */
    public function testGettingAllPictures()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);
        $this->assertEquals(5, count($response->getData()->data));
        $response->assertJsonStructure(
            [
                'data' => [
                    [
                        'id',
                        'user_id',
                        'title',
                        'slug',
                        'content',
                        'image',
                        'thumb',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ]
                ],
                'meta' => [
                    'picture_count'
                ]
            ]
        );
    }

    /**
     * Test getting one picture.
     *
     * @return void
     */
    public function testGettingPicture()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);
        $picture = $response->getData()->data[0];

        $showPicture = $this->json('GET', 'api/pictures/' . $picture->id);

        $showPicture->assertStatus(200);
        $showPicture->assertJson([
            'data' => [
                'id' => $picture->id,
                'title' => $picture->title,
                'content' => $picture->content,
                'image' => $picture->image
            ]
        ]);
    }

    /**
     * Test creating picture.
     *
     * @return void
     */
    public function testCreatePicture()
    {
        $data = [
            'title' => 'Uusi kuva',
            'image' => UploadedFile::fake()->image('random.jpg')
        ];
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/pictures', $data);
        // dd('ID:: ' . DB::table('users')->latest('id')->first());
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Picture stored successfully."]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'slug',
                'content',
                'image',
                'thumb',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    /**
     * Test updating picture.
     *
     * @return void
     */
    public function testUpdatePicture()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);
        $picture = $response->getData()->data[0];

        $data = [
            'title' => 'Päivitetty kuva',
            'content' => 'Hieno päivitys',
            'image' => UploadedFile::fake()->image('random.jpg')
        ];

        $user = factory(\App\User::class)->create();
        $updated = $this->actingAs($user, 'api')->json('PUT', 'api/pictures/' . $picture->id, $data);

        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Picture updated successfully."]);
    }

    /**
     * Test deleting the picture.
     *
     * @return void
     */
    public function testDeletePicture()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);

        $picture = $response->getData()->data[0];

        $user = factory(\App\User::class)->create();
        $delete = $this->actingAs($user, 'api')->json('DELETE', '/api/pictures/' . $picture->id);

        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Picture deleted!"]);
    }
}
