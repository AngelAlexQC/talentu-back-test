<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const NAME = 'Random User';
    const EMAIL = 'random@email.com';
    const PASSWORD = 'randompassword';

    public function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /**
     * Login before each test.
     * 
     * @return void
     */
    public function login()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'adminpass',
            'password' => Hash::make(self::PASSWORD),
        ]);

        $this->actingAs($user);
    }

    /**
     * Test GET /api/users
     * 
     * @return void
     */
    public function testGetUsers()
    {
        $users = User::factory()->count(5)->create();

        $response = $this->json('GET', route('api.users.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'dni',
                    'dni_type',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test GET /api/users/{id}
     * 
     * @return void
     */
    public function testGetUser()
    {
        $user = User::factory()->create();

        $response = $this->json('GET', route('api.users.show', $user));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'dni',
            'dni_type',
            'email',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * Test POST /api/users
     * 
     * @return void
     */
    public function testPostUser()
    {
        $response = $this->json('POST', route('api.users.store'), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'dni' => strval($this->faker->randomNumber(8)),
            'dni_type' => 'DNI',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'dni',
            'dni_type',
            'email',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'name' => self::NAME,
            'email' => self::EMAIL,
        ]);
    }

    /**
     * Test failed POST /api/users
     * 
     * @return void
     */
    public function testPostUserFailed()
    {
        $response = $this->json('POST', route('api.users.store'), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'dni' => strval($this->faker->randomNumber(8)),
            'dni_type' => 'DNI',
            'password' => self::PASSWORD,
            'password_confirmation' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'password',
            ],
        ]);
    }

    /**
     * Test PUT /api/users/{id}
     * 
     * @return void
     */
    public function testPutUser()
    {
        $user = User::factory()->create();

        $response = $this->json('PUT', route('api.users.update', $user), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'dni' => strval($this->faker->randomNumber(8)),
            'dni_type' => 'DNI',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'dni',
            'dni_type',
            'email',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'name' => self::NAME,
            'email' => self::EMAIL,
        ]);
    }

    /**
     * Test failed PUT /api/users/{id}
     * 
     * @return void
     */
    public function testPutUserFailed()
    {
        $user = User::factory()->create();

        $response = $this->json('PUT', route('api.users.update', $user), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'dni' => strval($this->faker->randomNumber(8)),
            'dni_type' => 'DNI',
            'password' => self::PASSWORD,
            'password_confirmation' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'password',
            ],
        ]);
    }

    /**
     * Test DELETE /api/users/{id}
     * 
     * @return void
     */
    public function testDeleteUser()
    {
        $user = User::factory()->create();

        $response = $this->json('DELETE', route('api.users.destroy', $user));

        $response->assertStatus(204);
    }

    /**
     * Test failed DELETE /api/users/{id}
     * 
     * @return void
     */
    public function testDeleteUserFailed()
    {
        $response = $this->json('DELETE', route('api.users.destroy', 0));

        $response->assertStatus(404);
    }
    
}
