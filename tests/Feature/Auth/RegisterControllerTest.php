<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class RegisterControllerTest extends TestCase
{
/**
     * setup register tests.
     */
    public function setUp(): void
    {
        parent::setUp();
        //clear table
        // User::truncate();
        Artisan::call('passport:install');
    }

    public function testRequireEmailAndLogin()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserRegisterSuccessfully()
    {
        $user = [
            'name' => 'user',
            'email' => 'user@email.com',
            'password' => 'userpass',
        ];

        $response = $this->json('POST', 'api/register', $user);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}
