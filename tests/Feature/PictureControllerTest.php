<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\Picture;
use App\User;
use File;

class PictureControllerTest extends TestCase
{

    private $userCreator;
    private $userNotCreator;

    /**
     * setup picturetest with 5 pictures.
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(User::class, 1000)->create();
        $this->userNotCreator = factory(User::class)->create();
        $user = factory(User::class)->create();
        $this->userCreator = $user;
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
            'image' => UploadedFile::fake()->image('random.jpg')
        ];

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
                'image',
                'thumb',
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
            'content' => 'Hieno päivitys'
        ];

        $updated = $this->actingAs($this->userCreator, 'api')->json('PUT', 'api/pictures/' . $picture->id, $data);

        // var_dump($updated);
        // dd($updated);

        $updated->assertStatus(200);
        $updated->assertJson(["success" => true]);
        $updated->assertJson(["message" => "Picture updated successfully."]);
        // delete user's folder and all subfolders.
        File::deleteDirectory(public_path($this->userCreator->id));
    }

    /**
     * Test update without permission.
     *
     * @return void
     */
    public function testUpdateWithOutPermission()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);
        $picture = $response->getData()->data[0];

        $data = [
            'title' => 'Uusi kuva',
            'image' => UploadedFile::fake()->image('random.jpg')
        ];

        $deleted = $this->actingAs($this->userNotCreator, 'api')->json('PUT', '/api/pictures/' . $picture->id);

        $deleted->assertStatus(403);
        $deleted->assertJson(['message' => "This action is unauthorized."]);
    }

    /**
     * Test deleting without permission.
     *
     * @return void
     */
    public function testDeleteWithOutPermission()
    {
        $response = $this->json('GET', '/api/pictures');
        $response->assertStatus(200);
        $picture = $response->getData()->data[0];

        $deleted = $this->actingAs($this->userNotCreator, 'api')->json('DELETE', '/api/pictures/' . $picture->id);

        $deleted->assertStatus(403);
        $deleted->assertJson(['message' => "This action is unauthorized."]);
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

        $deleted = $this->actingAs($this->userCreator, 'api')->json('DELETE', '/api/pictures/' . $picture->id);

        $deleted->assertStatus(200);
        $deleted->assertJson(['message' => "Picture deleted!"]);
        // assert the pictures has deleted from folders.
        $this->assertFileNotExists($picture->image);
        $this->assertFileNotExists($picture->thumb);
        // remove user's test folder and all subfolders if the is any.
        File::deleteDirectory(public_path($this->userCreator->id));
    }
}
