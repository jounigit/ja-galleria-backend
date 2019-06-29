<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Picture;

class PictureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * setup picturetest with 5 pictures.
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(Picture::class, 5)->create([
            'user_id' => 1
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
        $response->assertJsonCount(5);
        $response->assertJsonStructure(
            [
                [
                        'id',
                        'user_id',
                        'title',
                        'slug',
                        'content',
                        'image',
                        'created_at',
                        'updated_at',
                        'deleted_at'
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
        $picture = $response->getData()[0];

        $showPicture = $this->json('GET', 'api/pictures/' . $picture->id);

        $showPicture->assertStatus(200);
        // $getId = $showPicture->getData()->id;
        $showPicture->assertJson([
            'id' => $picture->id
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
            'content' => 'Hieno kuva tulossa',
            'image'=>'https://source.unsplash.com/random'
        ];
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/pictures',$data);

	    $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Picture stored successfully."]);
        $response->assertJson(['data' => $data]);
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
        $picture = $response->getData()[0];

       $data = [
            'title' => 'Päivitetty kuva',
            'content' => 'Hieno päivitys',
            'image'=>'https://source.unsplash.com/random'
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

        $picture = $response->getData()[0];

        $user = factory(\App\User::class)->create();
        $delete = $this->actingAs($user, 'api')->json('DELETE', '/api/pictures/' . $picture->id);
        // $delete->dump();
        $delete->assertStatus(200);
        $delete->assertJson(['message' => "Picture deleted!"]);

    }
}
