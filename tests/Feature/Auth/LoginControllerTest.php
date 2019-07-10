<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\User;
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

    /**
     * Test email and password are required.
     *
     * @return void
     */
    public function testRequireEmailAndPassword()
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

    /**
     * Test email is required.
     *
     * @return void
     */
    public function testRequireEmail()
    {
        $user = ['password' => 'userpass'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.']
                ]
            ]);
    }

        /**
     * Test password is required.
     *
     * @return void
     */
    public function testRequirePassword()
    {
        $user = ['email' => 'user@email.com'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => ['The password field is required.']
                ]
            ]);
    }

          /**
     * Test with wrong password.
     *
     * @return void
     */
    public function testWrongPassword()
    {
        $user = ['email' => 'user@email.com', 'password' => 'wrongpass'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ]);
    }

         /**
     * Test with wrong email.
     *
     * @return void
     */
    public function testWrongEmail()
    {
        $user = ['email' => 'wrong@email.com', 'password' => 'userpass'];
        $this->json('POST', 'api/login', $user)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['These credentials do not match our records.']
                ]
            ]);
    }

    /**
     * Test login successfully.
     *
     * @return void
     */
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
