<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Album;
use App\Category;
use App\Picture;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetUser()
    {
        $user = factory(User::class)->create();
        factory(Picture::class, 3)->create([
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user, 'api')->json('GET', '/api/users/' . $user->id);

        $response->assertStatus(200);
    }
}
