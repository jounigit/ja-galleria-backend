<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;

class UserControllerTest extends TestCase
{
    /**
     * setup usertest with 5 users.
     */
    public function setUp(): void
    {
        parent::setUp();
        factory(User::class, 5)->create();
    }

    /**
     * Test getting all Users.
     *
     * @return void
     */
    public function testGetAllUsers()
    {
        $user = User::all()->first();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/users');

        $response->assertStatus(200);
        $this->assertEquals(5, count($response->getData()->data));

        $response->assertJsonStructure(
            [
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ]
                ],
                'meta' => [
                    'user_count'
                ]
            ]
        );
    }

    /**
     * Test getting User.
     *
     * @return void
     */
    public function testGetUser()
    {
        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'categories' => [],
                'albums' => [],
                'pictures' => []
            ]
        ]);
    }
}
