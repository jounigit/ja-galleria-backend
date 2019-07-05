<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class LoginControllerTest extends TestCase
{
    /**
     * setup user tests.
     */
    public function setUp(): void
    {
        parent::setUp();
        //clear table
        // User::truncate();
        Artisan::call('passport:install');
        User::create([
            'name' => 'username',
            'email' => 'user@email.com',
            'password' => bcrypt('userpass'),
        ]);
    }


    public function testRequireEmailAndLogin()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);

    }

    public function testUserLoginSuccessfully()
    {
        $user = ['email' => 'user@email.com', 'password' => 'userpass'];

        $response = $this->json('POST', 'api/login', $user);
        // dd($response);
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

    public function testLogoutSuccessfully()
    {
        $user = [
            'email' => 'user@email.com',
            'password' => 'userpass'
        ];

        Auth::attempt($user);
        $token = Auth::user()->createToken('bigStore')->accessToken;
        $headers = ['Authorization' => "Bearer $token"];
        $response = $this->json('GET', 'api/logout', [], $headers);
        // dd($response);
        $response->assertStatus(204);
    }
}
