<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
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

    /**
     * Test name, email and password are required.
     *
     * @return void
     */
    public function testRequireNameEmailPassword()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
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
        $user = [
            'name' => 'user',
            'email' => 'user@email.com',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
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
        $user = [
            'name' => 'user',
            'password' => 'userpass',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.']
                ]
            ]);
    }

    /**
     * Test user register success.
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

    /**
     * Test user register with short password.
     *
     * @return void
     */
    public function testWithShortPassword()
    {
        $user = [
            'name' => 'user',
            'email' => 'user@email.com',
            'password' => 'pass',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => ['The password must be at least 6 characters.']
                ]
            ]);
    }

    /**
     * Test user register with malformed email.
     *
     * @return void
     */
    public function testWithMalformedEmail()
    {
        $user = [
            'name' => 'user',
            'email' => 'user>@email.com',
            'password' => 'userpass',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email must be a valid email address.']
                ]
            ]);
    }

    /**
     * Test require unique email.
     *
     * @return void
     */
    public function testRequireUniqueEmail()
    {
        User::create([
            'name' => 'username',
            'email' => 'user@email.com',
            'password' => bcrypt('userpass'),
        ]);

        $user = [
            'name' => 'user',
            'email' => 'user@email.com',
            'password' => 'password',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email has already been taken.']
                ]
            ]);
    }

    /**
     * Test with too long string for name.
     *
     * @return void
     */
    public function testWithLongStringName()
    {
        $user = [
            'name' => 'eyyvztvjaroirefwichuytqzwnosmhbxwngaohbqnmsidrkcooqxtjxsszpvevxugnhisusugcnimkaxpjrdqmszwrfhkkhrfm',
            'email' => 'user@email.com',
            'password' => 'password',
        ];

        $this->json('POST', 'api/register', $user)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name may not be greater than 50 characters.']
                ]
            ]);
    }

}
