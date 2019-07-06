<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;

class UserCOntrollerTest extends TestCase
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
// dd($user);
        $response = $this->actingAs($user, 'api')->json('GET', '/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure(
            [
                [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at'
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
            'id' => $user->id
        ]);
    }
}
