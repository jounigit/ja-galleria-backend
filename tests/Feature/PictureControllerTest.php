<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        factory(User::class, 1000)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/pictures', $data);

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
        // delete user's folder and all subfolders.
        $delete_dir = File::deleteDirectory(public_path($user->id));
        // assert directory deleting is true
        $this->assertTrue($delete_dir);
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

        $user = User::find($picture->user_id);
        // dd($user);
        $updated = $this->actingAs($user, 'api')->json('PUT', 'api/pictures/' . $picture->id, $data);

        $updated->assertStatus(200);
        $updated->assertJson(['success' => true]);
        $updated->assertJson(['message' => "Picture updated successfully."]);
        // delete user's folder and all subfolders.
        $delete_dir = File::deleteDirectory(public_path($user->id));
        // assert directory deleting is true
        $this->assertTrue($delete_dir);
    }

    /**
     * Test deleting the picture.
     *
     * @return void
     */
    public function testDeletePicture()
    {
        $data = [
            'title' => 'Uusi kuva',
            'image' => UploadedFile::fake()->image('random.jpg')
        ];
        factory(User::class, 1000)->create();
        $user = factory(User::class)->create();
        // makes new directories for the user and uploads the pictures in them.
        $response = $this->actingAs($user, 'api')->json('POST', '/api/pictures', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Picture stored successfully."]);
        $picture = $response->getData()->data;

        $deleted = $this->actingAs($user, 'api')->json('DELETE', '/api/pictures/' . $picture->id);

        $deleted->assertStatus(200);
        $deleted->assertJson(['message' => "Picture deleted!"]);
        // assert the pictures has deleted from folders.
        $this->assertFileNotExists($picture->image);
        $this->assertFileNotExists($picture->thumb);
        // delete user's folder and all subfolders.
        $delete_dir = File::deleteDirectory(public_path($user->id));
        // assert directory deleting is true
        $this->assertTrue($delete_dir);
    }
}
