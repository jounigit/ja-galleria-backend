<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetUsers()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/users');
        $response->assertStatus(200);
    }
}
