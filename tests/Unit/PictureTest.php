<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PictureTest extends TestCase
{
    /**
     * Test getting all pictures.
     *
     * @return void
     */
    public function testGettingAllPictures()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/pictures');

        $response->assertStatus(200);
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

    public function testCreatePicture()
    {
       $data = [
                        'title' => "Uusi kuva",
                        'content' => "Hieno kuva tulossa",
                        'image' => "https://images.pexels.com/photos/1000084/pexels-photo-1000084.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
                    ];
            $user = factory(\App\User::class)->create();
            $response = $this->actingAs($user, 'api')->json('POST', '/api/pictures',$data);
            $response->assertStatus(200);
            $response->assertJson(['status' => true]);
            $response->assertJson(['message' => "Picture stored successfully."]);
            $response->assertJson(['data' => $data]);
      }
}
